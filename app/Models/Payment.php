<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'user_id',
        'due_date',
        'paid_date',
        'status',
        'amount',
    ];

    protected $casts = [
        'due_date' => 'date',
        'paid_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isPaid()
    {
        return $this->status === 'paid';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isOverdue()
    {
        return $this->status === 'overdue';
    }
}
