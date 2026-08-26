<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class FrontPageAdsResource extends JsonResource
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
            'sort_order'  => (int) $this->sort_order,
            'is_active'   => (bool) $this->is_active,
            'starts_at'   => $this->starts_at ? $this->starts_at->toIso8601String() : null,
            'ends_at'     => $this->ends_at ? $this->ends_at->toIso8601String() : null,
            'clicks'      => (int) $this->clicks,
            'created_at'  => $this->created_at ? $this->created_at->toIso8601String() : null,
        ];
    }
}