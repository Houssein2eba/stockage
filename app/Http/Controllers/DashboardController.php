<?php

namespace App\Http\Controllers;

use App\Http\Resources\productResource;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\Order;
use App\Models\Category;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use App\Http\Resources\OrderResource;

class DashboardController extends Controller
{
    public function index()
    {
        // Get popular products (top 5 most sold)
        $popular = Product::query()
    ->select([
        'products.id',
        'products.name',
        'products.price',
        'products.description',
        DB::raw('COUNT(DISTINCT order_details.order_id) as orders_count'),
        DB::raw('SUM(order_details.quantity) as items_sold'),
        DB::raw('SUM(order_details.total_amount) as total_revenue'),
    ])
    ->leftJoin('order_details', 'products.id', '=', 'order_details.product_id')
    ->leftJoin('orders', 'order_details.order_id', '=', 'orders.id')
    ->groupBy('products.id')
    ->whereHas('orders')
    ->orderByDesc('items_sold')
    ->take(5)
    ->get();



        // Return as collection using ProductResource



        $stats = [
            'totalProducts' => Product::count(),
            'lowStockCount' => Product::whereRaw('quantity <= min_quantity')->count(),
            'totalSales' => Order::count(),
            'totalCategories' => Category::count(),
            'totalRevenue' => OrderDetail::sum('total_amount'),
            'totalProfit' => Order::sum('total_amount') * 0.2, // Assuming 20% profit margin
            'todaySales' => OrderDetail::whereDate('created_at', Carbon::today())->count(),
            'todayRevenue' => OrderDetail::whereDate('created_at', Carbon::today())->sum('total_amount'),
            'paidAmount' =>DB::table('orders')
                ->where('status', 'paid')
                ->join('order_details', 'orders.id', '=', 'order_details.order_id')
                ->sum('order_details.total_amount'),
            'popular_products' => $popular,

        ];

        $stats['dueAmount']=$stats['totalRevenue']-$stats['paidAmount'];

        $recentSales = OrderResource::collection(Order::with('client')
            ->latest()
            ->take(5)
            ->get());

        // Monthly sales data for chart using SQLite date functions
        $chartData = OrderDetail::selectRaw("strftime('%Y-%m', created_at) as month, COUNT(*) as count, SUM(total_amount) as total")
            ->where('created_at', '>=', Carbon::now()->subYear())
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'recentSales' => $recentSales,
            'chartData' => $chartData
        ]);
    }
}
