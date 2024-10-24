<?php

namespace App\Repositories\Cart;

use App\Models\Cart;
use App\Models\TemplateCategory;

interface CartRepository  {
    public function get();

    public function add($category_id, $data, $image, $quantity);

    public function updateCart($id, $quantity, $option_val_id);

    public function total();

    public function empty();
}