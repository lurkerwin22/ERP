<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\LigneVente;
use App\Models\Products;
use App\Models\Vente;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. High-Level Summary Aggregates
        $totalSalesCount = Vente::count();
        $totalRevenue = Vente::sum('total');
        $totalCustomers = Client::count();
        $totalProducts = Products::count();

        // 2. Stock Health Metrics (Single Raw Query for Performance)
        $stockStats = Products::selectRaw("
            COUNT(*) as total_items,
            SUM(stock) as total_units,
            SUM(CASE WHEN stock <= 0 THEN 1 ELSE 0 END) as out_of_stock,
            SUM(CASE WHEN stock > 0 AND stock <= seuil_alerte THEN 1 ELSE 0 END) as low_stock,
            SUM(CASE WHEN stock > seuil_alerte THEN 1 ELSE 0 END) as normal_stock
        ")->first();

        // 3. Time-Based Sales Statistics
        $today = Carbon::today();
        $todayStats = Vente::whereDate('created_at', $today)
            ->selectRaw('COUNT(*) as count, COALESCE(SUM(total), 0) as revenue')
            ->first();

        $monthStats = Vente::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->selectRaw('COALESCE(SUM(total), 0) as revenue')
            ->first();

        // 4. Last 7 Days Sales Trend (Graph Data)
        $last7Days = collect(range(6, 0))->map(function ($daysAgo) {
            $date = Carbon::now()->subDays($daysAgo);
            $daySales = Vente::whereDate('created_at', $date->toDateString())->sum('total');

            return [
                'day' => $date->format('D'),
                'date' => $date->format('d/m'),
                'revenue' => (float) $daySales,
            ];
        });

        // 5. Recent Activity (Last 5 Sales)
        // ✅ Load relationship optionally, but query includes client_nom snapshot
        $recentSales = Vente::with('client')
            ->latest()
            ->take(5)
            ->get();

        // 6. Top Selling Products (Aggregated from LigneVente)
        // ✅ Group by snapshot `nom_produit` instead of `product_id` so deleted products keep their name and sales history
        $topProducts = LigneVente::select(
                'nom_produit', 
                DB::raw('SUM(quantite) as total_sold'), 
                DB::raw('SUM(sous_total) as total_revenue')
            )
            ->groupBy('nom_produit')
            ->orderByDesc('total_sold')
            ->take(5)
            ->get();

        // 7. Low / Out-of-Stock Alert Table
        // ✅ Fixed column name from 'quantity' to 'stock'
        $criticalStockProducts = Products::where('stock', '<=', DB::raw('seuil_alerte'))
            ->orderBy('stock', 'asc')
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'totalSalesCount',
            'totalRevenue',
            'totalCustomers',
            'totalProducts',
            'stockStats',
            'todayStats',
            'monthStats',
            'last7Days',
            'recentSales',
            'topProducts',
            'criticalStockProducts'
        ));
    }
}