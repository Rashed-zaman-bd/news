<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateVideoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Get the video ID from the route parameter
        $videoId = $this->route('video')?->id ?? $this->route('video');

        return [
            'title'       => 'required|string|max:255',
            'slug'        => ['nullable', 'string', 'max:255', Rule::unique('videos', 'slug')->ignore($videoId)],
            'thumbnail'   => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            'video_url'   => 'nullable|url|max:255',
            'description' => 'nullable|string',
            'is_active'   => 'boolean',
        ];
    }
}