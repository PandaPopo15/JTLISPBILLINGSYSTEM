<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('mikrotik_id')
                  ->nullable()
                  ->after('plan_interest')
                  ->constrained('mikrotiks')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['mikrotik_id']);
            $table->dropColumn('mikrotik_id');
        });
    }
};
