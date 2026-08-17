<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $categoryId = $this->route('category')?->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('categories', 'slug')->ignore($categoryId)],
            'icon' => ['nullable', 'string', 'max:255'],
            'parent_id' => [
                'nullable',
                'exists:categories,id',
                Rule::notIn([$categoryId]), // can't be its own parent
            ],
            'order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'ক্যাটাগরির নাম আবশ্যক।',
            'slug.unique' => 'এই স্লাগটি ইতিমধ্যে ব্যবহৃত হয়েছে।',
            'parent_id.not_in' => 'একটি ক্যাটাগরি নিজের প্যারেন্ট হতে পারে না।',
        ];
    }
}