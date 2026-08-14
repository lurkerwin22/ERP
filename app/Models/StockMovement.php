<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'type',
        'quantity',
        'reason',
    ];

    /**
     * Relationship: A stock movement belongs to a Produit.
     */
    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id');
    }
}