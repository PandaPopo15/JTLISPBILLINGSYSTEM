<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobOrder extends Model
{
    protected $fillable = [
        'client_id',
        'assigned_to',
        'status',
        'installation_date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'installation_date' => 'date',
        ];
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function installer()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
