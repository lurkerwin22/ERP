<?php

namespace App\Services\Ai\Tools;

use App\Models\Customer;

class CustomerDebtTool
{
    public static function definition(): array
    {
        return [
            'type' => 'function',
            'function' => [
                'name' => 'get_customer_debts',
                'description' => 'Get a list of customers who owe money or have outstanding unpaid debts.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'min_debt' => [
                            'type' => 'number',
                            'description' => 'Minimum debt threshold in TND (default 0)'
                        ]
                    ],
                    'required' => []
                ]
            ]
        ];
    }

    public function execute(array $args): array
    {
        $minDebt = $args['min_debt'] ?? 0;

        // Note: Replace 'outstanding_balance' with your actual debt column name if different (e.g., 'balance', 'debt')
        return Customer::query()
            ->where('outstanding_balance', '>', $minDebt)
            ->select('id', 'name', 'phone', 'outstanding_balance')
            ->orderBy('outstanding_balance', 'desc')
            ->get()
            ->map(function ($customer) {
                return [
                    'customer_id' => $customer->id,
                    'name' => $customer->name,
                    'phone' => $customer->phone ?? 'N/A',
                    'debt_amount_tnd' => (float) $customer->outstanding_balance,
                ];
            })
            ->toArray();
    }
}