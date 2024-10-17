<?php

namespace App\Http\Controllers;

use App\Repositories\Cart\CartModelRepository;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CartController extends Controller
{
    public function index(){
        $repo = new CartModelRepository();

        return Inertia::render('Cart', [
            'cart' => $repo->get(),
        ]);
    }
}
