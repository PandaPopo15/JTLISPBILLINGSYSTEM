<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('landing_settings', function (Blueprint $table) {
            $table->id();
            $table->string('isp_name')->default('ISP Billing');
            $table->string('theme_color')->default('#ff5252');
            $table->string('headline')->default('Fast, Reliable Internet for Your Home or Business');
            $table->text('subheadline')->default('Choose the plan that fits your needs and register with your email. Verify your account and wait for admin approval before installation.');
            $table->json('plans')->nullable();
            $table->string('logo_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('landing_settings');
    }
};
