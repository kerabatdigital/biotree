<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')
            ->where('email', 'test@example.com')
            ->update([
                'password' => bcrypt('password123'),
                'email_verified_at' => now(),
            ]);
    }

    public function down(): void
    {
        // Rollback would reset password, left empty
    }
};
