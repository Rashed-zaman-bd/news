<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAdvertisementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // gate this in middleware/policy as needed
    }

    public function rules(): array
    {
        return [
            'image'      => ['required', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:2048'],
            'name'       => ['required', 'string', 'max:255'],
            'provider'   => ['required', 'string', 'max:255'],
            'link_url'   => ['nullable', 'url', 'max:2048'],
            'placement'  => ['required', 'in:top,middle,middle-two,middle-three,sidebar,sidebar-two'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active'  => ['nullable', 'boolean'],
            'starts_at'  => ['nullable', 'date'],
            'ends_at'    => ['nullable', 'date', 'after_or_equal:starts_at'],
        ];
    }

    public function messages(): array
    {
        return [
            'image.required'     => 'বিজ্ঞাপনের ছবি আবশ্যক।',
            'image.image'        => 'অনুগ্রহ করে একটি বৈধ ছবি ফাইল আপলোড করুন।',
            'image.mimes'        => 'শুধুমাত্র jpg, jpeg, png বা webp ফরম্যাট সমর্থিত।',
            'image.max'          => 'ছবির সর্বোচ্চ আকার ২ মেগাবাইট।',
            'name.required'      => 'বিজ্ঞাপনের নাম আবশ্যক।',
            'provider.required'  => 'বিজ্ঞাপনদাতার নাম আবশ্যক।',
            'link_url.url'       => 'সঠিক URL প্রদান করুন।',
            'placement.required' => 'বিজ্ঞাপনের অবস্থান নির্বাচন করুন।',
            'placement.in'       => 'অবস্থান অবশ্যই top, middle অথবা sidebar এর একটি হতে হবে।',
            'ends_at.after_or_equal' => 'শেষের তারিখ শুরুর তারিখের পরে হতে হবে।',
        ];
    }
}
