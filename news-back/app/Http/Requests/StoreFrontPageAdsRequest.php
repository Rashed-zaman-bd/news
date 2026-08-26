<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFrontPageAdsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public static function placements(): array
    {
        return [
            'top', 'middle', 'middle-two', 'middle-three', 'middle-four',
            'middle-five', 'middle-six', 'middle-seven', 'middle-eight',
            'middle-nine', 'middle-ten', 'sidebar', 'sidebar-two',
            'sidebar-three', 'sidebar-four', 'sidebar-five', 'sidebar-six',
        ];
    }

    public function rules(): array
    {
        return [
            'image' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp,gif',
                'max:2048',
            ],
            'name'       => ['nullable', 'string', 'max:255'],
            'provider'   => ['nullable', 'string', 'max:255'],
            'link_url'   => ['nullable', 'url', 'max:2048'],
            'placement'  => [
                'required',
                Rule::in(static::placements()),
            ],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active'  => ['nullable', 'boolean'],
            'starts_at'  => ['nullable', 'date'],
            'ends_at'    => ['nullable', 'date', 'after_or_equal:starts_at'],
        ];
    }

    public function messages(): array
    {
        return [
            'image.required'         => 'বিজ্ঞাপনের ছবি আবশ্যক।',
            'image.image'            => 'অনুগ্রহ করে একটি বৈধ ছবি ফাইল আপলোড করুন।',
            'image.mimes'            => 'শুধুমাত্র jpg, jpeg, png, webp অথবা gif ফরম্যাট সমর্থিত।',
            'image.max'              => 'ছবির সর্বোচ্চ আকার ২ মেগাবাইট।',
            'name.string'            => 'বিজ্ঞাপনের নাম সঠিক নয়।',
            'provider.string'        => 'বিজ্ঞাপনদাতার নাম সঠিক নয়।',
            'link_url.url'           => 'সঠিক URL প্রদান করুন।',
            'placement.required'     => 'বিজ্ঞাপনের অবস্থান নির্বাচন করুন।',
            'placement.in'           => 'নির্বাচিত বিজ্ঞাপনের অবস্থান সঠিক নয়।',
            'sort_order.integer'     => 'অর্ডার অবশ্যই সংখ্যা হতে হবে।',
            'sort_order.min'         => 'অর্ডার ০ বা তার বেশি হতে হবে।',
            'is_active.boolean'      => 'Active status সঠিক নয়।',
            'starts_at.date'         => 'শুরুর তারিখ সঠিক নয়।',
            'ends_at.date'           => 'শেষের তারিখ সঠিক নয়।',
            'ends_at.after_or_equal' => 'শেষের তারিখ শুরুর তারিখের পরে হতে হবে।',
        ];
    }
}