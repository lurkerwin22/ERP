<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Ai\Tools\StockTool;
use App\Services\Ai\Tools\SalesTool;
use App\Services\Ai\Tools\CustomerDebtTool;
use Illuminate\Http\JsonResponse;

class ReportController extends Controller
{
    public function stockAlerts(StockTool $tool): JsonResponse
    {
        $data = $tool->execute([]);
        
        return response()->json([
            'type' => 'stock',
            'title' => ' Alertes Stock & Ruptures',
            'data' => $data,
        ]);
    }

    public function monthlySales(SalesTool $tool): JsonResponse
    {
        $data = $tool->execute(['period' => 'this_month']);

        return response()->json([
            'type' => 'sales',
            'title' => '📊 Résumé des Ventes du Mois',
            'data' => $data,
        ]);
    }

    public function customerDebts(CustomerDebtTool $tool): JsonResponse
    {
        $data = $tool->execute(['min_debt' => 0]);

        return response()->json([
            'type' => 'debts',
            'title' => ' Créances Clients',
            'data' => $data,
        ]);
    }
}