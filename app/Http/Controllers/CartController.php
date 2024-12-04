<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Option;
use App\Repositories\Cart\CartModelRepository;
use Auth;
use GrahamCampbell\ResultType\Success;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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

        return redirect()->back();
    }

    public function update(Request $request, string $id)
    {
            $request->validate([
                'quantity' => 'required|integer',
                'option_val_id' => 'nullable|exists:option_values,id'
            ]);

            $cart = new CartModelRepository();
            $cart->updateCart(
                $id,
                $request->post('quantity'),
                $request->post('option_val_id')
            );

            return redirect()->back();
    }

    public function destroy(Cart $cart)
    {
        $imageDeleted = !Storage::disk('public_images')->exists($cart->image) || Storage::disk('public_images')->delete($cart->image);
        if($imageDeleted){
            $cartDeleted = $cart->delete();
            if($cartDeleted){
                return redirect()->back()->with('message', 'The item has been deleted.');
            }
        }
        return redirect()->back()->with('error', 'There was an error deleting the item.');
    }
}
