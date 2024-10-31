<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TemplateCategory;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return inertia()->render('Admin/Dashboard', [
            'user' => Auth()->user(),
            'total_orders' => Order::total_orders(),
            'total_revenue' => Order::total_revenue(),
            'AOV' => Order::AOV(),
            'completed_orders' => Order::completed_orders(),
            'pending_orders' => Order::pending_orders(),
            'cancelled_orders' => Order::cancelled_orders(),
            'refunded_orders' => Order::refunded_orders(),
            //'abandoned_carts'
            'top_products' => TemplateCategory::top_products(),
        ]);
    }

    public function products()
    {
        $products = TemplateCategory::orderBy('id', 'desc')->paginate(5);
        return inertia()->render('Admin/Products', [
            'user' => Auth()->user(),
            'products' => $products,
        ]);
    }

    public function getProductsData()
    {
        $products = TemplateCategory::orderBy('id', 'desc')->paginate(5);
        return response()->json($products);
    }
}
