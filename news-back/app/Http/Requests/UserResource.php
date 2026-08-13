<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'status' => $this->status,
            'avatar_url' => $this->avatar ? asset('storage/' . $this->avatar) : null,
            'bio' => $this->bio,
            'phone' => $this->phone,
            'designation' => $this->designation,
            'is_subscribed' => $this->is_subscribed,
            'subscription_expires_at' => $this->subscription_expires_at?->toDateTimeString(),
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}
