<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'admin_id' => $this->admin_id,
            'user_name' => $this->user_name,
            // 'password' => $this->password,
            // 'store_id' => $this->store_id,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'store' => new StoreResource($this->store),
        ];
    }
}
