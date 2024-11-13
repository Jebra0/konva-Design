<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Stripe\StripeClient;
use Stripe\PaymentIntent;
use Illuminate\Support\Str;
use Stripe\Stripe as StripeGateway;
class PaymentController extends Controller
{
    public function index(Order $order){
        return inertia('Payment', compact('order'));
    }

    public function createStripePaymentIntent(Order $order)
    {
        $stripe = new StripeClient(config('services.stripe.secret_key'));

        $paymentIntent = $stripe->paymentIntents->create([
            'amount' => $order->total,
            'currency' => 'usd',
            'payment_methods_types' => ['card'],
        ]);
        return ['clientSecret' => $paymentIntent->client_secret];
    }

    public function getSession()
    {
        $stripe = new StripeClient(config('services.stripe.secret_key'));

        return $stripe->checkout->sessions->create([
            'success_url' => 'http://127.0.0.1:8000/success',
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => 'usd',
                        'unit_amount' => 449,
                        'product_data' => [
                            'name' => 'business card'
                        ]
                    ],
                    'quantity' => 1,
                ],
            ],
            'mode' => 'payment',
        ]);
    }
}
