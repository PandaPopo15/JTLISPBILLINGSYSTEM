<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // pending = registered via landing page, awaiting admin approval
            // active  = accepted/added by admin
            // rejected = rejected by admin
            $table->enum('status', ['pending', 'active', 'rejected'])
                  ->default('pending')
                  ->after('mikrotik_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
