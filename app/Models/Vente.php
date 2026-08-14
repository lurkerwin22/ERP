<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vente extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Relationship: A Sale belongs to a Client (nullable for walk-in/general customers).
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Relationship: A Sale has many Sale Items (LigneVente).
     */
    public function ligneVentes()
    {
        return $this->hasMany(LigneVente::class);
    }
}