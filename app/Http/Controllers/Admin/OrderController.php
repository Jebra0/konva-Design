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
        $orders = Order::latest()->paginate(1);
        return inertia('Admin/Orders/Index', compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
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

        // Start a query builder instance for Order
        $ordersQuery = Order::query();

        // Apply filters only if the request data is present
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

        // Paginate the results
        $orders = $ordersQuery->paginate(1);

        return inertia('Admin/Orders/Index', compact('orders'));
    }


    // public function filter(Request $request)
    // {
    //     $request->validate([
    //         'number' => 'nullable|integer|exists:orders,number',
    //         'user' => 'nullable|integer|exists:users,id',
    //         'status' => 'nullable|string|in:pending,processing,completed,cancelled,refunded',
    //         'payment_status' => 'nullable|max:255|in:pending,paid,failed'
    //     ]);

    //     $orders = Order::where('number', $request->post('number'))
    //         ->where('user_id', $request->post('user'))
    //         ->where('status', $request->post('status'))
    //         ->where('payment_status', $request->post('payment_status'))
    //         ->paginate(1);

    //     return inertia('Admin/Orders/Index', compact('orders'));

    // }
}
