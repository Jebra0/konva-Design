<?php

namespace App\Repositories\Cart;

use App\Models\TemplateCategory;

interface CartRepository  {
    public function get();

    public function add(TemplateCategory $item, $quantity = 1);

    public function update($id, $quantity);

    public function delete($id);

    public function total();

    public function empty();
}