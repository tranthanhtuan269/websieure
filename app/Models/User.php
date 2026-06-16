<?php

namespace App\Models;

use App\Enums\CommissionStatus;
use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'referral_code',
        'affiliate_balance',
        'affiliate_total_earned',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (User $user): void {
            $adminEmail = config('site.admin_email');

            if ($user->role === UserRole::Admin && $user->email !== $adminEmail) {
                $user->role = UserRole::User;
            }

            if ($user->email === $adminEmail) {
                $user->role = UserRole::Admin;
            }
        });

        static::created(function (User $user): void {
            if (! $user->referral_code) {
                app(\App\Services\AffiliateService::class)->ensureReferralCode($user);
            }
        });
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function referredOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'referrer_id');
    }

    public function commissions(): HasMany
    {
        return $this->hasMany(AffiliateCommission::class, 'referrer_id');
    }

    public function payoutRequests(): HasMany
    {
        return $this->hasMany(AffiliatePayoutRequest::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    public function isUser(): bool
    {
        return $this->role === UserRole::User;
    }

    public function roleLabel(): string
    {
        return $this->role->label();
    }

    public function approvedCommissionsCount(): int
    {
        return $this->commissions()->where('status', CommissionStatus::Approved)->count();
    }
}
