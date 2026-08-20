<?php

namespace App\Services;

use App\Models\Sale;
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
}