<?php
namespace App\Services\Ai\Tools;

use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ProductPerformanceTool
{
    public function name(): string
    {
        return 'get_product_performance';
    }

    public function description(): string
    {
        return 'Get a list of top-selling products, including total quantities sold and total revenue generated. Can be filtered by date.';
    }

    public function parameters(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'limit' => [
                    'type' => 'integer',
                    'description' => 'The number of top products to return (default: 5)'
                ],
                'start_date' => [
                    'type' => 'string',
                    'description' => 'Start date for the performance period in YYYY-MM-DD format (optional)'
                ],
                'end_date' => [
                    'type' => 'string',
                    'description' => 'End date for the performance period in YYYY-MM-DD format (optional)'
                ]
            ]
        ];
    }

    public function execute(array $args): string
    {
        $limit = $args['limit'] ?? 5;
        
        $query = Product::query()
            ->select(
                'products.id',
                'products.name',
                DB::raw('SUM(sale_items.quantity) as total_quantity_sold'),
                // Adjust 'total' below if your sale_items schema uses 'subtotal' or 'price' * 'quantity'
                DB::raw('SUM(sale_items.total) as total_revenue') 
            )
            ->join('sale_items', 'products.id', '=', 'sale_items.product_id')
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_revenue');

        if (!empty($args['start_date'])) {
            $query->whereDate('sales.created_at', '>=', $args['start_date']);
        }

        if (!empty($args['end_date'])) {
            $query->whereDate('sales.created_at', '<=', $args['end_date']);
        }

        $results = $query->limit($limit)->get();

        return json_encode([
            'status' => 'success',
            'timeframe' => [
                'start' => $args['start_date'] ?? 'all-time',
                'end' => $args['end_date'] ?? 'all-time',
            ],
            'data' => $results
        ]);
    }
}