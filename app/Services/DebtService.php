<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\Customer;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class DebtService
{
    /**
     * Get all outstanding sales.
     */
    public function getOutstandingSalesCollection(): Collection
    {
        return Sale::query()
            ->where('status', '!=', 'cancelled')
            ->with(['customer', 'payments'])
            ->latest()
            ->get()
            ->filter(fn (Sale $sale) => $sale->remaining_balance > 0)
            ->values();
    }

    /**
     * Paginate outstanding sales for the view.
     */
    public function getPaginatedOutstandingSales(int $perPage = 15): LengthAwarePaginator
    {
        $all = $this->getOutstandingSalesCollection();
        $page = LengthAwarePaginator::resolveCurrentPage();

        return new LengthAwarePaginator(
            $all->forPage($page, $perPage)->values(),
            $all->count(),
            $perPage,
            $page,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );
    }

    /**
     * Get total outstanding debt across all sales.
     */
    public function getTotalDebt(): float
    {
        return round(
            $this->getOutstandingSalesCollection()
                ->sum(fn (Sale $sale) => $sale->remaining_balance),
            2
        );
    }

    /**
     * Get count of sales with outstanding debt.
     */
    public function getOutstandingSalesCount(): int
    {
        return $this->getOutstandingSalesCollection()->count();
    }
    /**
     * Get customers with outstanding debts exceeding the minimum threshold.
     */
    public function getCustomerDebts(float $minDebt = 0): Collection
    {
        return Customer::query()
            ->withSum(['sales' => fn ($q) => $q->where('payment_status', '!=', 'paid')], 'total')
            ->withSum('payments', 'amount')
            ->get()
            ->map(function ($customer) {
                $totalSales = $customer->sales_sum_total ?? 0;
                $totalPayments = $customer->payments_sum_amount ?? 0;
                $outstandingDebt = max(0, $totalSales - $totalPayments);

                return [
                    'customer_id' => $customer->id,
                    'customer_name' => $customer->name,
                    'phone' => $customer->phone,
                    'outstanding_debt' => $outstandingDebt,
                ];
            })
            ->filter(fn ($item) => $item['outstanding_debt'] > $minDebt)
            ->values();
    }
}