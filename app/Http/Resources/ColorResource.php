<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ColorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'color_id' => $this->color_id,
            'color_name' => $this->color_name,
            'color_code' => $this->color_code,
            'created_at' => $this->created_at
        ];
    }
}