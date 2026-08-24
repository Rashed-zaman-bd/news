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
            'thumbnail'   => 'required|image|mimes:jpeg,jpg,png,webp|max:2048', // Max 2MB
            'video_url'   => 'nullable|url|max:255',
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
