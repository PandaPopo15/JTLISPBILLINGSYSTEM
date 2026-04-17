<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Napbox extends Model
{
    protected $fillable = [
        'name',
        'location',
        'latitude',
        'longitude',
        'mikrotik_id',
        'notes',
    ];

    public function mikrotik()
    {
        return $this->belongsTo(Mikrotik::class);
    }
}
