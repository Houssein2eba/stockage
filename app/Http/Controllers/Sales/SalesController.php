<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Http\Resources\ClientResource;
use App\Http\Resources\PaymentResource;
use App\Http\Resources\ProductResource;
use App\Models\Client;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SalesController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::with(['client', 'products', 'payment'])
            ->latest()
            ->paginate(PAGINATION);
        
        // Calculate statistics
        $stats = [
            'totalRevenue' => Order::sum('total_amount'),
            'totalSales' => Order::count(),
            'todaySales' => Order::whereDate('created_at', today())->count(),
            'pendingPayments' => Order::whereNull('payment_id')->count()
        ];

        return inertia('Sales/Index', [
            'sales' => $orders,
            'stats' => $stats,
            'filters' => request()->only(['search', 'status', 'date'])
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

    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'nullable|exists:clients,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'payment_id' => 'nullable|exists:payments,id',
            'notes' => 'nullable|string'
        ]);

        DB::transaction(function () use ($request) {
            $totalAmount = 0;
            
            // Calculate total amount first
            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                $totalAmount += $product->price * $item['quantity'];
            }

            // Create the order
            $order = Order::create([
                'reference' => 'ORD-' . Str::random(10),
                'client_id' => $request->client_id,
                'payment_id' => $request->payment_id,
                'total_amount' => $totalAmount,
                'status' => $request->payment_id ? 'paid' : 'pending'
            ]);

            // Add order details
            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                
                if ($product->quantity < $item['quantity']) {
                    throw new \Exception("Insufficient stock for product: {$product->name}");
                }

                // Create order detail
                $order->products()->attach($product->id, [
                    'id' => Str::uuid(),
                    'quantity' => $item['quantity'],
                    'total_amount' => $product->price * $item['quantity']
                ]);

                // Update product quantity
                $product->decrement('quantity', $item['quantity']);
            }
        });

        return back()->with('success', 'Sale recorded successfully');
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
            'sale' => $order,
            'clients' => ClientResource::collection($clients),
            'products' => ProductResource::collection($products),
            'payments' => PaymentResource::collection($payments)
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'client.id' => 'nullable|exists:clients,id',
            'payment.id' => 'nullable|exists:payments,id',
            'items' => 'required|array|min:1',
            'items.*.product.id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($request, $id) {
            $order = Order::findOrFail($id);
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
                'status' => $request->payment && $request->payment['id'] ? 'paid' : 'pending'
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
        });

        return back()->with('success', 'Sale updated successfully');
    }
}
