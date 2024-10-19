<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $fillable = ['name'];

    public function options(){
        return $this->belongsToMany(
            TemplateCategory::class,
            'category_options',
            'option_id',
            'category_id',
            'id',
            'id'
        );
    }

    public function values(){
        return $this->hasMany(OptionValue::class, 'option_id', 'id');
    }
}
