<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('landing_pages', function (Blueprint $table) {
            $table->id();
            $table->string('type', 20);
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('meta_description', 500)->nullable();
            $table->string('hero_image', 500)->nullable();
            $table->string('affiliate_url', 500)->nullable();
            $table->text('intro')->nullable();
            $table->json('sections')->nullable();
            $table->longText('body')->nullable();
            $table->json('popup_settings')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['type', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('landing_pages');
    }
};
