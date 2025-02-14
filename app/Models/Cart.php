<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Cart extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $fillable = [
        'id',
        'cookie_id',
        'user_id',
        'category_id',
        'quantity',
        'image',
        'data',
        'file'
    ];

    protected function casts(): array
    {
        return [
            'data' => 'json',
        ];
    }
    
    public function category(){
        return $this->belongsTo(TemplateCategory::class, 'category_id', 'id')
            ->with('options');
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function options(): BelongsToMany{
        return $this->belongsToMany(
            OptionValue::class,
            'cart_option_values',
            'cart_id',
            'option_value_id',
        );
    }
}
