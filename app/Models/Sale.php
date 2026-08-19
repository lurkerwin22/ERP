<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = [
        'customer_id',
        'customer_name',
        'customer_phone',
        'customer_address',
        'sale_date',
        'total',
        'status',
        'notes',
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'sale_date' => 'datetime',
    ];

    // Ensure computed attributes are automatically available
    protected $appends = [
        'total_amount',
        'amount_paid',
        'remaining_balance',
        'payment_status',
    ];

    // Relationships
    public function saleItems()
    {
        return $this->hasMany(SaleItem::class, 'sale_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // --- Financial Accessors ---

    /**
     * Map 'total_amount' to 'total' DB column (falls back to items sum if total is 0 or null)
     */
    public function getTotalAmountAttribute(): float
    {
        $total = (float) $this->attributes['total'];

        if ($total > 0) {
            return round($total,2);
        }

        // Safety fallback: sum directly from items if DB column isn't populated
        return (float) round($this->saleItems->sum(function ($item) {
            return $item->unit_price * $item->quantity;
        }),2);
    }

    /**
     * Total Amount Paid
     */
    public function getAmountPaidAttribute(): float
    {
        return (float) round($this->payments->sum('amount'),2);
    }

    /**
     * Remaining Balance
     */
    public function getRemainingBalanceAttribute(): float
    {
        return round($this->total_amount - $this->amount_paid,2);
    }

    /**
     * Payment Status Badge Logic
     */
    public function getPaymentStatusAttribute(): string
    {
        if ($this->status === 'cancelled') {
            return 'cancelled';
        }

        $paid = $this->amount_paid;
        $total = $this->total_amount;

        if ($paid <= 0) {
            return 'unpaid';
        }

        if ($paid < $total) {
            return 'partial';
        }

        return 'paid';
    }
}