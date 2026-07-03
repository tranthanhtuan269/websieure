<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\ThemeController;
use App\Http\Controllers\Account\AffiliateController as AccountAffiliateController;
use App\Http\Controllers\Account\DashboardController as AccountDashboardController;
use App\Http\Controllers\Account\OrderController as AccountOrderController;
use App\Http\Controllers\Account\PayoutController as AccountPayoutController;
use App\Http\Controllers\Admin\AffiliateController as AdminAffiliateController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\PayoutRequestController;
use App\Http\Controllers\Admin\LandingPageController as AdminLandingPageController;
use App\Http\Controllers\Admin\ThemeController as AdminThemeController;
use Illuminate\Support\Facades\Route;

Route::get('/lp/{landingPage}', [LandingPageController::class, 'show'])->name('landing-pages.show');

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/themes', [ThemeController::class, 'index'])->name('themes.index');
Route::get('/themes/{theme}', [ThemeController::class, 'show'])->name('themes.show');
Route::get('/chu-de', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/chu-de/{category}', [CategoryController::class, 'show'])->name('categories.show');

Route::get('/themes/{theme}/mua', [CheckoutController::class, 'create'])->name('checkout.create');
Route::post('/themes/{theme}/mua', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/dat-hang/{order}', [CheckoutController::class, 'success'])->name('checkout.success');

Route::redirect('/admin/login', '/dang-nhap');

Route::middleware('guest')->group(function () {
    Route::get('/dang-nhap', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/dang-nhap', [AuthController::class, 'login']);
    Route::get('/dang-ky', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/dang-ky', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware('auth')->prefix('tai-khoan')->name('account.')->group(function () {
    Route::get('/', [AccountDashboardController::class, 'index'])->name('dashboard');
    Route::get('/don-hang', [AccountOrderController::class, 'index'])->name('orders.index');
    Route::get('/affiliate', [AccountAffiliateController::class, 'index'])->name('affiliate.index');
    Route::get('/rut-tien', [AccountPayoutController::class, 'index'])->name('payouts.index');
    Route::post('/rut-tien', [AccountPayoutController::class, 'store'])->name('payouts.store');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('categories', AdminCategoryController::class)->except(['show']);
    Route::resource('themes', AdminThemeController::class)->except(['show']);
    Route::resource('landing-pages', AdminLandingPageController::class)->except(['show']);
    Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}/edit', [AdminOrderController::class, 'edit'])->name('orders.edit');
    Route::put('orders/{order}', [AdminOrderController::class, 'update'])->name('orders.update');
    Route::get('affiliates', [AdminAffiliateController::class, 'index'])->name('affiliates.index');
    Route::put('affiliates/settings', [AdminAffiliateController::class, 'updateSettings'])->name('affiliates.settings');
    Route::get('commissions', [AdminAffiliateController::class, 'commissions'])->name('commissions.index');
    Route::get('payout-requests', [PayoutRequestController::class, 'index'])->name('payouts.index');
    Route::get('payout-requests/{payout}/edit', [PayoutRequestController::class, 'edit'])->name('payouts.edit');
    Route::put('payout-requests/{payout}', [PayoutRequestController::class, 'update'])->name('payouts.update');
});
