<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOpinionRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Route already gated behind auth:sanctum + role:admin,editor middleware
        return true;
    }

    public function rules(): array
    {
        return [
            'title'               => ['required', 'string', 'max:255'],
            'slug'                => ['nullable', 'string', 'max:255', 'unique:opinions,slug'],
            'writer_name'         => ['required', 'string', 'max:255'],
            'writer_designation'  => ['nullable', 'string', 'max:255'],
            'writer_image'        => ['nullable', 'image', 'max:2096'], // 2MB
            'text'                => ['required', 'string'],
            'image'               => ['nullable', 'image', 'max:4096'],
            'is_published'        => ['sometimes', 'boolean'],
            'published_at'        => ['nullable', 'date'],
            'sort_order'          => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'       => 'শিরোনাম আবশ্যক।',
            'writer_name.required' => 'লেখকের নাম আবশ্যক।',
            'text.required'        => 'মতামতের বিষয়বস্তু আবশ্যক।',
        ];
    }
}
