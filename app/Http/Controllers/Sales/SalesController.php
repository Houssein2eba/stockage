<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Http\Resources\ClearStockResource;
use App\Http\Resources\ClientResource;
use App\Http\Resources\OrderResource;

use App\Http\Resources\ProductResource;
use App\Http\Requests\OrderRequest;
use App\Http\Resources\SettingResource;
use App\Http\Resources\StockResource;
use App\Models\Client;
use App\Models\Order;
use App\Models\OrderDetail;

use App\Models\Product;
use App\Models\ProductStock;
use App\Models\Setting;
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
        ->when(in_array($request->sort, ['order_total_amount', 'total_amount']), function ($query) use ($request) {
            $query->orderBy('order_total_amount', $request->direction ?? 'asc');
        })
        ->when(
            $request->filled('sort') && !in_array($request->sort, ['order_total_amount', 'total_amount']),
            function ($query) use ($request) {
                $query->orderBy($request->sort, $request->direction ?? 'asc');
            }
        )
        // Always order by latest if no specific sort
        ->orderBy('created_at', 'desc');


        $orders = $orders->paginate(PAGINATION)
        ->withQueryString();
        if($request->wantsJson()){
            return response()->json([
            "orders" => OrderResource::collection($orders),
            "meta" => [
                "current_page" => $orders->currentPage(),
                "last_page" => $orders->lastPage(),
                "per_page" => $orders->perPage(),
                "total" => $orders->total(),
            ]
    ]);
        }

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
            'sale' => new OrderResource($order),
            'setting'=> new SettingResource(Setting::first()),
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

                $productIds = collect($data['products'])->pluck('product.id')->toArray();
                $products = Product::whereIn('id', $productIds)->lockForUpdate()->get()->keyBy('id');

                // Validate products exist and check stock availability
                foreach ($data['products'] as $productItem) {
                    $productId = $productItem['product']['id'];
                    $quantity = $productItem['quantity'];

                    if (!isset($products[$productId])) {
                        throw new \Exception('Produit introuvable avec ID ' . $productId);
                    }

                    $product = $products[$productId];

                    if ($product->quantity < $quantity) {
                        throw new \Exception('Quantité insuffisante pour ' . $product->name . '. Stock disponible: ' . $product->quantity);
                    }

                    // Calculate amounts
                    $totalAmount = $productItem['total_amount'] ?? ($product->price * $quantity);
                    $orderTotal += $totalAmount;

                    // Prepare pivot data for many-to-many relationship
                    $productData[$productId] = [
                        'quantity' => $quantity,
                        'total_amount' => $totalAmount,
                    ];

                    // Prepare for bulk update of product quantities
                    $product->quantity -= $quantity;
                }

                // Bulk update product quantities
                $cases = [];
                $ids = [];
                foreach ($products as $product) {
                    $cases[] = "WHEN '{$product->id}' THEN {$product->quantity}";
                    $ids[] = "'{$product->id}'";
                }

                if (!empty($cases)) {
                    $idsString = implode(',', $ids);
                    $casesString = implode(' ', $cases);

                    DB::statement("UPDATE products SET quantity = CASE id {$casesString} END WHERE id IN ({$idsString})");
                }

                // Attach products to order with pivot data
                $order->products()->attach($productData);

                // Update stock movement record (bulk update if possible, otherwise keep loop)
                if (isset($data['stock']['id'])) {
                    $stockId = $data['stock']['id'];
                    $productStockUpdates = [];

                    foreach ($data['products'] as $productItem) {
                        $productId = $productItem['product']['id'];
                        $quantity = $productItem['quantity'];

                        $productStockUpdates[] = [
                            'product_id' => $productId,
                            'stock_id' => $stockId,
                            'products_quantity' => DB::raw('products_quantity - ' . $quantity),
                            'type' => 'out',
                            'stock_date' => now(),
                            'updated_at' => now(),
                        ];
                    }

                    // Perform bulk update for product_stocks pivot table
                    foreach ($productStockUpdates as $updateData) {
                        DB::table('product_stocks')
                            ->where('product_id', $updateData['product_id'])
                            ->where('stock_id', $updateData['stock_id'])
                            ->update([
                                'products_quantity' => $updateData['products_quantity'],
                                'type' => $updateData['type'],
                                'stock_date' => $updateData['stock_date'],
                                'updated_at' => $updateData['updated_at'],
                            ]);
                    }
                }

                // Update order total amount (if needed, otherwise remove)
                $order->update([
                    'order_total_amount' => $orderTotal
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

            $productIds = collect($request->items)->pluck('product.id')->toArray();
            $products = Product::whereIn('id', $productIds)->lockForUpdate()->get()->keyBy('id');

            $oldProductQuantities = [];
            foreach ($order->products as $product) {
                $oldProductQuantities[$product->id] = $product->pivot->quantity;
            }

            $totalAmount = 0;
            $newProductQuantities = [];
            $orderDetails = [];

            foreach ($request->items as $item) {
                $productId = $item['product']['id'];
                $quantity = $item['quantity'];

                if (!isset($products[$productId])) {
                    throw new \Exception('Produit introuvable avec ID ' . $productId);
                }

                $product = $products[$productId];

                // Calculate total amount
                $itemTotalAmount = $item['total_amount'] ?? ($product->price * $quantity);
                $totalAmount += $itemTotalAmount;

                // Prepare order details for sync
                $orderDetails[$productId] = [
                    'quantity' => $quantity,
                    'total_amount' => $itemTotalAmount,
                ];

                // Calculate new quantity for bulk update
                $oldQuantity = $oldProductQuantities[$productId] ?? 0;
                $newProductQuantities[$productId] = ($product->quantity + $oldQuantity) - $quantity;

                if ($newProductQuantities[$productId] < 0) {
                    throw new \Exception("Quantité insuffisante pour " . $product->name);
                }
            }

            // Bulk update product quantities
            $cases = [];
            $ids = [];
            foreach ($newProductQuantities as $id => $qty) {
                $cases[] = "WHEN '{$id}' THEN {$qty}";
                $ids[] = "'{$id}'";
            }

            if (!empty($cases)) {
                $idsString = implode(',', $ids);
                $casesString = implode(' ', $cases);
                DB::statement("UPDATE products SET quantity = CASE id {$casesString} END WHERE id IN ({$idsString})");
            }

            // Update order main info
            $order->update([
                'client_id' => $request->client ? $request->client['id'] : null,
                'order_total_amount' => $totalAmount,
                'status' => $request->status ?? ($request->paid ? 'paid' : 'pending')
            ]);

            // Sync new products with order details
            $order->products()->sync($orderDetails);

            // Update stock movement record (if applicable)
            if (isset($order->stock_id)) {
                $stockId = $order->stock_id;
                foreach ($request->items as $item) {
                    $productId = $item['product']['id'];
                    $quantity = $item['quantity'];
                    $oldQuantity = $oldProductQuantities[$productId] ?? 0;

                    $quantityDifference = $quantity - $oldQuantity;

                    if ($quantityDifference !== 0) {
                        DB::table('product_stocks')
                            ->where('product_id', $productId)
                            ->where('stock_id', $stockId)
                            ->update([
                                'products_quantity' => DB::raw('products_quantity - ' . $quantityDifference),
                                'type' => $quantityDifference > 0 ? 'out' : 'in',
                                'stock_date' => now(),
                                'updated_at' => now(),
                            ]);
                    }
                }
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

        $productQuantitiesToRestore = [];
        $productStockUpdates = [];

        foreach ($order->products as $product) {
            $productQuantitiesToRestore[$product->id] = ($productQuantitiesToRestore[$product->id] ?? 0) + $product->pivot->quantity;

            if (isset($order->stock_id)) {
                $productStockUpdates[$product->id] = (
                    $productStockUpdates[$product->id] ?? 0
                ) + $product->pivot->quantity;
            }
        }

        // Bulk update product quantities
        $cases = [];
        $ids = [];
        foreach ($productQuantitiesToRestore as $productId => $quantity) {
            $cases[] = "WHEN '{$productId}' THEN quantity + {$quantity}";
            $ids[] = "'{$productId}'";
        }

        if (!empty($cases)) {
            $idsString = implode(',', $ids);
            $casesString = implode(' ', $cases);
            DB::statement("UPDATE products SET quantity = CASE id {$casesString} END WHERE id IN ({$idsString})");
        }

        // Bulk update product_stocks pivot table
        if (isset($order->stock_id) && !empty($productStockUpdates)) {
            $stockId = $order->stock_id;
            $cases = [];
            $ids = [];
            foreach ($productStockUpdates as $productId => $quantity) {
                $cases[] = "WHEN '{$productId}' THEN products_quantity + {$quantity}";
                $ids[] = "'{$productId}'";
            }

            if (!empty($cases)) {
                $idsString = implode(',', $ids);
                $casesString = implode(' ', $cases);
                DB::statement("UPDATE product_stocks SET products_quantity = CASE product_id {$casesString} END, type = 'in', stock_date = NOW(), updated_at = NOW() WHERE product_id IN ({$idsString}) AND stock_id = '{$stockId}'");
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
