<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'purpose',
        'item',
        'amount',
        'expense_date',
        'notes'
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount' => 'decimal:2'
    ];
}
