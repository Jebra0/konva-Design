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

    public static function total_revenue() : float
    {
        return self::where('status', 'completed')
            ->where('payment_status', 'paid')
            ->sum('total');
    }

    /*
        the Average Order Value = total_revenue / total_orders
    */
    public static function AOV() : float
    {
        $revenue = self::total_revenue();
        $orders = self::total_orders();
        if($orders){
            return $revenue / $orders;
        }
        return 0;
    }

    public static function completed_orders() :int
    {
        return self::where('status', 'completed')
            ->where('payment_status', 'paid')
            ->count();
    }

    public static function pending_orders() :int
    {
        return self::where('status', 'pending')
            ->count();
    }

    public static function cancelled_orders() :int
    {
        return self::where('status', 'cancelled')
            ->count();
    }

    public static function abandoned_cart()
    {
        return self::where('payment_status', 'pending')
            ->count();
    }

    public static function refunded_orders() :int
    {
        return self::where('status', 'refunded')
            ->count();
    }
}
