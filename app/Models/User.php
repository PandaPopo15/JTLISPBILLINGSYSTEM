<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name', 'middle_name', 'last_name',
        'username', 'email', 'phone_number',
        'address', 'latitude', 'longitude',
        'age', 'plan_interest', 'mikrotik_id',
        'pppoe_username', 'pppoe_password', 'status',
        'password', 'is_admin', 'email_verified_at',
        'profile_image',
        'due_date',
        'installation_date',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'due_date' => 'date',
            'installation_date' => 'date',
            'is_admin' => 'integer',
        ];
    }

    /**
     * Get full name attribute
     */
    public function getFullNameAttribute()
    {
        return trim("{$this->first_name} {$this->middle_name} {$this->last_name}");
    }

    /**
     * Get location attribute (latitude,longitude)
     */
    public function getLocationAttribute()
    {
        if ($this->latitude && $this->longitude) {
            return $this->latitude . ',' . $this->longitude;
        }
        return null;
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->is_admin === 1;
    }

    /**
     * Check if user is installer/technician
     */
    public function isInstaller(): bool
    {
        return $this->is_admin === 2;
    }

    /**
     * Check if user is client
     */
    public function isClient(): bool
    {
        return $this->is_admin === 0;
    }

    /**
     * Check if email is verified
     */
    public function isEmailVerified(): bool
    {
        return $this->email_verified_at !== null;
    }

    /**
     * Check if user is normal user
     */
    public function isNormalUser(): bool
    {
        return $this->is_admin === 0;
    }

    public function mikrotik()
    {
        return $this->belongsTo(Mikrotik::class, 'mikrotik_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function jobOrders()
    {
        return $this->hasMany(JobOrder::class, 'assigned_to');
    }

    public function clientJobOrders()
    {
        return $this->hasMany(JobOrder::class, 'client_id');
    }
}

