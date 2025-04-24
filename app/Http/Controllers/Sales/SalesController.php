<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Http\Resources\ClientResource;
use App\Http\Resources\OrderResource;
use App\Http\Resources\PaymentResource;
use App\Http\Resources\ProductResource;
use App\Http\Requests\OrderRequest;
use App\Models\Client;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Payment;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SalesController extends Controller
{
    public function index(Request $request)
    {
        
        $request->validate([
            'search' => 'nullable|string',
            'status' => 'nullable|string',
            'page' => 'nullable|integer',
            'date' => 'nullable|date',
            'direction' => 'nullable|in:asc,desc',
            'sort' => 'nullable|in:id,reference,client,total_amount,status,created_at'
        ]);
        $orders = Order::with(['client', 'products', 'payment'])
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
        $created_at=date('Y-m-d', strtotime($date));
        $query->whereDate('created_at', $created_at);
    })
    ->when($request->sort === 'total_amount', function ($query) use ($request) {
        $query->orderBy('total_amount', $request->direction ?? 'asc');
    }, function ($query) use ($request) {
        if ($request->sort === 'date') {
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
            'pendingPayments' => Order::whereNull('payment_id')->count()
        ];

        
        return inertia('Sales/Index', [
            'sales' => OrderResource::collection($orders),
            'stats' => $stats,
            'filters' => request()->only(['search', 'status', 'date'])
        ]);
    }

    public function show($id){
        $order = Order::with(['client', 'products', 'payment'])->findOrFail($id);
        
        return inertia('Sales/Show', [
            'sale' => new OrderResource($order)
        ]);
    }

    public function create(){
        $clients = Client::all();
        $products = Product::where('quantity', '>', 0)->get();
        $payments = Payment::all();

        return inertia('Sales/Create', [
            'clients' => ClientResource::collection($clients),
            'products' => ProductResource::collection($products),
            'payments' => PaymentResource::collection($payments)
        ]);
    }

    public function store(OrderRequest $request)
    {
        DB::transaction(function () use ($request) {
            // Create the order
            $order = Order::create([
                'reference' => Order::generateReference(),
                'client_id' => $request->client_id,
                'payment_id' => $request->payment_id,
                'status' => $request->payment_id ? 'paid' : 'pending'
            ]);

            // Add order details
            foreach ($request->items as $item) {
               $productData[$item['product_id']]=[
                'quantity'=>$item['quantity'],
                'total_amount'=>$item['quantity']*Product::find($item['product_id'])->price
               ];
            }
            $order->products()->attach($productData);
            // Update product quantities
            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                if ($product->quantity < $item['quantity']) {
                    throw new \Exception("Insufficient stock for product: {$product->name}");
                }
                $product->decrement('quantity', $item['quantity']);
            }
            $attributes = $order->toArray();
            unset($attributes['id'], $attributes['created_at'], $attributes['updated_at']);
            activity()
                ->causedBy(auth()->user())
                ->performedOn($order)
                ->withProperties(['attributes' => $attributes])
                ->log('Order Created');
        });

        return back();
    }

    public function edit($id)
    {
        $order = Order::with(['client', 'products', 'payment'])->findOrFail($id);
        $clients = Client::all();
        $products = Product::where('quantity', '>', 0)
            ->orWhereIn('id', $order->products->pluck('id'))
            ->get();
        $payments = Payment::all();

        return inertia('Sales/Edit', [
            'sale' => new OrderResource($order),
            'clients' => ClientResource::collection($clients),
            'products' => ProductResource::collection($products),
            'payments' => PaymentResource::collection($payments)
        ]);
    }

    public function update(OrderRequest $request, $id)
    {
        DB::transaction(function () use ($request, $id) {
            $order = Order::findOrFail($id);
            $old = $order->toArray();
            $order->load('products');

            // Calculate total amount
            $totalAmount = collect($request->items)->sum(function ($item) {
                $product = Product::find($item['product']['id']);
                return $product->price * $item['quantity'];
            });

            // Get current products to restore quantities
            $currentProducts = $order->products;

            // Restore previous quantities
            foreach ($currentProducts as $product) {
                $product->increment('quantity', $product->pivot->quantity);
            }

            // Update order main info
            $order->update([
                'client_id' => $request->client ? $request->client['id'] : null,
                'payment_id' => $request->payment ? $request->payment['id'] : null,
                'total_amount' => $totalAmount,
                'status' => $request->status ?? ($request->payment && $request->payment['id'] ? 'paid' : 'pending')
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
        });

        return back();
    }

    public function destroy($id)
    {
        DB::transaction(function () use ($id) {
            $order = Order::findOrFail($id);
            $old = $order->toArray();
            $order->delete();
            unset($old['id'], $old['created_at'], $old['updated_at']);
            activity()
                ->causedBy(auth()->user())
                ->performedOn($order)
                ->withProperties(['old' => $old])
                ->log('Order Deleted');
        });

        return redirect()->back();
    }
}
