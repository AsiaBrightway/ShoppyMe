<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    protected $primaryKey = 'sub_category_id';
    protected $fillable = [
        'category_id',
        'sub_category_name',
        'image'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'sub_category_id', 'sub_category_id');
    }
}