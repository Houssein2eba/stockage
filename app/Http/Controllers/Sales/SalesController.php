<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Http\Resources\ClearStockResource;
use App\Http\Resources\ClientResource;
use App\Http\Resources\OrderResource;

use App\Http\Resources\ProductResource;
use App\Http\Requests\OrderRequest;
use App\Http\Resources\StockResource;
use App\Models\Client;
use App\Models\Order;
use App\Models\OrderDetail;

use App\Models\Product;
use App\Models\ProductStock;
use App\Models\Stock;
use App\Observers\ProductStockObserver;
use App\Services\ProductMonitorService;
use App\Services\StockMonitorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Log;

class SalesController extends Controller
{
    public function __construct(public ProductMonitorService $productMonitorService, public StockMonitorService $stockMonitorService){
        $this->productMonitorService = $productMonitorService;
        $this->stockMonitorService = $stockMonitorService;

    }
    public function index(Request $request)
{
    $request->validate([
        'search' => 'nullable|string',
        'status' => 'nullable|string|in:paid,pending,cancelled',
        'page' => 'nullable|integer',
        'date' => 'nullable|date',
        'direction' => 'nullable|in:asc,desc',
        'sort' => 'nullable|in:id,reference,client_id,order_total_amount,status,created_at'
    ]);

    $orders = Order::with(['client', 'products'])
        ->withSum('products as total_amount', 'order_details.total_amount')

        ->when($request->search, function ($query, $search) {
            $query->where('reference', 'LIKE', "%{$search}%")
                ->orWhereHas('client', function ($query) use ($search) {
                    $query->where('name', 'LIKE', "%{$search}%");
                });
        })

        ->when($request->status, function ($query, $status) {
            $query->where('status', $status);
        })

        ->when($request->date, function ($query, $date) {
            $query->whereDate('created_at', date('Y-m-d', strtotime($date)));
        })

        // Sorting
        ->when($request->sort === 'order_total_amount' || $request->sort === 'total_amount', function ($query) use ($request) {
            $query->orderBy('total_amount', $request->direction ?? 'asc');
        })

        ->when(
            $request->filled('sort') && !in_array($request->sort, ['order_total_amount', 'total_amount']),
            function ($query) use ($request) {
                $query->orderBy($request->sort, $request->direction ?? 'asc');
            }
        )

        // Always order by latest if no specific sort
        ->orderBy('created_at', 'desc');
        if($request->wantsJson()){
            return response()->json(["orders" => OrderResource::collection($orders->get())]);
        }

        $orders = $orders->paginate(PAGINATION)
        ->withQueryString();

    // Statistiques utiles
    $stats = [
        'totalRevenue' => DB::table('order_details')->sum('total_amount'),
        'totalSales' => Order::count(),
        'todaySales' => Order::whereDate('created_at', today())->count(),
        'pendingPayments' => Order::where('status', 'pending')->count()
    ];

    return inertia('Sales/Index', [
        'sales' => OrderResource::collection($orders),
        'stats' => $stats,
        'filters' => $request->only(['search', 'status', 'date', 'sort', 'direction'])
    ]);
}


    public function show($id)
    {
        $order = Order::with(['client', 'products'])->findOrFail($id);

        return inertia('Sales/Show', [
            'sale' => new OrderResource($order)
        ]);
    }

    public function create(Request $request)
    {
        $clients = Client::all();
        $stocks=Stock::with('products')->get();



        return inertia('Sales/Create', [
            'clients' => ClientResource::collection($clients),
            'stocks' => ClearStockResource::collection($stocks),
        ]);
    }
    public function store(OrderRequest $request)
    {
        $data = $request->validated();

        try {
            $orderId = DB::transaction(function () use ($data) {

                // Create the main order
                $order = Order::create([
                    'reference' => Order::generateReference(),
                    'client_id' => $data['client']['id'] ?? null,
                    'stock_id' => $data['stock']['id'] ?? null,
                    'status' => $data['paid'] ? 'paid' : 'pending',
                    'order_total_amount' => $data['order_total_amount']
                ]);


                $orderTotal = 0;
                $productData = [];

                // Validate products exist
                if (empty($data['products'])) {
                    throw new \Exception('Aucun produit sélectionné');
                }

                foreach ($data['products'] as $productItem) {
                    // Lock product for update to prevent concurrent modifications
                    $product = Product::where('id', $productItem['product']['id'])
                                    ->lockForUpdate()
                                    ->first();

                    if (!$product) {
                        throw new \Exception('Produit introuvable avec ID ' . $productItem['product']['id']);
                    }

                    // Check stock availability
                    if ($product->quantity < $productItem['quantity']) {
                        throw new \Exception("Stock insuffisant pour le produit : {$product->name}");
                    }

                    // Calculate amounts
                    $totalAmount = $productItem['total_amount'] ?? ($product->price * $productItem['quantity']);
                    $orderTotal += $totalAmount;

                    // Prepare pivot data for many-to-many relationship
                    $productData[$product->id] = [
                        'quantity' => $productItem['quantity'],
                        'total_amount' => $totalAmount,

                    ];

                    // Update product stock quantity
                    $product->decrement('quantity', $productItem['quantity']);

                    // Update stock movement record
                    if (isset($data['stock']['id'])) {
                        $product->stocks()->updateExistingPivot($data['stock']['id'], [
                            'products_quantity' => DB::raw('products_quantity - ' . $productItem['quantity']),
                            'type' => 'out',
                            'stock_date' => now(),
                            'updated_at' => now(),
                        ]);
                    }

                    
                }

                // Attach products to order with pivot data
                $order->products()->attach($productData);

                // Update order total amount
                $order->update([
                    'total_amount' => $orderTotal
                ]);

                

                return $order->id;
            });

            Log::info('Transaction réussie', ['order_id' => $orderId]);

            return to_route('sales.show', $orderId);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la création de la commande', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $data
            ]);

            return back()->with('error', $e->getMessage());
        }
    }



    public function edit($id)
    {
        $order = Order::with(['client', 'products'])->findOrFail($id);
        $clients = Client::all();
        $products = Product::where('quantity', '>', 0)
            ->orWhereIn('id', $order->products->pluck('id'))
            ->get();


        return inertia('Sales/Edit', [
            'sale' => new OrderResource($order),
            'clients' => ClientResource::collection($clients),
            'products' => ProductResource::collection($products),

        ]);
    }

    public function update(OrderRequest $request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $order = Order::where('id', $id)->lockForUpdate()->first();
            $old = $order->toArray();
            $order->load('products');

            // Calculate total amount
            $totalAmount = collect($request->items)->sum(function ($item) {
                $product = Product::find($item['product']['id']);
                return $product->price * $item['quantity'];
            });

            // Restore previous quantities
            foreach ($order->products as $product) {
                $product->increment('quantity', $product->pivot->quantity);
            }

            // Update order main info
            $order->update([
                'client_id' => $request->client ? $request->client['id'] : null,

                'total_amount' => $totalAmount,
                'status' => $request->status ?? ($request->paid ? 'paid' : 'pending')
            ]);

            // Sync new products with order details
            $orderDetails = collect($request->items)->mapWithKeys(function ($item) {
                return [
                    $item['product']['id'] => [
                        'id' => Str::uuid(),
                        'quantity' => $item['quantity'],
                        'total_amount' => Product::find($item['product']['id'])->price * $item['quantity']
                    ]
                ];
            })->all();

            $order->products()->sync($orderDetails);

            // Update new quantities
            foreach ($request->items as $item) {
                $product = Product::find($item['product']['id']);
                if ($product->quantity < $item['quantity']) {
                    throw new \Exception("Insufficient stock for product: {$product->name}");
                }
                $product->decrement('quantity', $item['quantity']);
            }

            $order->refresh();


            return back();
        });
    }

public function destroy($id)
{
    DB::transaction(function () use ($id) {
        $order = Order::with('products')->findOrFail($id);
        $old = $order->toArray();

        // Restituer les quantités des produits
        foreach ($order->products as $product) {
            $product->increment('quantity', $product->pivot->quantity);

            // Si vous utilisez également la gestion des stocks, mettez à jour le stock
            if (isset($product->pivot->stock_id)) {
                $product->stocks()->updateExistingPivot($product->pivot->stock_id, [
                    'products_quantity' => DB::raw('products_quantity + ' . $product->pivot->quantity),
                    'type' => 'in', // Marquer comme entrée de stock
                    'stock_date' => now(),
                ]);
            }
        }

        $order->update(['status' => 'cancelled']);

        unset($old['id'], $old['created_at'], $old['updated_at']);

        // activity()
        //     ->causedBy(auth()->user())
        //     ->performedOn($order)
        //     ->withProperties([
        //         'old' => $old,
        //         'attributes' => $order->toArray(),
        //         'restored_quantities' => $order->products->map(function($product) {
        //             return [
        //                 'product_id' => $product->id,
        //                 'quantity_restored' => $product->pivot->quantity
        //             ];
        //         })
        //     ])
        //     ->log('Annuler une vente et restituer les quantités');
    });

    return back();
}
    public function markAsPaid(Request $request,Order $id)
    {
         $order=Order::findOrFail($id->id);
        $order->update(['status' => 'paid','updated_at' => now()]);

         if($request->wantsJson()){
            return response()->json([
                'order' => new OrderResource($order),
                'message' => 'Order Marked as Paid',
                'status' => 200
            ]);
         }


        return back();
    }
}
