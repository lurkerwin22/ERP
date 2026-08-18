<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vente extends Model
{
    protected $fillable = [
        'client_id',
        'client_nom',
        'client_telephone',
        'client_adresse',
        'date_vente',
        'total',
        'statut',
        'notes',
    ];

    public function ligneVentes()
    {
        return $this->hasMany(LigneVente::class, 'vente_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }
}