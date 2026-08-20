<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Sale;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Customer extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'city',
        'notes',
    ];

    /**
     * Relationship: One Customer has many Sales.
     * Ready for when we build the Sales feature later!
     */
   
    
    public function sales()
    {
        return $this->hasMany(Sale::class, 'customer_id');
    }

    // Calculate customer-level aggregates
    public function getTotalPurchasesAttribute(): float
    {
        return (float) $this->sales->where('status', '!=', 'cancelled')->sum('total');
    }

    public function getTotalPaidAttribute(): float
    {
        return (float) $this->sales->where('status', '!=', 'cancelled')
            ->sum(fn ($sale) => $sale->amount_paid);
    }

    public function getTotalOutstandingDebtAttribute(): float
    {
        return max(0, $this->total_purchases - $this->total_paid);
    }
    public function payments(): HasManyThrough
    {
        return $this->hasManyThrough(Payment::class, Sale::class);
    }
}