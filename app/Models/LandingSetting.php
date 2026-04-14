<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'isp_name',
        'theme_color',
        'headline',
        'subheadline',
        'plans',
        'logo_path',
    ];

    protected $casts = [
        'plans' => 'array',
    ];
}
