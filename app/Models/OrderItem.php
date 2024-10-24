<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class OrderItem extends Pivot
{
    use HasFactory;

    public $table = 'order_items';
    public $incrementing = true;

    public function category():BelongsTo
    {
        return $this->belongsTo(TemplateCategory::class, 'category_id')
            ->withDefault([
                'name' => $this->category_name,
                'price' => $this->category_price
            ]);
    }

    public function order():BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
