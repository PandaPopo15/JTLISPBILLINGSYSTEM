<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Mikrotik extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'zerotier_network_id',
        'ip_address',
        'port',
        'username',
        'password',
        'location',
        'notes',
        'is_active',
        'last_connected_at',
    ];

    protected $casts = [
        'is_active'          => 'boolean',
        'last_connected_at'  => 'datetime',
    ];

    protected $hidden = ['password'];

    public function clients()
    {
        return $this->hasMany(User::class, 'mikrotik_id');
    }

    public function getClientCountAttribute(): int
    {
        return $this->clients()->count();
    }
}
