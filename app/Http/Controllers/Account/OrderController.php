<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $orders = Order::query()
            ->with(['theme', 'referrer', 'commission'])
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhere('customer_email', $user->email);
            })
            ->latest()
            ->paginate(15);

        return view('account.orders.index', compact('orders'));
    }
}
