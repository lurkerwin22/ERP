<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'city',
        'notes',
    ];

    /**
     * Relationship: One Customer has many Sales.
     * Ready for when we build the Sales feature later!
     */
    public function sales()
    {
        // Using 'customer_id' as the foreign key in the upcoming 'sales' table
        return $this->hasMany(Sale::class, 'customer_id');
    }
}