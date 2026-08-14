<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    /** @use HasFactory<\Database\Factories\ProductsFactory> */
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'url',
        'prix',
        'stock',
        'seuil_alerte',
        'categorie_id',
    ];  
    public function category(){
        return $this->belongsTo(Categorie::class, 'categorie_id');
    }
    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class, 'product_id');
    }
}
