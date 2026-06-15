<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Theme;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(): View
    {
        $orders = Order::with('theme')->latest()->paginate(20);

        return view('admin.orders.index', compact('orders'));
    }

    public function edit(Order $order): View
    {
        $order->load('theme');

        return view('admin.orders.form', compact('order'));
    }

    public function update(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:pending,paid,delivered,cancelled'],
            'note' => ['nullable', 'string'],
        ]);

        $oldStatus = $order->status;
        $order->update($data);

        if (in_array($data['status'], [Order::STATUS_PAID, Order::STATUS_DELIVERED], true)
            && ! in_array($oldStatus, [Order::STATUS_PAID, Order::STATUS_DELIVERED], true)) {
            $order->theme()->increment('sales_count');
        }

        return redirect()->route('admin.orders.index')->with('success', 'Đã cập nhật đơn hàng.');
    }
}
