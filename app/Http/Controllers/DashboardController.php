<?php

namespace App\Http\Controllers;

use App\Http\Resources\productResource;
use App\Models\Client;
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
        $popular = Product::withSum('orders as total_quantity', 'order_details.quantity')
        ->withSum('orders as total_amount', 'order_details.total_amount')
        ->where('total_quantity', '>', 0)
            ->orderByDesc('total_quantity')
            ->take(5)
            ->get();
        // Return as collection using ProductResource



        $stats = [
            'totalProducts' => Product::count(),
            'lowStockCount' => Product::whereRaw('quantity <= min_quantity')->count(),
            'totalSales' => Order::count(),
            'totalCategories' => Category::count(),
            'totalRevenue' => OrderDetail::sum('total_amount'),
            'totalProfit' => DB::table('order_details')
                ->join('products', 'products.id', '=', 'order_details.product_id')
                ->sum(DB::raw('(products.price - products.cost) * order_details.quantity')),
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
