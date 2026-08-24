<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class StoreVideoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
             'title'       => 'required|string|max:255',
             'slug'        => 'nullable|string|max:255|unique:videos,slug',
             'thumbnail'   => 'required|image|mimes:jpeg,jpg,png,webp|max:2048',
             'video_type'  => 'required|in:upload,embed',
             'video'       => 'required_if:video_type,upload|file|mimes:mp4,mov,ogg,qt|max:102400', // 100MB
             'video_url'   => 'required_if:video_type,embed|nullable|url|max:255',
             'description' => 'nullable|string',
             'is_active'   => 'boolean',
            ];
    }

    protected function prepareForValidation(): void
    {
        // Auto-generate slug if left empty
        if (!$this->slug && $this->title) {
            $this->merge([
                'slug' => Str::slug($this->title),
            ]);
        }
    }
}
