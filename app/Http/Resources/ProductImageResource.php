<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductImageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'product_image_id' => $this->product_image_id,
            'product_id' => $this->product_id,
            'image' => $this->image,
            'created_at' => $this->created_at
        ];
    }
}