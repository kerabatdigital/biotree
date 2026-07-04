<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('google_id')->nullable()->unique()->after('email');
            $table->string('avatar')->nullable()->after('google_id');
            $table->string('role')->default('user')->after('avatar');       // user | admin
            $table->string('status')->default('active')->after('role');     // active | suspended
            $table->string('plan')->default('free')->after('status');       // free | pro
            $table->string('locale', 5)->default('en')->after('plan');      // en | ms
            $table->timestamp('last_login_at')->nullable()->after('locale');

            // OAuth-only accounts (Google) won't have a password.
            $table->string('password')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'google_id', 'avatar', 'role', 'status', 'plan', 'locale', 'last_login_at',
            ]);
        });
    }
};
