<?php
namespace App\Services\Ai\Tools;

use App\Models\Product;

class StockAnalysisTool
{
    /**
     * Tool definition sent to OpenAI API (JSON Schema)
     */
    public static function definition(): array
    {
        return [
            'type' => 'function',
            'function' => [
                'name' => 'get_low_stock_products',
                'description' => 'Retrieves products where physical stock is equal to or below their alert threshold, or completely out of stock.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'limit' => [
                            'type' => 'integer',
                            'description' => 'Maximum number of low-stock products to return (default 10)'
                        ]
                    ],
                    'required' => []
                ]
            ]
        ];
    }

    /**
     * Execute Eloquent Query
     */
    public function execute(array $args): array
    {
        $limit = $args['limit'] ?? 10;

        return Product::query()
            ->whereColumn('stock', '<=', 'alert_threshold')
            ->select('id', 'name', 'stock', 'alert_threshold', 'price')
            ->orderBy('stock', 'asc')
            ->take($limit)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'current_stock' => $product->stock,
                    'alert_threshold' => $product->alert_threshold,
                    'unit_price_tnd' => (float) $product->price,
                    'status' => $product->stock <= 0 ? 'OUT_OF_STOCK' : 'LOW_STOCK',
                ];
            })
            ->toArray();
    }
}