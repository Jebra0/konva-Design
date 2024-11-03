<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    use HasFactory;

    //public $incrementing = false;

    public $timestamps = false;

    protected $fillable = ['name'];

    public function categories(){
        return $this->belongsToMany(
            TemplateCategory::class,
            'category_options',
            'option_id',
            'category_id',
        );
    }

    public function values(){
        return $this->hasMany(OptionValue::class, 'option_id', 'id');
    }
}
