<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = Order::latest()->paginate(20);
        return inertia('Admin/Orders/Index', compact('orders'));
    }

    public function getOrders()
    {
        return response()->json(Order::paginate(1));
    }

    public function statusUpdate(Request $request, Order $order)
    {

        $request->validate([
            'statusValue' => 'required|in:pending,processing,completed,cancelled,refunded'
        ]);

        $order->update(['status' => $request->statusValue]);

        return redirect()->route('admin.orders.index')->with('message', 'Status Updated to: ' . $request->statusValue . ' Successfully.');
    }
    public function paymentStatusUpdate(Request $request, Order $order)
    {

        $request->validate([
            'payStatusValue' => 'required|in:pending,paid,failed'
        ]);
        $order->update(['payment_status' => $request->payStatusValue]);

        return redirect()->back()->with('message', 'Status Updated to: ' . $request->payStatusValue . ' Successfully.');
    }


    public function filter(Request $request)
    {
        $request->validate([
            'number' => 'nullable|integer|exists:orders,number',
            'user' => 'nullable|integer|exists:users,id',
            'status' => 'nullable|string|in:pending,processing,completed,cancelled,refunded',
            'payment_status' => 'nullable|string|in:pending,paid,failed',
        ]);

        $ordersQuery = Order::query();

        if ($request->filled('number')) {
            $ordersQuery->where('number', $request->post('number'));
        }
        if ($request->filled('user')) {
            $ordersQuery->where('user_id', $request->post('user'));
        }
        if ($request->filled('status')) {
            $ordersQuery->where('status', $request->post('status'));
        }
        if ($request->filled('payment_status')) {
            $ordersQuery->where('payment_status', $request->post('payment_status'));
        }

        $orders = $ordersQuery->paginate(20);

        return inertia('Admin/Orders/Index', compact('orders'));
    }

}
