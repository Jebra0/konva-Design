<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $incrementing = false;

    protected $fillable = [
        'cookie_id',
        'user_id',
        'category_id',
        'quantity',
        'options',
    ];
    
    public function product(){
        return $this->belongsTo(TemplateCategory::class, 'category_id', 'id');
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
