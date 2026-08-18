<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'amount',
        'payment_method',
        'paid_at',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class, 'sale_id');
    }
    
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'sale_id');
    }

    // 2. Add Accessors for derived values
    public function getAmountPaidAttribute(): float
    {
        // Sum loaded payments in memory if relationship is loaded to avoid extra queries
        return (float) $this->payments->sum('amount');
    }

    public function getRemainingAmountAttribute(): float
    {
        return max(0, (float) $this->total - $this->amount_paid);
    }

    public function getPaymentStatusAttribute(): string
    {
        if ($this->amount_paid <= 0) {
            return 'unpaid';
        }

        if ($this->amount_paid < $this->total) {
            return 'partial';
        }

        return 'paid';
    }
}