<?php

namespace App\Services\Ai\Tools;

use App\Services\DebtService;

class CustomerDebtTool
{
    public static function definition(): array
    {
        return [
            'type' => 'function',
            'function' => [
                'name' => 'get_customer_debts',
                'description' => 'Get customers with actual outstanding debts calculated from non-cancelled sales minus all recorded payments. Includes walk-in customers.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'min_debt' => [
                            'type' => 'number',
                            'description' => 'Minimum outstanding debt in TND. Default is 0.',
                        ],
                    ],
                    'required' => [],
                ],
            ],
        ];
    }

    public function execute(array $args): array
    {
        $minDebt = (float) ($args['min_debt'] ?? 0);

        return app(DebtService::class)
            ->getCustomerDebts($minDebt)
            ->toArray();
    }
}