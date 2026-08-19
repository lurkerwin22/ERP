<?php
namespace App\Services\Ai\Tools;

use App\Models\Sale;
use App\Models\Payment;
use App\Models\Customer;
use Carbon\Carbon;

class BusinessSummaryTool
{
    public function name(): string
    {
        return 'get_business_summary';
    }

    public function description(): string
    {
        return 'Get a high-level macro financial snapshot of the business, including total revenue invoiced, total cash collected, and total outstanding debt.';
    }

    public function parameters(): array
    {
        return [
            'type' => 'object',
            'properties' => [] // No parameters needed for a global macro summary
        ];
    }

    public function execute(array $args): string
    {
        // Adjust column names ('total_amount', 'amount') based on your exact schema
        $totalInvoicedAllTime = Sale::sum('total_amount') ?? 0;
        $totalCollectedAllTime = Payment::sum('amount') ?? 0;
        
        // Calculate outstanding debt (Invoiced minus Collected)
        $totalOutstandingDebt = $totalInvoicedAllTime - $totalCollectedAllTime;

        // Optionally, grab performance for the current month for a more relevant summary
        $currentMonthInvoiced = Sale::whereMonth('created_at', Carbon::now()->month)
                                    ->whereYear('created_at', Carbon::now()->year)
                                    ->sum('total_amount') ?? 0;

        $activeCustomersCount = Customer::has('sales')->count();

        return json_encode([
            'status' => 'success',
            'metrics' => [
                'total_invoiced_all_time' => round($totalInvoicedAllTime, 3),
                'total_collected_all_time' => round($totalCollectedAllTime, 3),
                'total_outstanding_debt' => round($totalOutstandingDebt, 3),
                'current_month_invoiced' => round($currentMonthInvoiced, 3),
                'active_customers_count' => $activeCustomersCount
            ]
        ]);
    }
}