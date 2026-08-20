<?php

namespace App\Services\Ai\Tools;

use App\Models\SaleItem;
use Illuminate\Support\Facades\DB;

class ProductPerformanceTool extends BaseTool
{
    public function name(): string
    {
        return 'get_top_products';
    }

    public function description(): string
    {
        return 'Retrieve top-selling products ranked by revenue generated or quantity sold.';
    }

    public function parameters(): array
    {
        return [
            'limit' => [
                'type' => 'integer',
                'description' => 'Number of top products to return (default: 5)',
            ],
            'days' => [
                'type' => 'integer',
                'description' => 'Filter sales from the past N days (optional)',
            ],
        ];
    }

    public function execute(array $args = []): array
    {
        $limit = $args['limit'] ?? 5;
        $query = SaleItem::query()
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->where('sales.status', '!=', 'cancelled');

        if (!empty($args['days'])) {
            $query->where('sales.sale_date', '>=', now()->subDays($args['days']));
        }

        $topProducts = $query->select(
            'sale_items.product_id',
            'sale_items.product_name',
            DB::raw('SUM(sale_items.quantity) as total_quantity_sold'),
            DB::raw('SUM(sale_items.subtotal) as total_revenue_generated')
        )
        ->groupBy('sale_items.product_id', 'sale_items.product_name')
        ->orderByDesc('total_revenue_generated')
        ->limit($limit)
        ->get();

        return [
            'count' => $topProducts->count(),
            'period_analyzed' => !empty($args['days']) ? "Past {$args['days']} days" : 'All time',
            'products' => $topProducts->map(fn ($p) => [
                'product_id' => $p->product_id,
                'product_name' => $p->product_name,
                'total_quantity_sold' => (int) $p->total_quantity_sold,
                'total_revenue_tnd' => round((float) $p->total_revenue_generated, 3),
            ])->toArray(),
        ];
    }
}