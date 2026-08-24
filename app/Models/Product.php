<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'purchase_price', // Added purchase_price
        'stock',
        'alert_threshold',
        'category_id',
        'image',
    ];

    public function category(){
        return $this->belongsTo(Category::class, 'category_id');
    }
    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class, 'product_id');
    }
    public function getProfitAttribute(): float
    {
        return $this->price - ($this->purchase_price ?? 0);
    }

    public function getMarginPercentageAttribute(): float
    {
        if (!$this->purchase_price || $this->purchase_price <= 0) {
            return 0;
        }

        return (($this->price - $this->purchase_price) / $this->purchase_price) * 100;
    }
}
