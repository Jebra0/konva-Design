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
        Cart::where('cookie_id', $this->getCookieId())->get();
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
        Cart::where('cookie_id', $this->getCookieId())
            ->join('templates_categories', 'templates_categories.id', '=', 'carts.category_id')
            ->selectRaw('SUM(carts.quantity * templates_categories.price) AS totlal')
            ->value('total');
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