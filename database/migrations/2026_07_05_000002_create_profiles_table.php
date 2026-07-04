<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('username')->unique();
            $table->string('display_name')->nullable();
            $table->string('tagline')->nullable();
            $table->text('bio')->nullable();
            $table->string('avatar_path')->nullable();
            $table->boolean('is_published')->default(true);
            $table->boolean('is_verified')->default(false);
            $table->json('theme')->nullable();
            $table->text('custom_css')->nullable();
            $table->string('seo_title')->nullable();
            $table->string('seo_description')->nullable();
            $table->string('og_image_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
