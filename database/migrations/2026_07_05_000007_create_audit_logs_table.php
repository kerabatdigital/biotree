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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained('users')->cascadeOnDelete();
            $table->string('action'); // suspend_user, restore_user, delete_link, unpublish_profile, dismiss_report, etc.
            $table->string('target_type'); // User, Profile, Link, Report
            $table->unsignedBigInteger('target_id');
            $table->json('old_values')->nullable(); // Values before change
            $table->json('new_values')->nullable(); // Values after change
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('created_at');

            $table->index(['target_type', 'target_id']);
            $table->index('admin_id');
            $table->index('action');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
