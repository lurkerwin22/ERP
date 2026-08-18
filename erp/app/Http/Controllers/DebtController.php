<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;

class DebtController extends Controller
{
    public function index(Request $request)
    {
        // Query non-cancelled sales with their loaded relationships and payments
        $sales = Sale::where('status', '!=', 'cancelled')
            ->with(['customer', 'payments'])
            ->latest()
            ->get()
            // Filter only sales with an outstanding remaining balance
            ->filter(fn (Sale $sale) => $sale->remaining_amount > 0);

        // Calculate aggregate global totals for the KPIs
        $totalDebt = $sales->sum(fn (Sale $sale) => $sale->remaining_amount);
        $totalSalesWithDebt = $sales->count();

        return view('debts.index', compact('sales', 'totalDebt', 'totalSalesWithDebt'));
    }
}