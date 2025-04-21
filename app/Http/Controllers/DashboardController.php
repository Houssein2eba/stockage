<?php

namespace App\Http\Controllers;

use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\Order;
use App\Models\Category;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'totalProducts' => Product::count(),
            'lowStockCount' => Product::whereRaw('quantity <= min_quantity')->count(),
            'totalSales' => Order::count(),
            'totalCategories' => Category::count(),
            'totalRevenue' => Order::sum('total_amount'),
            'totalProfit' => Order::sum('total_amount') * 0.2, // Assuming 20% profit margin
            'todaySales' => Order::whereDate('created_at', Carbon::today())->count(),
            'todayRevenue' => Order::whereDate('created_at', Carbon::today())->sum('total_amount'),
            'paidAmount' => Payment::where('status', 'paid')->sum('amount'),
            'dueAmount' => Order::sum('total_amount') - Payment::where('status', 'paid')->sum('amount'),
        ];

        $recentSales = Order::with('client')
            ->latest()
            ->take(5)
            ->get()
            ->map(function($sale) {
                return [
                    'id' => $sale->id,
                    'invoice_number' => $sale->reference,
                    'customer_name' => $sale->client ? $sale->client->name : 'No Client',
                    'created_at' => $sale->created_at,
                    'amount' => $sale->total_amount,
                    'status' => $sale->payment_status
                ];
            });

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