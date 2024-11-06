<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Option;
use App\Repositories\Cart\CartModelRepository;
use Auth;
use GrahamCampbell\ResultType\Success;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Log;

class CartController extends Controller
{
    public function index()
    {
        $cart = new CartModelRepository();
        $all_options = Option::with('values')->get();
        $total = $cart->total();
        return Inertia::render('Cart', [
            'cart' => $cart->get(),
            'all_options' => $all_options,
            'total' => $total,
            'user' => Auth::user(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:templates_categories,id',
            'quantity' => 'integer|required',
            'jsonData' => 'json|required',
            'image' => 'required|image|mimes:png|max:2048',
        ]);
        // 'file' => 'required|file|mimes:pdf|max:20480'

        $cart = new CartModelRepository();

        $cart->add(
            $request->post('category_id'),
            $request->post('jsonData'),
            $request->file('image'),
            $request->post('quantity'),
            'test'
        );

        return redirect()->back()->with('message', 'The item added to the cart. ');
        // try {
        // } catch (\Exception $e) {
        //     return $e->getMessage();
        // }

    }

    public function update(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|exists:carts,id',
                'quantity' => 'integer|required',
                'option_val_id' => 'required|exists:option_values,id'
            ]);

            $cart = new CartModelRepository();
            $cart->updateCart(
                $request->id,
                $request->quantity,
                $request->option_val_id
            );

            return response()->json(['updatedItem' => $cart->get()]);
        }catch(\Exception $e){
            return $e->getMessage();
        }


    }

    public function destroy(Cart $cart)
    {
        try {
            $cart->delete();
            return response()->json([
                'message' => 'Cart item deleted successfully',
            ]);
        } catch (\Exception $e) {
            // return $e->getMessage();
            return response()->json([
                'message' => 'Failed to delete cart item',
                'err' => $e->getMessage()
            ]);
        }

    }
}
