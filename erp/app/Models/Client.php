<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'nom',
        'email',
        'telephone',
        'adresse',
        'ville',
        'notes',
    ];

    /**
     * Relationship: One Client has many Ventes (Sales).
     * Ready for when we build the Sales feature later!
     */
    public function ventes()
    {
        // Using 'client_id' as the foreign key in the upcoming 'ventes' table
        return $this->hasMany(Vente::class, 'client_id');
    }
}