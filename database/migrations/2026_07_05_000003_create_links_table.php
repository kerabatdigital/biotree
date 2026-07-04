<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('links', function (Blueprint $table) {
            $table->id();
            $table->ulid('ulid')->unique();                 // public id for /out/{ulid} click tracking (Phase 3)
            $table->foreignId('profile_id')->constrained()->cascadeOnDelete();
            $table->string('type')->default('link');        // link | header | social | embed
            $table->string('title')->nullable();
            $table->string('url', 2048)->nullable();
            $table->string('icon')->nullable();             // phosphor icon name (no prefix)
            $table->string('thumbnail_path')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('clicks_count')->default(0);
            $table->timestamp('start_at')->nullable();      // scheduling (Pro)
            $table->timestamp('end_at')->nullable();
            $table->timestamps();

            $table->index(['profile_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('links');
    }
};
