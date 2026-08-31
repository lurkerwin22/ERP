<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'purchase_price',
        'stock',
        'unit',
        'alert_threshold',
        'category_id',
        'supplier_id',
        'image',
    ];

    public function category(){
        return $this->belongsTo(Category::class, 'category_id');
    }
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }
    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class, 'product_id');
    }
    public function getProfitAttribute(): float
    {
        return $this->price - ($this->purchase_price ?? 0);
    }
    /**
     * Calculate absolute margin (Selling Price - Purchase Price).
     */
    public function getMarginAttribute(): ?float
    {
        if (is_null($this->purchase_price) || is_null($this->price)) {
            return null;
        }

        return (float) $this->price - (float) $this->purchase_price;
    }

    /**
     * Calculate margin percentage based on purchase price.
     */
    public function getMarginPercentageAttribute(): ?float
    {
        if (is_null($this->purchase_price) || (float) $this->purchase_price <= 0 || is_null($this->price)) {
            return null;
        }

        return (($this->price - $this->purchase_price) / $this->purchase_price) * 100;
    }
}
