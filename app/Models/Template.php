<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    use HasFactory;

    protected $fillable = [
        "name",
        "type",
        "data",
        "image",
    ];

    protected function casts(): array
    {
        return [
            'data' => 'json',
        ];
    }
}