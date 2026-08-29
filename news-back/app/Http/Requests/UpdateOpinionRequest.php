<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOpinionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $opinion = $this->route('opinion');

        return [
            'title'               => ['sometimes', 'required', 'string', 'max:255'],
            'slug'                => [
                'sometimes', 'nullable', 'string', 'max:255',
                Rule::unique('opinions', 'slug')->ignore($opinion?->id),
            ],
            'writer_name'         => ['sometimes', 'required', 'string', 'max:255'],
            'writer_designation'  => ['nullable', 'string', 'max:255'],
            'writer_image'        => ['nullable', 'image', 'max:2096'],
            'text'                => ['sometimes', 'required', 'string'],
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
