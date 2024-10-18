<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    ];

    protected function casts(): array
    {
        return [
            'data' => 'json',
        ];
    }
    
    public function product(){
        return $this->belongsTo(TemplateCategory::class, 'category_id', 'id');
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
