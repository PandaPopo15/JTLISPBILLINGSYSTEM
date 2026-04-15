<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DashboardSetting extends Model
{
    protected $fillable = [
        'dashboard_title',
        'primary_color',
        'dashboard_logo',
        'favicon',
        'dashboard_tagline',
    ];
}
