<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductPrice extends Model
{
    protected $table = 'product_prices';
    protected $primaryKey = 'product_price_id';
    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'size_id',
        'color_id',
        'price',
        'stock_qty',
        'return_points',
        'is_promotion',
        'promotion_price'
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    public function size()
    {
        return $this->belongsTo(Size::class, 'size_id', 'size_id');
    }


    public function color()
    {
        return $this->belongsTo(Color::class, 'color_id', 'color_id');
    }
}
