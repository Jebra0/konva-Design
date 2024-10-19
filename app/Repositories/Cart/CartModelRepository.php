<?php

namespace App\Repositories\Cart;
use App\Models\Cart;
use App\Models\TemplateCategory;
use Cookie;
use Str;

class CartModelRepository implements CartRepository
{
    public function get()
    {
        return Cart::with('category', 'options')->where('cookie_id', $this->getCookieId())->get();
    }

    public function add($category_id, $data, $image, $quantity)
    {
        try {
            $path = $image->store('images/' . 'cart_Images', ['disk' => 'public_images']);

            return Cart::create([
                'id' => Str::uuid(),
                'cookie_id' => $this->getCookieId(),
                'user_id' => auth()->id(),
                'category_id' => $category_id,
                'quantity' => $quantity,
                'image' => $path,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            throw $e;
        }

    }

    public function update($id, $quantity = 1)
    {
        Cart::where('id', $id)
            ->where('cookie_id', $this->getCookieId())
            ->update([
                'quantity' => $quantity
            ]);
    }

    public function delete($id)
    {
        Cart::where('id', $id)
            ->where('cookie_id', $this->getCookieId())
            ->delete();
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