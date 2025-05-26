<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\SizeResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'product_id' => $this->product_id,
            'category' => [
                'category_id' => $this->category?->category_id,
                'category_name' => $this->category?->category_name,
                'image' => $this->category?->image,
                'created_at' => $this->category?->created_at,
                'updated_at' => $this->category?->updated_at,
            ],
            'sub_category' => [
                'sub_category_id' => $this->subCategory?->sub_category_id,
                'sub_category_name' => $this->subCategory?->sub_category_name,
                'image' => $this->subCategory?->image,
                'created_at' => $this->subCategory?->created_at,
                'updated_at' => $this->subCategory?->updated_at,
            ],
            'brand' => [
                'brand_id' => $this->brand?->brand_id,
                'brand_name' => $this->brand?->brand_name,
                'brand_description' => $this->brand?->brand_description,
                'image' => $this->brand?->image,
                'created_at' => $this->brand?->created_at,
                'updated_at' => $this->brand?->updated_at,
            ],
            'product_name' => $this->product_name,
            'product_description' => $this->product_description,
            'images' => $this->images->map(function ($image) {
                return [
                    'product_image_id' => $image->product_image_id,
                    'image' => $image->image,
                    'created_at' => $image->created_at,
                    'updated_at' => $image->updated_at,
                ];
            }),
            'productPrices' => $this->prices->map(function ($price) {
                return [
                    'size' => [
                        'size_id' => $price->size?->size_id,
                        'size_name' => $price->size?->size, // Assuming 'size' is the field name
                    ],
                    'color' => [
                        'color_id' => $price->color?->color_id,
                        'color_name' => $price->color?->color_name,
                        'color_code' => $price->color?->color_code,
                    ],
                    'price' => $price->price,
                    'stock_qty' => $price->stock_qty,
                    'returnPoint' => $price->return_points,
                    'is_promotion' => $price->is_promotion,
                    'promotion_price' => $price->promotion_price,
                ];
            }),
            'created_at' => $this->created_at,
        ];
    }
}
