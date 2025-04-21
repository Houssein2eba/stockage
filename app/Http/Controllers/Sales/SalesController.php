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
    public function index()
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
}
