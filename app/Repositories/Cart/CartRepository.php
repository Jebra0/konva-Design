<?php

namespace App\Repositories\Cart;

use App\Models\Cart;
use App\Models\TemplateCategory;

interface CartRepository  {
    public function get();

    public function add($category_id, $data, $image, $quantity);

    public function update($id, $quantity);

    public function delete($id);

    public function total();

    public function empty();
}