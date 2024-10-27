<?php

namespace App\Http\Controllers;

use App\Models\Design;
use App\Models\Order;
use App\Models\OrderAddress;
use App\Models\OrderItem;
use App\Repositories\Cart\CartModelRepository;
use App\Repositories\Cart\CartRepository;
use Auth;
use DB;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = new CartModelRepository();
        $total = $cart->total();
        return inertia()->render('checkout', [
            'cart' => $cart->get(),
            'total' => $total,
            'user' => auth()->user(),
        ]);
    }

    public function store(Request $request, CartModelRepository $cart)
    {
        $request->validate([
            'billing_address.first_name' => 'required|string|max:255',
            'billing_address.last_name' => 'required|string|max:255',
            'billing_address.email' => 'required|email|max:255',
            'billing_address.phone' => 'required|string|max:20',
            'billing_address.city' => 'required|string|max:255',
            'billing_address.state' => 'required|string|max:255',
            'billing_address.street' => 'required|string|max:255',
            'billing_address.postal_code' => 'required|string|max:20',

            'shipping_address.first_name' => 'required|string|max:255',
            'shipping_address.last_name' => 'required|string|max:255',
            'shipping_address.email' => 'required|email|max:255',
            'shipping_address.phone' => 'required|string|max:20',
            'shipping_address.city' => 'required|string|max:255',
            'shipping_address.state' => 'required|string|max:255',
            'shipping_address.street' => 'required|string|max:255',
            'shipping_address.postal_code' => 'required|string|max:20',
        ]);

        $billing_address = $request->input('billing_address');
        $billing_address['type'] = 'billing';

        $shipping_address = $request->input('shipping_address');
        $shipping_address['type'] = 'shipping';

        try {
            DB::beginTransaction();

            $order = Order::create([
                'user_id' => Auth::user()->id,
                'payment_method' => 'cod',
                'total' => $cart->total(),
            ]);

            foreach ($cart->get() as $item) {
                $order_item = OrderItem::create([
                    'order_id' => $order->id,
                    'category_id' => $item->category->id,
                    'category_price' => $item->category->price,
                    'category_name' => $item->category->name,
                    'quantity' => $item->quantity
                ]);

                if (!empty($item->options)) {
                    $order_item->options()->attach($item->options);
                }

                Design::create([
                    'user_id' => Auth::user()->id,
                    'name' => $item->category->name,
                    'data' => $item->data,
                    'image' => $item->image,
                ]);
            }

            $order->addresses()->create($shipping_address);
            $order->addresses()->create($billing_address);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            // return $e->getMessage();
        }
    }
}
