<?php

namespace App\Http\Controllers;

use App\Repositories\Cart\CartModelRepository;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CartController extends Controller
{
    public function index()
    {
        $cart = new CartModelRepository();

        return Inertia::render('Cart', [
            'cart' => $cart->get(),
        ]);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'category_id' => 'required|exists:templates_categories,id',
                'quantity' => 'integer|required',
                'data' => 'json|required',
                'image' => 'required|image|mimes:png|max:2048'
            ]);
 
            $cart = new CartModelRepository();
            $cart->add(
                $request->post('category_id'),
                $request->post('data'),
                $request->file('image'),
                $request->post('quantity')
            );

            return redirect()->route('cart.index');
        }catch(\Exception $e){
            // return $e->getMessage();
        }

    }
}
