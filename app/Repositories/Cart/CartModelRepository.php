<?php

namespace App\Repositories\Cart;
use App\Models\Cart;
use App\Models\TemplateCategory;
use Cookie;
use Log;
use Storage;
use Str;

class CartModelRepository implements CartRepository
{
    public function get()
    {
        return Cart::with('category', 'options')->where('cookie_id', $this->getCookieId())->get();
    }

    public function add($category_id, $data, $image, $quantity, $file)
    {
        try {
            $img_path = $image->store('images/' . 'cart_Images', ['disk' => 'public_images']);
            // $file_path = $file->store('printed_PDF_files', ['disk' => 'public_images']);

            return Cart::create([
                'id' => Str::uuid(),
                'cookie_id' => $this->getCookieId(),
                'user_id' => auth()->id(),
                'category_id' => $category_id,
                'quantity' => $quantity,
                'image' => $img_path,
                'file' => $file,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            throw $e;
        }

    }

    public function updateCart($id, $quantity, $option_val_id)
    {
        $cart = Cart::with('options')
            ->where('id', $id)
            ->first();

        if ($cart) {
            $cart->update([
                'quantity' => $quantity,
            ]);
            $cart->options()->attach( $option_val_id);
        }
    }

    public function total()
    {
        $cartItems = Cart::where('cookie_id', $this->getCookieId())
            ->with('category', 'options')
            ->get();

        $total = 0;

        foreach ($cartItems as $cartItem) {
            $quantity = $cartItem->quantity;

            $category_price = $cartItem->category ? $cartItem->category->price : 0;

            $options_sum_price = $cartItem->options->sum('price');

            $total += $quantity * ($category_price + $options_sum_price);
        }

        return $total;
    }

    public function empty()
    {
        Cart::where('cookie_id', $this->getCookieId())->delete();
    }

    public function getCookieId()
    {
        $cookie_id = Cookie::get('cartId');

        if (!$cookie_id) {
            $cookie_id = Str::uuid();
            Cookie::queue('cartId', $cookie_id, 30 * 24 * 60);
        }

        return $cookie_id;
    }
}