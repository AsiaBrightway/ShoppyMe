<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'sub_category_id' => $this->sub_category_id, // Correcting sub_category_id to match your expected output
            'sub_category_name' => $this->sub_category_name,
            'image' => $this->image,
            'updated_at' => $this->updated_at,
            'created_at' => $this->created_at,
            'category' => [
                'category_id' => $this->category?->category_id,
                'category_name' => $this->category?->category_name,
                'image' => $this->category?->image,
                'created_at' => $this->category?->created_at,
                'updated_at' => $this->category?->updated_at,
            ]
        ];
    }
}
