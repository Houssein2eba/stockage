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

class SalesController extends Controller
{
    public function __construct(public ProductMonitorService $productMonitorService, public StockMonitorService $stockMonitorService){
        $this->productMonitorService = $productMonitorService;
        $this->stockMonitorService = $stockMonitorService;

    }
    public function index(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'search' => 'nullable|string',
            'status' => 'nullable|string|in:paid,pending',
            'page' => 'nullable|integer',
            'date' => 'nullable|date',
            'direction' => 'nullable|in:asc,desc',
            'sort' => 'nullable|in:id,reference,client,total_amount,status,created_at'
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
                $created_at = date('Y-m-d', strtotime($date));
                $query->whereDate('created_at', $created_at);
            })
            ->when($request->sort === 'total_amount', function ($query) use ($request) {
                $query->orderBy('total_amount', $request->direction ?? 'asc');
            }, function ($query) use ($request) {
                if ($request->sort === 'date') {
                    $query->orderBy($request->sort, $request->direction ?? 'asc');
                }elseif ($request->sort === 'reference') {
                    $query->orderBy($request->sort, $request->direction ?? 'asc');
                } else {
                    $query->orderBy($request->sort, $request->direction ?? 'asc');
                }
            })
            ->orderBy('created_at', 'desc')
            ->paginate(PAGINATION)
            ->withQueryString();

        // Calculate statistics
        $stats = [
            'totalRevenue' => OrderDetail::sum('total_amount'),
            'totalSales' => Order::count(),
            'todaySales' => Order::whereDate('created_at', today())->count(),
            'pendingPayments' => Order::where('status', 'pending')->count()
        ];

        return inertia('Sales/Index', [
            'sales' => OrderResource::collection($orders),
            'stats' => $stats,
            'filters' => $request->only(['search', 'status', 'date'])
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
            // 1. Create the order
            $order = Order::create([
                'reference' => Order::generateReference(),
                'client_id' => $data['client']['id'] ?? null,
                'status' => $data['paid'] ? 'paid' : 'pending',
            ]);

            $orderTotal = 0;
            $productData = [];
            foreach($data['products'] as $products){

                $product=Product::findOrFail($products['product']['id']);
                if ($product->quantity < $products['quantity']) {
                    return back()->with('error', "Insufficient stock for product: {$product->name}");
                }
                // 2. Calculate total amount for each product
                $totalAmount = $product->price * $products['quantity'];
                $orderTotal += $totalAmount;
                // 3. Prepare product data for pivot table
                $productData[$product->id] = [
                    'quantity' => $products['quantity'],
                    'total_amount' => $totalAmount
                ];

                // 4. Update product quantity
                $product->decrement('quantity', $products['quantity']);
                // 5 stock pivot

                $product->stocks()->updateExistingPivot($data['stock']['id'], [
                    'quantity' => $products['quantity'],
                    'stock_out_date' => now(),
                    'stock_in_date' => null, // Only if you want to explicitly clear it
                ]);


            }


            // 6. Attach products to order
            $order->products()->attach($productData);

            // 7. Log
            activity()
                ->causedBy(auth()->user())
                ->performedOn($order)
                ->withProperties([
                    'attributes' => $order->only(['reference', 'client_id', 'status', 'total_amount']),
                ])
                ->log('Order Created');

            return $order->id;
        });

        return to_route('sales.show', $orderId);

    } catch (\Exception $e) {
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
            $order = Order::findOrFail($id);
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
            $attributes = $order->toArray();
            unset($old['id'], $old['created_at'], $old['updated_at']);
            unset($attributes['id'], $attributes['created_at'], $attributes['updated_at']);

            activity()
                ->causedBy(auth()->user())
                ->performedOn($order)
                ->withProperties(['old' => $old, 'attributes' => $attributes])
                ->log('Order Updated');

            return back();
        });
    }

    public function destroy($id)
    {
        DB::transaction(function () use ($id) {
            $order = Order::findOrFail($id);
            $old = $order->toArray();
            $order->update(['status' => 'cancelled']);


            unset($old['id'], $old['created_at'], $old['updated_at']);

            activity()
                ->causedBy(auth()->user())
                ->performedOn($order)
                ->withProperties(['old' => $old,'attributes' => $order->toArray()])
                ->log('Order Cancelled');
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
        activity()
            ->causedBy(auth()->user())
            ->performedOn($order)
            ->log('Order Marked as Paid');

        return back();
    }
}
