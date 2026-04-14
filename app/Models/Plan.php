<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'name', 'speed', 'price', 'description',
        'installation_fee', 'is_active', 'is_popular',
    ];

    protected function casts(): array
    {
        return [
            'price'            => 'decimal:2',
            'installation_fee' => 'decimal:2',
            'is_active'        => 'boolean',
            'is_popular'       => 'boolean',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('price');
    }
}
