<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->constrained()->cascadeOnDelete();
            $table->string('visitor_hash', 64)->nullable(); // hashed ip+ua+day — no raw PII (PDPA-friendly)
            $table->string('country', 2)->nullable();
            $table->string('referrer_host')->nullable();
            $table->string('device', 16)->nullable();
            $table->timestamp('created_at')->nullable();

            $table->index(['profile_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_views');
    }
};
