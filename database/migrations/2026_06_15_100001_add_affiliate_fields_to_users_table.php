<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('referral_code', 20)->nullable()->unique()->after('role');
            $table->unsignedBigInteger('affiliate_balance')->default(0)->after('referral_code');
            $table->unsignedBigInteger('affiliate_total_earned')->default(0)->after('affiliate_balance');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['referral_code', 'affiliate_balance', 'affiliate_total_earned']);
        });
    }
};
