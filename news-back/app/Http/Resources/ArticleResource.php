<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ArticleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'title'          => $this->title,
            'slug'           => $this->slug,
            'sub_title'      => $this->sub_title,
            'excerpt'        => $this->excerpt,
            'content'        => $this->content,
            'featured_image' => $this->featured_image
                ? Storage::disk('public')->url($this->featured_image)
                : null,
            'status'         => $this->status,
            'is_featured'    => $this->is_featured,
            'is_breaking'    => $this->is_breaking,
            'views'          => $this->views,
            'published_at'   => $this->published_at?->toIso8601String(),
            'created_at'     => $this->created_at?->toIso8601String(),
            'updated_at'     => $this->updated_at?->toIso8601String(),

            // Relationships
            'category'     => $this->whenLoaded('category', fn () => new CategoryResource($this->category)),
            'sub_category' => $this->whenLoaded('subCategory', fn () => $this->subCategory
                ? new CategoryResource($this->subCategory) : null),
            'author'   => $this->whenLoaded('author', fn () => [
                'id'   => $this->author->id,
                'name' => $this->author->name,
            ]),
            'editor'   => $this->whenLoaded('editor', fn () => $this->editor ? [
                'id'   => $this->editor->id,
                'name' => $this->editor->name,
            ] : null),

        ];
    }
}