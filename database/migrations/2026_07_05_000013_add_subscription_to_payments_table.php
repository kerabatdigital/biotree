<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('subscription_id')->nullable()->after('user_id')->constrained()->nullableOnDelete();
            $table->foreignId('plan_id')->nullable()->after('subscription_id')->constrained()->nullableOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeignKeyIfExists(['subscription_id']);
            $table->dropForeignKeyIfExists(['plan_id']);
            $table->dropColumn(['subscription_id', 'plan_id']);
        });
    }
};
