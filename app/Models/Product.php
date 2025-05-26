<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Product extends Model
{
    protected $table = 'products'; // Explicit table name
    protected $primaryKey = 'product_id';
    public $timestamps = true; // Enable timestamps if columns exist

    protected $fillable = [
        'sub_category_id',
        'brand_id',
        'product_name',
        'product_description',
    ];

    public function subCategory(): BelongsTo
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id', 'sub_category_id');
    }

    public function category(): HasOneThrough
    {
        return $this->hasOneThrough(
            Category::class,
            SubCategory::class,
            'sub_category_id', // Foreign key on SubCategory
            'category_id', // Foreign key on Category
            'sub_category_id', // Local key on Product
            'category_id' // Local key on SubCategory
        );
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class, 'brand_id', 'brand_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class, 'product_id', 'product_id');
    }

    public function prices(): HasMany
    {
        return $this->hasMany(ProductPrice::class, 'product_id', 'product_id');
    }
}
