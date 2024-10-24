<?php

namespace App\Http\Controllers;

use App\Repositories\Cart\CartModelRepository;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function index(){
        $cart = new CartModelRepository();

        return inertia()->render('checkout',[
            'cart' => $cart->get(),
            'user' => auth()->user(),
        ]);
    }

    public function store(Request $request){
        return $request;
    }
}
