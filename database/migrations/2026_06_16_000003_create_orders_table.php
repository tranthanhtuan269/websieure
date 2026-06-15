<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code', 20)->unique();
            $table->foreignId('theme_id')->constrained()->cascadeOnDelete();
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone', 20)->nullable();
            $table->unsignedInteger('amount');
            $table->string('status', 20)->default('pending');
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
