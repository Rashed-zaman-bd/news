<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFrontPageAdsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'image' => [
                'sometimes',
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp,gif',
                'max:2048',
            ],
            'name'       => ['nullable', 'string', 'max:255'],
            'provider'   => ['nullable', 'string', 'max:255'],
            'link_url'   => ['nullable', 'url', 'max:2048'],
            'placement'  => [
                'sometimes',
                'required',
                Rule::in(StoreFrontPageAdsRequest::placements()),
            ],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active'  => ['nullable', 'boolean'],
            'starts_at'  => ['nullable', 'date'],
            'ends_at'    => ['nullable', 'date', 'after_or_equal:starts_at'],
        ];
    }

    public function messages(): array
    {
        return (new StoreFrontPageAdsRequest())->messages();
    }
}