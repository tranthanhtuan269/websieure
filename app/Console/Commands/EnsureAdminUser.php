<?php

namespace App\Console\Commands;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class EnsureAdminUser extends Command
{
    protected $signature = 'admin:ensure {--password= : Mật khẩu admin (mặc định lấy từ ADMIN_PASSWORD trong .env)}';

    protected $description = 'Tạo hoặc reset tài khoản admin theo ADMIN_EMAIL / ADMIN_PASSWORD trong .env';

    public function handle(): int
    {
        $email = config('site.admin_email');
        $password = $this->option('password') ?: config('site.admin_password');

        if (! $email || ! $password) {
            $this->error('Thiếu ADMIN_EMAIL hoặc ADMIN_PASSWORD trong .env');

            return self::FAILURE;
        }

        $user = User::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => 'Admin',
                'password' => Hash::make($password),
                'role' => UserRole::Admin,
            ]
        );

        $this->info('Tài khoản admin đã sẵn sàng.');
        $this->line("  Email:    {$user->email}");
        $this->line("  Mật khẩu: {$password}");
        $this->line('  Đăng nhập: /dang-nhap');

        return self::SUCCESS;
    }
}
