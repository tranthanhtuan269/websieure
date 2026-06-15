<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('themes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('tagline')->nullable();
            $table->text('description')->nullable();
            $table->json('features')->nullable();
            $table->unsignedInteger('price');
            $table->unsignedInteger('sale_price')->nullable();
            $table->string('preview_url')->nullable();
            $table->string('thumbnail_color', 7)->default('#4f46e5');
            $table->string('thumbnail_label', 32)->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sales_count')->default(0);
            $table->timestamps();

            $table->index(['category_id', 'is_active']);
            $table->index('is_featured');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('themes');
    }
};
