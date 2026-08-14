<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LigneVente extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Relationship: A Sale Line Item belongs to a Sale.
     */
    public function vente()
    {
        return $this->belongsTo(Vente::class);
    }

    /**
     * Relationship: A Sale Line Item belongs to a Product.
     */
    public function produit()
    {
        return $this->belongsTo(Products::class, 'product_id');
    }
}