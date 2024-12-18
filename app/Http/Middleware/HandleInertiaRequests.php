<?php

namespace App\Http\Middleware;

use App\Repositories\Cart\CartModelRepository;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): string|null
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $cart = new CartModelRepository();
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
            'flash' => [
                'message' => session('message'),
                'error' => session('error')
            ],
            'cart' => [
                'itemsCount' => $cart->get()->count()
            ],
            'stripe' => [
                'publishable_Key' => config('services.stripe.publish_key'),
                'secret_key' => config('services.stripe.secret_key'),
            ]
        ];
    }
}
