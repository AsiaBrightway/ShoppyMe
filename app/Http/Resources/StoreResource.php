<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        return [
            'store_id' => $this->store_id,
            'store_name' => $this->store_name,
            'owner_name' => $this->owner_name,
            'address' => $this->address,
            'phone_number' => $this->phone_number,
            'email' => $this->email,
            'created_at' => $this->created_at
        ];
    }
}
