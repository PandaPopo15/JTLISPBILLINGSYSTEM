<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\LandingSetting;

class ViewServiceProvider extends ServiceProvider
{
    public function boot()
    {
        View::composer('admin.*', function ($view) {
            $dashboardSettings = LandingSetting::first();
            $view->with('dashboardSettings', $dashboardSettings);
        });
    }
}
