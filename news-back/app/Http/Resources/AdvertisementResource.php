<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class AdvertisementResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'image'       => $this->image ? Storage::disk('public')->url($this->image) : null,
            'name'        => $this->name,
            'provider'    => $this->provider,
            'link_url'    => $this->link_url,
            'placement'   => $this->placement,
            'sort_order'  => $this->sort_order,
            'is_active'   => $this->is_active,
            'starts_at'   => $this->starts_at?->toIso8601String(),
            'ends_at'     => $this->ends_at?->toIso8601String(),
            'clicks'      => $this->clicks,
            'created_at'  => $this->created_at?->toIso8601String(),
        ];
    }
}