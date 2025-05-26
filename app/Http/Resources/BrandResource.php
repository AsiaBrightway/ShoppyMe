<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BrandResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'brand_id' => $this->brand_id,
            'brand_name' => $this->brand_name,
            'brand_description' => $this->brand_description,
            'image' => $this->image,
            'created_at' => $this->created_at
        ];
    }
}