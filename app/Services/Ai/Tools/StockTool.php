<?php

namespace App\Services\Ai\Tools;

use App\Models\Product;

class StockTool extends BaseTool
{
    public function name(): string
    {
        return 'get_low_stock_products';
    }

    public function description(): string
    {
        return 'Retrieve products where stock quantity is at or below the safety threshold.';
    }

    public function parameters(): array
    {
        return [
            'threshold' => [
                'type' => 'integer',
                'description' => 'Minimum stock threshold limit (default: 10)',
            ],
        ];
    }

    public function execute(array $args = []): array
    {
        $threshold = $args['threshold'] ?? 10;

        $products = Product::whereColumn('stock', '<=', 'alert_threshold')
            ->orWhere('stock', '<=', $threshold)
            ->get(['id', 'name', 'stock', 'alert_threshold', 'price']);

        return [
            'count' => $products->count(),
            'threshold_used' => $threshold,
            'products' => $products->map(fn ($p) => [
                'id' => $p->id,
                'name' => $p->name,
                'current_stock' => $p->stock,
                'alert_threshold' => $p->alert_threshold,
                'unit_price' => (float) $p->price,
            ])->toArray(),
        ];
    }
}