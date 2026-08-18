<?php
namespace App\Services\Ai\Tools;

use App\Models\Sale;
use Carbon\Carbon;

class SalesAnalysisTool
{
    public static function definition(): array
    {
        return [
            'type' => 'function',
            'function' => [
                'name' => 'get_sales_summary',
                'description' => 'Get revenue and transaction metrics for a given period (today, this_week, this_month).',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'period' => [
                            'type' => 'string',
                            'enum' => ['today', 'this_week', 'this_month'],
                            'description' => 'Time period to analyze'
                        ]
                    ],
                    'required' => ['period']
                ]
            ]
        ];
    }

    public function execute(array $args): array
    {
        $period = $args['period'] ?? 'today';
        $query = Sale::query();

        match ($period) {
            'today' => $query->whereDate('created_at', Carbon::today()),
            'this_week' => $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]),
            'this_month' => $query->whereMonth('created_at', Carbon::now()->month)->whereYear('created_at', Carbon::now()->year),
            default => $query->whereDate('created_at', Carbon::today()),
        };

        $totalSalesCount = $query->count();
        $totalRevenue = (float) $query->sum('total');

        return [
            'period' => $period,
            'total_sales_count' => $totalSalesCount,
            'total_revenue_tnd' => number_format($totalRevenue, 2, '.', ''),
            'average_sale_value_tnd' => $totalSalesCount > 0 ? number_format($totalRevenue / $totalSalesCount, 2, '.', '') : 0,
        ];
    }
}