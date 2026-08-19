<?php

namespace App\Services;

use App\Models\Sale;
use Illuminate\Support\Collection;

class DebtService
{
    /**
     * Get all outstanding sales.
     *
     * Each sale's remaining balance is calculated from:
     *
     * sale.total - sum(payments.amount)
     */
    public function getOutstandingSales(): Collection
    {
        return Sale::query()
            ->where('status', '!=', 'cancelled')
            ->with([
                'customer',
                'payments',
            ])
            ->latest()
            ->get()
            ->filter(fn (Sale $sale) => $sale->remaining_balance > 0)
            ->values();
    }

    /**
     * Get total outstanding debt across all sales.
     */
    public function getTotalDebt(): float
    {
        return round(
            $this->getOutstandingSales()
                ->sum(fn (Sale $sale) => $sale->remaining_balance),
            2
        );
    }

    /**
     * Get number of sales with outstanding debt.
     */
    public function getOutstandingSalesCount(): int
    {
        return $this->getOutstandingSales()->count();
    }

    /**
     * Get outstanding debt grouped by customer.
     *
     * Multiple unpaid sales belonging to the same customer
     * are combined into one customer-level debt.
     */
    public function getCustomerDebts(float $minDebt = 0): array
    {
        $sales = $this->getOutstandingSales();

        $customers = [];

        foreach ($sales as $sale) {
            $remaining = $sale->remaining_balance;

            if ($sale->customer_id) {
                $key = 'customer_' . $sale->customer_id;

                $name = $sale->customer?->name
                    ?? $sale->customer_name
                    ?? 'Unknown Customer';

                $phone = $sale->customer?->phone
                    ?? $sale->customer_phone
                    ?? 'N/A';

                $customerId = $sale->customer_id;
            } else {
                $name = $sale->customer_name ?: 'Walk-in Customer';

                $key = 'walkin_' . $name;

                $phone = $sale->customer_phone ?? 'N/A';
                $customerId = null;
            }

            if (!isset($customers[$key])) {
                $customers[$key] = [
                    'customer_id' => $customerId,
                    'name' => $name,
                    'phone' => $phone,
                    'debt_amount_tnd' => 0.00,
                    'outstanding_sales_count' => 0,
                ];
            }

            $customers[$key]['debt_amount_tnd'] += $remaining;
            $customers[$key]['outstanding_sales_count']++;
        }

        return collect($customers)
            ->map(function (array $customer) {
                $customer['debt_amount_tnd'] = round(
                    $customer['debt_amount_tnd'],
                    2
                );

                return $customer;
            })
            ->filter(
                fn (array $customer) =>
                    $customer['debt_amount_tnd'] >= $minDebt
            )
            ->sortByDesc('debt_amount_tnd')
            ->values()
            ->toArray();
    }
}