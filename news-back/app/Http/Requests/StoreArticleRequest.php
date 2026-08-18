<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // gate this in controller/middleware (role check) instead
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:articles,slug'],
            'sub_title' => ['nullable', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string'],
            'content' => ['required', 'string'],
            'featured_image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
            'category_id' => ['required', 'exists:categories,id'],
            'status' => ['nullable', Rule::in(['draft', 'pending', 'published', 'archived'])],
            'is_featured' => ['nullable', 'boolean'],
            'is_breaking' => ['nullable', 'boolean'],
            'published_at' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'শিরোনাম প্রদান করা আবশ্যক।',
            'content.required' => 'কনটেন্ট প্রদান করা আবশ্যক।',
            'category_id.required' => 'ক্যাটাগরি নির্বাচন করুন।',
            'category_id.exists' => 'নির্বাচিত ক্যাটাগরিটি সঠিক নয়।',
            'featured_image.image' => 'ছবিটি অবশ্যই একটি বৈধ ইমেজ ফাইল হতে হবে।',
            'featured_image.max' => 'ছবির সাইজ সর্বোচ্চ ২ মেগাবাইট হতে পারবে।',
        ];
    }
}