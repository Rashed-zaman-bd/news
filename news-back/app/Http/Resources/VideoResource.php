<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class VideoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'title'       => $this->title,
            'slug'        => $this->slug,
            'thumbnail'   => $this->thumbnail ? Storage::url($this->thumbnail) : null,
            'video_url'   => $this->video_url,
            'description' => $this->description,
            'is_active'   => (bool) $this->is_active,
            'created_at'  => $this->created_at->toDateTimeString(),
        ];
    }
}
