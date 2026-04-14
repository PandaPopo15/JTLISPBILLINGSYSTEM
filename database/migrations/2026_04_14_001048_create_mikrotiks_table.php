<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mikrotiks', function (Blueprint $table) {
            $table->id();
            $table->string('name');                          // friendly label e.g. "Tower 1 - Brgy. San Jose"
            $table->string('zerotier_network_id')->nullable(); // ZeroTier network ID
            $table->string('ip_address');                    // ZeroTier-assigned IP of the MikroTik
            $table->unsignedSmallInteger('port')->default(8728);
            $table->string('username')->default('admin');
            $table->string('password');
            $table->string('location')->nullable();          // physical location / area served
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_connected_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mikrotiks');
    }
};
