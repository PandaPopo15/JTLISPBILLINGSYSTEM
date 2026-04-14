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
        'logo_path',
        'dashboard_title',
        'dashboard_tagline',
        'primary_color',
        'dashboard_logo',
        'favicon',
    ];
}
