<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\Sale;
use App\Services\Ai\AiAssistantService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct(
        protected AiAssistantService $aiService
    ) {}
    public function index()
    {
        // 1. High-Level Summary Aggregates
        $totalSalesCount = Sale::count();
        $totalRevenue = Sale::sum('total');
        $totalCustomers = Customer::count();
        $totalProduct = Product::count();

        // 2. Stock Health Metrics (Single Raw Query for Performance)
        $stockStats = Product::selectRaw("
            COUNT(*) as total_items,
            SUM(stock) as total_units,
            SUM(CASE WHEN stock <= 0 THEN 1 ELSE 0 END) as out_of_stock,
            SUM(CASE WHEN stock > 0 AND stock <= alert_threshold THEN 1 ELSE 0 END) as low_stock,
            SUM(CASE WHEN stock > alert_threshold THEN 1 ELSE 0 END) as normal_stock
        ")->first();

        // 3. Time-Based Sales Statistics
        $today = Carbon::today();
        $todayStats = Sale::whereDate('created_at', $today)
            ->selectRaw('COUNT(*) as count, COALESCE(SUM(total), 0) as revenue')
            ->first();

        $monthStats = Sale::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->selectRaw('COALESCE(SUM(total), 0) as revenue')
            ->first();

        // 4. Last 7 Days Sales Trend (Graph Data)
        $last7Days = collect(range(6, 0))->map(function ($daysAgo) {
            $date = Carbon::now()->subDays($daysAgo);
            $daySales = Sale::whereDate('created_at', $date->toDateString())->sum('total');

            return [
                'day' => $date->format('D'),
                'date' => $date->format('d/m'),
                'revenue' => (float) $daySales,
            ];
        });

        // 5. Recent Activity (Last 5 Sales)
        // ✅ Load relationship optionally, but query includes customer_name snapshot
        $recentSales = Sale::with('customer')
            ->latest()
            ->take(5)
            ->get();

        // 6. Top Selling Product (Aggregated from SaleItem)
        // ✅ Group by snapshot `product_name` instead of `product_id` so deleted products keep their name and sales history
        $topProduct = SaleItem::select(
                'product_name', 
                DB::raw('SUM(quantity) as total_sold'), 
                DB::raw('SUM(subtotal) as total_revenue')
            )
            ->groupBy('product_name')
            ->orderByDesc('total_sold')
            ->take(5)
            ->get();

        // 7. Low / Out-of-Stock Alert Table
        // ✅ Fixed column name from 'quantity' to 'stock'
        $criticalStockProduct = Product::where('stock', '<=', DB::raw('alert_threshold'))
            ->orderBy('stock', 'asc')
            ->take(5)
            ->get();


        // 3. Access it via $this->aiService
        $aiAlerts = $this->aiService->getProactiveAlerts();

        return view('dashboard', compact(
            'totalSalesCount',
            'totalRevenue',
            'totalCustomers',
            'totalProduct',
            'stockStats',
            'todayStats',
            'monthStats',
            'last7Days',
            'recentSales',
            'topProduct',
            'criticalStockProduct',
            'aiAlerts'
        ));
    }
}