<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OptionValue extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $fillable = [
        'option_id',
        'value',
        'price',
    ];

    public function option(){
        return $this->belongsTo(Option::class, 'option_id', 'id');
    }

    public function carts(){
        return $this->belongsToMany(
            Cart::class,
            'cart_option_values',
            'option_value_id',
            'cart_id',
            'id',
            'id'
        );
    }
}
