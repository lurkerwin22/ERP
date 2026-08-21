<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Ai\Tools\StockTool;
use App\Services\Ai\Tools\SalesByPeriodTool;
use App\Services\Ai\Tools\DebtTool;
use Illuminate\Http\JsonResponse;

class ReportController extends Controller
{
    public function stockAlerts(StockTool $tool): JsonResponse
    {
        $result = $tool->execute([]);

        $products = $result['products'] ?? [];

        return response()->json([
            'type' => 'stock',
            'title' => '🚨 Alertes Stock & Ruptures',
            'data' => [
                'items' => $products,
            ],
        ]);
    }

    public function monthlySales(SalesByPeriodTool $tool): JsonResponse
    {
        $data = $tool->execute([
            'start_date' => now()->startOfMonth()->format('Y-m-d'),
            'end_date' => now()->endOfMonth()->format('Y-m-d'),
        ]);

        return response()->json([
            'type' => 'sales',
            'title' => '📊 Résumé des Ventes du Mois',
            'data' => [
                'total_sales' => $data['total_revenue_tnd'] ?? 0,
                'count' => $data['total_orders'] ?? 0,
                'average_sale_value' => $data['average_order_value_tnd'] ?? 0,
            ],
        ]);
    }

    public function customerDebts(DebtTool $tool): JsonResponse
    {
        $data = $tool->execute([
            'min_amount' => 0,
        ]);

        $debtors = $data['debtors'] ?? [];

        return response()->json([
            'type' => 'debts',
            'title' => '💰 Créances Clients',
            'data' => [
                'debts' => array_map(function ($debtor) {
                    return [
                        'customer_name' => $debtor['name'] ?? 'Client inconnu',
                        'balance' => $debtor['outstanding_debt'] ?? 0,
                        'total_purchases' => $debtor['total_purchases'] ?? 0,
                        'total_paid' => $debtor['total_paid'] ?? 0,
                    ];
                }, $debtors),
            ],
        ]);
    }
}