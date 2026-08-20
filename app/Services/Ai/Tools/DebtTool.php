<?php

namespace App\Services\Ai\Tools;

use App\Models\Customer;

class DebtTool extends BaseTool
{
    public function name(): string
    {
        return 'get_overdue_debtors';
    }

    public function description(): string
    {
        return 'Retrieve customers with unpaid balances or outstanding debts in TND.';
    }

    public function parameters(): array
    {
        return [
            'min_amount' => [
                'type' => 'number',
                'description' => 'Filter debtors owing at least this amount in TND (optional)',
            ],
            'max_amount' => [
                'type' => 'number',
                'description' => 'Filter debtors owing at most this amount in TND (optional)',
            ],
        ];
    }

    public function execute(array $args = []): array
    {
        $minAmount = $args['min_amount'] ?? 0;
        $maxAmount = $args['max_amount'] ?? null;

        $debtors = Customer::with(['sales' => function ($query) {
            $query->where('status', '!=', 'cancelled')->with('payments');
        }])->get()->map(function ($customer) {
            $totalSales = $customer->sales->sum('total');
            $totalPaid = $customer->sales->flatMap->payments->sum('amount');
            $debt = $totalSales - $totalPaid;

            return [
                'id' => $customer->id,
                'name' => $customer->name,
                'email' => $customer->email,
                'phone' => $customer->phone,
                'total_purchases' => round((float) $totalSales, 3),
                'total_paid' => round((float) $totalPaid, 3),
                'outstanding_debt' => round((float) $debt, 3),
            ];
        })->filter(function ($customer) use ($minAmount, $maxAmount) {
            if ($customer['outstanding_debt'] <= $minAmount) {
                return false;
            }
            if ($maxAmount !== null && $customer['outstanding_debt'] > $maxAmount) {
                return false;
            }
            return true;
        })->values();

        return [
            'total_debtors_count' => $debtors->count(),
            'total_outstanding_sum' => round($debtors->sum('outstanding_debt'), 3),
            'debtors' => $debtors->toArray(),
        ];
    }
}