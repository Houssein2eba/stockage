<?php

namespace App\Http\Controllers;

use App\Http\Resources\productResource;
use App\Models\Client;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\Order;
use App\Models\Category;
use App\Models\Payment;
use App\Models\Stock;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use App\Http\Resources\OrderResource;
use Maatwebsite\Excel\Concerns\ToArray;

class DashboardController extends Controller
{
    public function index(Request $request)
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
            'lowStockCount' =>Stock::withCount('lows')->count(),
            'totalSales' => Order::where('status', '!=','cancelled')->count(),
            'totalCategories' => Category::count(),
            'totalRevenue' => $totalAmount = DB::table('order_details')
                            ->join('orders', 'order_details.order_id', '=', 'orders.id')
                            ->where('orders.status', '!=', 'cancelled')
                            ->sum('order_details.total_amount'),
            'totalProfit' => DB::table('order_details')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->where('orders.status', '!=', 'cancelled')
                ->join('products', 'products.id', '=', 'order_details.product_id')
                ->sum(DB::raw('(products.price - products.cost) * order_details.quantity')),
            'todaySales' => Order::where('status', '!=','cancelled')->whereDate('created_at', Carbon::today())->count(),
            'todayRevenue' => DB::table('order_details')
                ->join('orders', 'order_details.order_id', '=', 'orders.id')
                ->where('orders.status', '!=', 'cancelled')
                ->whereDate('orders.created_at', Carbon::today())
                ->sum('order_details.total_amount'),
            'paidAmount' =>DB::table('orders')
                ->where('status', 'paid')
                ->join('order_details', 'orders.id', '=', 'order_details.order_id')
                ->sum('order_details.total_amount'),
            'popular_products' => $popular,
            'stocksCount' =>Stock::count(),


        ];


        $stats['dueAmount']=$stats['totalRevenue']-$stats['paidAmount'];

$stats['clientCount'] = Client::count();
        $recentSales = OrderResource::collection(Order::with('client')
            ->latest()
            ->take(5)
            ->get());

            if($request->wantsJson()){

            return response()->json($stats);
          }

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
