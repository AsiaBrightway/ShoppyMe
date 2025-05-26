<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'user_id' => $this->user_id,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            // 'store_id' => $this->store_id,
            'is_active' => $this->is_active,
            'email_verified_at' => $this->email_verified_at,
            // 'remember_token' => $this->remember_token,
            'created_at' => $this->created_at,
        ];
    }
}
