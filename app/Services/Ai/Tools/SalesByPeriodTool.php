<?php

namespace App\Services\Ai\Tools;

use App\Models\Sale;
use Carbon\Carbon;

class SalesByPeriodTool extends BaseTool
{
    public function name(): string
    {
        return 'get_sales_by_period';
    }

    public function description(): string
    {
        return 'Get total sales revenue, order count, and average order value for a date range (or past N days).';
    }

    public function parameters(): array
    {
        return [
            'days' => [
                'type' => 'integer',
                'description' => 'Number of past days to analyze (default: 30)',
            ],
            'start_date' => [
                'type' => 'string',
                'description' => 'Start date in YYYY-MM-DD format (optional)',
            ],
            'end_date' => [
                'type' => 'string',
                'description' => 'End date in YYYY-MM-DD format (optional)',
            ],
        ];
    }

    public function execute(array $args = []): array
    {
        $query = Sale::where('status', '!=', 'cancelled');

        if (!empty($args['start_date']) && !empty($args['end_date'])) {
            $startDate = Carbon::parse($args['start_date'])->startOfDay();
            $endDate = Carbon::parse($args['end_date'])->endOfDay();
            $query->whereBetween('sale_date', [$startDate, $endDate]);
            $periodLabel = "From {$args['start_date']} to {$args['end_date']}";
        } else {
            $days = $args['days'] ?? 30;
            $startDate = now()->subDays($days)->startOfDay();
            $query->where('sale_date', '>=', $startDate);
            $periodLabel = "Past {$days} days";
        }

        $sales = $query->get(['id', 'total', 'sale_date', 'status', 'customer_name']);
        $totalRevenue = (float) $sales->sum('total');
        $totalOrders = $sales->count();
        $avgOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        return [
            'period' => $periodLabel,
            'total_revenue_tnd' => round($totalRevenue, 3),
            'total_orders' => $totalOrders,
            'average_order_value_tnd' => round($avgOrderValue, 3),
            'sales' => $sales->take(20)->map(fn ($s) => [
                'sale_id' => $s->id,
                'customer_name' => $s->customer_name ?? 'Guest',
                'total_amount' => (float) $s->total,
                'date' => $s->sale_date ? Carbon::parse($s->sale_date)->format('Y-m-d') : null,
                'status' => $s->status,
            ])->toArray(),
        ];
    }
}