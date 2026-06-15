<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ThemeController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ThemeController as AdminThemeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/themes', [ThemeController::class, 'index'])->name('themes.index');
Route::get('/themes/{theme}', [ThemeController::class, 'show'])->name('themes.show');
Route::get('/chu-de', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/chu-de/{category}', [CategoryController::class, 'show'])->name('categories.show');

Route::get('/themes/{theme}/mua', [CheckoutController::class, 'create'])->name('checkout.create');
Route::post('/themes/{theme}/mua', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/dat-hang/{order}', [CheckoutController::class, 'success'])->name('checkout.success');

Route::get('/admin/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/admin/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('categories', AdminCategoryController::class)->except(['show']);
    Route::resource('themes', AdminThemeController::class)->except(['show']);
    Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}/edit', [AdminOrderController::class, 'edit'])->name('orders.edit');
    Route::put('orders/{order}', [AdminOrderController::class, 'update'])->name('orders.update');
});
