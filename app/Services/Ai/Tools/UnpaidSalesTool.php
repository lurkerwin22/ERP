<?php

namespace App\Services\Ai\Tools;

use App\Models\Sale;

class UnpaidSalesTool extends BaseTool
{
    public function name(): string
    {
        return 'get_unpaid_sales';
    }

    public function description(): string
    {
        return 'Retrieve sales invoices that are unpaid or partially paid, including remaining balance per invoice.';
    }

    public function parameters(): array
    {
        return [
            'limit' => [
                'type' => 'integer',
                'description' => 'Maximum number of unpaid invoices to return (default: 10)',
            ],
        ];
    }

    public function execute(array $args = []): array
    {
        $limit = $args['limit'] ?? 10;

        $unpaidSales = Sale::where('status', '!=', 'cancelled')
            ->with(['payments', 'customer'])
            ->get()
            ->map(function ($sale) {
                $paid = $sale->payments->sum('amount');
                $remaining = $sale->total - $paid;

                return [
                    'sale_id' => $sale->id,
                    'sale_date' => $sale->sale_date,
                    'customer_name' => $sale->customer_name ?? $sale->customer?->name ?? 'Guest',
                    'customer_phone' => $sale->customer_phone ?? $sale->customer?->phone,
                    'total_amount_tnd' => round((float) $sale->total, 3),
                    'paid_amount_tnd' => round((float) $paid, 3),
                    'remaining_balance_tnd' => round((float) $remaining, 3),
                ];
            })
            ->filter(fn ($s) => $s['remaining_balance_tnd'] > 0)
            ->sortByDesc('remaining_balance_tnd')
            ->take($limit)
            ->values();

        return [
            'total_unpaid_invoices_found' => $unpaidSales->count(),
            'total_unpaid_amount_tnd' => round($unpaidSales->sum('remaining_balance_tnd'), 3),
            'invoices' => $unpaidSales->toArray(),
        ];
    }
}