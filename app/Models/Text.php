<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Text extends Model
{
    use HasFactory;

    protected $fillable = [
        "name",
        "data",
        "image",
        "user_id"
    ];

    protected function casts(): array
    {
        return [
            'data' => 'json',
        ];
    }
}
