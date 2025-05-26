<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductPriceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'product_price_id' => $this->product_image_id,
            'product_id' => $this->product_id,
            'size_id' => $this->size_id,
            'color_id' => $this->color_id,
            'price' => $this->price,
            'stock_qty' => $this->stock_qty,
            'return_points' => $this->return_points,
            'is_promotion' => $this->is_promotion,
            'promotion_price' => $this->promotion_price,
            'created_at' => $this->created_at
        ];
    }
}
