<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('landing_page_generations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('affiliate_url', 500);
            $table->string('topic')->nullable();
            $table->string('status', 20)->default('pending');
            $table->string('step', 20)->default('pending');
            $table->unsignedTinyInteger('progress')->default(0);
            $table->json('crawl_data')->nullable();
            $table->json('pack_data')->nullable();
            $table->string('zip_path')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('landing_page_generations');
    }
};
