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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->morphs('reportable'); // reportable_type, reportable_id
            $table->string('reporter_email')->nullable();
            $table->string('reason'); // phishing, spam, inappropriate, harassment, other
            $table->string('description')->nullable(); // Optional detailed description
            $table->string('status')->default('open'); // open, reviewed, actioned, dismissed
            $table->text('admin_notes')->nullable(); // Internal admin notes
            $table->foreignId('handled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('handled_at')->nullable();
            $table->timestamps();

            $table->index(['reportable_type', 'reportable_id']);
            $table->index('status');
            $table->index('reason');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
