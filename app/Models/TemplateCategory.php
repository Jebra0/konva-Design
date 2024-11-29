<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TemplateCategory extends Model
{
    use HasFactory;

    protected $table = 'templates_categories';

    protected $fillable = ['name', 'price', 'quantity', 'user_id'];

    public function templates()
    {
        return $this->hasMany(Template::class, 'category_id');
    }

    public function options()
    {
        return $this->belongsToMany(
            Option::class,
            'category_options',
            'category_id',
            'option_id',
        )->with('values');
    }

    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(
            Order::class,
            'order_items',
            'category_id',
            'order_id',
        )->using(OrderItem::class)
            ->withPivot(['category_name', 'category_price', 'quantity']);
    }

    public static function top_products()
    {
        return self::withCount('orders')
            ->orderBy('orders_count', 'desc')
            ->limit(20)
            ->get();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
