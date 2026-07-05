<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // "Pro Monthly", "Pro Yearly"
            $table->string('slug')->unique(); // pro_monthly, pro_yearly
            $table->text('description')->nullable();
            $table->unsignedInteger('monthly_price_cents')->nullable(); // RM 6 = 600 sen
            $table->unsignedInteger('yearly_price_cents')->nullable(); // RM 40 = 4000 sen
            $table->json('features')->nullable(); // ["unlimited_links", "custom_css", ...]
            $table->json('limits')->nullable(); // {"max_links": 999, ...}
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
