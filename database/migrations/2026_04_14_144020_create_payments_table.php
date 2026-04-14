<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('due_date');
            $table->date('paid_date')->nullable();
            $table->enum('status', ['pending', 'paid', 'overdue'])->default('pending');
            $table->decimal('amount', 10, 2)->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'due_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};