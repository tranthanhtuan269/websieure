<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AffiliateCommission;
use App\Models\AffiliatePayoutRequest;
use App\Models\Category;
use App\Models\Order;
use App\Models\Theme;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'themes' => Theme::count(),
            'categories' => Category::count(),
            'orders' => Order::count(),
            'pending_orders' => Order::where('status', Order::STATUS_PENDING)->count(),
            'revenue' => Order::whereIn('status', [Order::STATUS_PAID, Order::STATUS_DELIVERED])->sum('amount'),
            'commissions' => AffiliateCommission::where('status', 'approved')->sum('commission_amount'),
            'pending_payouts' => AffiliatePayoutRequest::where('status', 'pending')->count(),
        ];

        $recentOrders = Order::with('theme')->latest()->take(8)->get();

        return view('admin.dashboard', compact('stats', 'recentOrders'));
    }
}
