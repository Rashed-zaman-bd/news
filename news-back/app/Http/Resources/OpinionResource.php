<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class OpinionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'title'               => $this->title,
            'slug'                => $this->slug,
            'writer_name'         => $this->writer_name,
            'writer_designation'  => $this->writer_designation,
            'writer_image'        => $this->writer_image
                ? Storage::disk('public')->url($this->writer_image)
                : null,
            'text'                => $this->text,
            'image'               => $this->image
                ? Storage::disk('public')->url($this->image)
                : null,
            'is_published'        => $this->is_published,
            'published_at'        => $this->published_at?->toIso8601String(),
            'sort_order'          => $this->sort_order,
            'created_at'          => $this->created_at?->toIso8601String(),
            'updated_at'          => $this->updated_at?->toIso8601String(),
        ];
    }
}
