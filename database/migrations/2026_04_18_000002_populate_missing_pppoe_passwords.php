<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        // Generate PPPoE passwords for users who don't have one
        $users = DB::table('users')
            ->whereNull('pppoe_password')
            ->orWhere('pppoe_password', '')
            ->get();

        foreach ($users as $user) {
            DB::table('users')
                ->where('id', $user->id)
                ->update(['pppoe_password' => Str::random(8)]);
        }
    }

    public function down(): void
    {
        // No rollback needed
    }
};
