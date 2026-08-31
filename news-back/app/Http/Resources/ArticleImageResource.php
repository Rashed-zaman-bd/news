<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ArticleImageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'url'        => Storage::disk('public')->url($this->image_path),
            'caption'    => $this->caption,
            'sort_order' => $this->sort_order,
            'is_cover'   => $this->is_cover,
        ];
    }
}
