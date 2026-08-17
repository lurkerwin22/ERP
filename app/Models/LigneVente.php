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
        'quantite',
        'prix_unitaire',
        'sous_total',
    ];

    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id');
    }

    public function vente()
    {
        return $this->belongsTo(Vente::class, 'vente_id');
    }
}