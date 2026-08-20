<?php

namespace App\Services\Ai\Tools;

use App\Models\Customer;

class CustomerHistoryTool extends BaseTool
{
    public function name(): string
    {
        return 'get_customer_purchase_history';
    }

    public function description(): string
    {
        return 'Retrieve details, total lifetime purchases, outstanding debt, and past transactions for a customer by name or ID.';
    }

    public function parameters(): array
    {
        return [
            'customer_id' => [
                'type' => 'integer',
                'description' => 'The ID of the customer (optional if name is given)',
            ],
            'query' => [
                'type' => 'string',
                'description' => 'Customer name or phone number search query (optional if customer_id is given)',
            ],
        ];
    }

    public function execute(array $args = []): array
    {
        $customerQuery = Customer::with(['sales' => function ($q) {
            $q->where('status', '!=', 'cancelled')->with('payments');
        }]);

        if (!empty($args['customer_id'])) {
            $customer = $customerQuery->find($args['customer_id']);
        } elseif (!empty($args['query'])) {
            $search = $args['query'];
            $customer = $customerQuery->where('name', 'LIKE', "%{$search}%")
                ->orWhere('phone', 'LIKE', "%{$search}%")
                ->first();
        } else {
            return ['error' => 'Either customer_id or query parameter is required.'];
        }

        if (!$customer) {
            return ['error' => 'Customer not found.'];
        }

        $totalPurchases = (float) $customer->sales->sum('total');
        $totalPaid = (float) $customer->sales->flatMap->payments->sum('amount');
        $remainingDebt = $totalPurchases - $totalPaid;

        return [
            'customer_id' => $customer->id,
            'name' => $customer->name,
            'phone' => $customer->phone,
            'email' => $customer->email,
            'total_sales_count' => $customer->sales->count(),
            'total_purchases_tnd' => round($totalPurchases, 3),
            'total_paid_tnd' => round($totalPaid, 3),
            'remaining_debt_tnd' => round($remainingDebt, 3),
            'recent_orders' => $customer->sales->take(5)->map(fn ($sale) => [
                'sale_id' => $sale->id,
                'date' => $sale->sale_date,
                'total' => (float) $sale->total,
                'paid' => (float) $sale->payments->sum('amount'),
                'remaining' => round((float) ($sale->total - $sale->payments->sum('amount')), 3),
                'status' => $sale->status,
            ])->toArray(),
        ];
    }
}