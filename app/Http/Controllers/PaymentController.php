<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Repositories\Cart\CartModelRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\StripeClient;
class PaymentController extends Controller
{
    public function index(Order $order){
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access.');
        }

        return inertia('Payment',['order' => $order]);
    }

    public function createStripePaymentIntent(Order $order)
    {
        $stripe = new StripeClient(config('services.stripe.secret_key'));

        $paymentIntent = $stripe->paymentIntents->create([
            'amount' => $order->total * 100,
            'currency' => 'usd',
            'payment_method_types' => ['card'],
        ]);

        $payment = Payment::where('order_id', $order->id)->first();
        if(!$payment){
            Payment::create([
                'user_id' => Auth::user()->id,
                'order_id' => $order->id,
                'amount' => $paymentIntent->amount,
                'currency' => $paymentIntent->currency,
                'status' => 'pending',
                'method' => 'stripe',
                'transaction_id' => $paymentIntent->id,
                'data' => json_encode($paymentIntent),
            ]);
        }
        return ['clientSecret' => $paymentIntent->client_secret];
    }

    public function confirm(Request $request, Order $order){
        $stripe = new StripeClient(config('services.stripe.secret_key'));

        $paymentIntent = $stripe->paymentIntents->retrieve($request->query('payment_intent'), []);

        if ($paymentIntent->status == 'succeeded') {
            $payment = Payment::where('order_id', $order->id)->first();
            if($payment){
                $payment->update([
                    'status' => 'completed',
                    'data' => json_encode($paymentIntent),
                ]);
                // update order payment status
                 $order->update(['payment_status' => 'paid']);

                 // delete cart
                $cart = new CartModelRepository();
                $cart->empty();

                return redirect()->route('home')->with('message', 'Payment successful!');
            }
        }

        return redirect()->back()->with('error', 'Payment Failed!');
    }

}
