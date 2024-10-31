<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'number',
        'status',
        'payment_status',
        'payment_method',
        'shipping_cost',
        'tax',
        'discount',
        'total',
    ];

    public static function booted()
    {
        static::creating(function (Order $order) {
            $order->number = self::getNextOrderNum();
        });
    }

    public static function getNextOrderNum(): int|string
    {
        $year = date('Y');
        $number = Order::whereYear('created_at', $year)->max('number');
        if ($number) {
            return $number += 1;
        } else {
            return $year . '0001';
        }
    }


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(
            TemplateCategory::class,
            'order_items',
            'order_id',
            'category_id'
        )->using(OrderItem::class)
            ->withPivot(['category_name', 'price', 'quantity']);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(OrderAddress::class);
    }

    public function billing(): HasOne
    {
        return $this->hasOne(OrderAddress::class)
            ->where('type', 'billing');
    }

    public function shipping(): HasOne
    {
        return $this->hasOne(OrderAddress::class)
            ->where('type', 'shipping');
    }

    public static function total_orders() :int
    {
        return self::count();
    }

}
