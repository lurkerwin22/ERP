<?php

namespace App\Services\Ai\Tools;

use App\Models\Sale;
use App\Models\Payment;
use App\Models\Customer;

class SalesTool extends BaseTool
{
    public function name(): string
    {
        return 'get_business_summary';
    }

    public function description(): string
    {
        return 'Get high-level macro financial metrics: total revenue invoiced, cash collected, current month invoiced, and active customer count.';
    }

    public function parameters(): array
    {
        return [];
    }

    public function execute(array $args = []): array
    {
        $totalInvoiced = Sale::where('status', '!=', 'cancelled')->sum('total') ?? 0;
        $totalCollected = Payment::sum('amount') ?? 0;
        $currentMonthInvoiced = Sale::where('status', '!=', 'cancelled')
            ->whereYear('sale_date', now()->year)
            ->whereMonth('sale_date', now()->month)
            ->sum('total') ?? 0;

        return [
            'total_invoiced_all_time' => round((float) $totalInvoiced, 3),
            'total_collected_all_time' => round((float) $totalCollected, 3),
            'total_outstanding_debt' => round((float) ($totalInvoiced - $totalCollected), 3),
            'current_month_invoiced' => round((float) $currentMonthInvoiced, 3),
            'active_customers_count' => Customer::has('sales')->count(),
        ];
    }
}