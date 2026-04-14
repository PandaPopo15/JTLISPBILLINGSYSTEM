<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('landing_settings', function (Blueprint $table) {
            $table->string('dashboard_title')->default('ISP Billing')->after('logo_path');
            $table->string('dashboard_tagline')->nullable()->after('dashboard_title');
            $table->string('primary_color')->default('#ff5252')->after('dashboard_tagline');
            $table->string('dashboard_logo')->nullable()->after('primary_color');
            $table->string('favicon')->nullable()->after('dashboard_logo');
        });
    }

    public function down(): void
    {
        Schema::table('landing_settings', function (Blueprint $table) {
            $table->dropColumn(['dashboard_title', 'dashboard_tagline', 'primary_color', 'dashboard_logo', 'favicon']);
        });
    }
};
