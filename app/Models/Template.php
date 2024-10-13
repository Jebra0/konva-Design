<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
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

    public function scopeGetTemplates(Builder $query){
        return $query->where('type', 'Template');
    }
    public function scopeGetText(Builder $query){
        return $query->where('type', 'text');
    }
    public function scopeGetShapes(Builder $query){
        return $query->where('type', 'shape');
    }
}
