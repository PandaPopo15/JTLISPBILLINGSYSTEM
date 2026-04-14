<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = [
        'description',
        'amount',
        'sale_date',
        'type',
        'customer_name',
        'notes'
    ];

    protected $casts = [
        'sale_date' => 'date',
        'amount' => 'decimal:2'
    ];
}
