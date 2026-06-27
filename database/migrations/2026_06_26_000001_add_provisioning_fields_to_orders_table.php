<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('package_type', 20)->default('theme')->after('status');
            $table->string('domain')->nullable()->after('package_type');
            $table->string('provisioning_status', 30)->nullable()->after('domain');
            $table->string('provisioning_token', 64)->nullable()->unique()->after('provisioning_status');
            $table->string('site_url')->nullable()->after('provisioning_token');
            $table->string('wp_admin_user')->nullable()->after('site_url');
            $table->text('wp_admin_password')->nullable()->after('wp_admin_user');
            $table->string('cloudflare_zone_id')->nullable()->after('wp_admin_password');
            $table->json('provisioning_log')->nullable()->after('cloudflare_zone_id');
            $table->text('provisioning_error')->nullable()->after('provisioning_log');
            $table->timestamp('provisioning_started_at')->nullable()->after('provisioning_error');
            $table->timestamp('provisioning_completed_at')->nullable()->after('provisioning_started_at');

            $table->index('provisioning_status');
            $table->index('package_type');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['provisioning_status']);
            $table->dropIndex(['package_type']);
            $table->dropColumn([
                'package_type',
                'domain',
                'provisioning_status',
                'provisioning_token',
                'site_url',
                'wp_admin_user',
                'wp_admin_password',
                'cloudflare_zone_id',
                'provisioning_log',
                'provisioning_error',
                'provisioning_started_at',
                'provisioning_completed_at',
            ]);
        });
    }
};
