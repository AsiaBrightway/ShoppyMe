<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeliveryChargesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'delivery_charges_id' => $this->delivery_charges_id,
            'township_id' => $this->township_id,
            'delivery_fee' => $this->delivery_fee,
            'created_at' => $this->created_at
        ];
    }
}
