<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LigneVente extends Model
{
    protected $table = 'ligne_ventes';

    protected $fillable = [
        'vente_id',
        'product_id',
        'nom_produit',
        'prix_unitaire',
        'quantite',
        'sous_total',
    ];

    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id');
    }

    // Alias so both $item->product and $item->produit work safely
    public function produit()
    {
        return $this->product();
    }

    public function vente()
    {
        return $this->belongsTo(Vente::class, 'vente_id');
    }
}