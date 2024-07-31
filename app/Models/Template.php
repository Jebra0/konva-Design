<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    use HasFactory;
    protected $fillable = [
        "name",
        "category_id",
        "data",
        "image",
    ];

    protected function casts(): array
    {
        return [
            'data' => 'json',
        ];
    }

    public function category(){
        return $this->belongsTo(TemplateCategory::class, 'category_id');
    }
}
