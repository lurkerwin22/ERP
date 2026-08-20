<?php

namespace App\Http\Controllers;

use App\Services\DebtService;
use Illuminate\Http\Request;

class DebtController extends Controller
{
    public function index(Request $request, DebtService $debtService)
    {
        $sales = $debtService->getPaginatedOutstandingSales(15);
        $totalDebt = $debtService->getTotalDebt();
        $totalSalesWithDebt = $debtService->getOutstandingSalesCount();

        return view('debts.index', compact(
            'sales',
            'totalDebt',
            'totalSalesWithDebt'
        ));
    }
}