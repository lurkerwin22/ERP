<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'image',
        'price',
        'stock',
        'alert_threshold',
        'category_id',
    ];  
    public function category(){
        return $this->belongsTo(Category::class, 'category_id');
    }
    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class, 'product_id');
    }
}
