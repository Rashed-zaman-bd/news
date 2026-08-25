<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryAdsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // image optional on update — only replaced if a new file is sent
            'image'      => ['sometimes', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:2048'],
            'name'       => ['nullable', 'string', 'max:255'],
            'provider'   => ['nullable', 'string', 'max:255'],
            'link_url'   => ['nullable', 'url', 'max:2048'],
            'placement'  => ['sometimes', 'in:top,middle,middle-two,middle-three,sidebar,sidebar-two'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active'  => ['nullable', 'boolean'],
            'starts_at'  => ['nullable', 'date'],
            'ends_at'    => ['nullable', 'date', 'after_or_equal:starts_at'],
        ];
    }

    public function messages(): array
    {
        return [
            'image.image'    => 'অনুগ্রহ করে একটি বৈধ ছবি ফাইল আপলোড করুন।',
            'image.mimes'    => 'শুধুমাত্র jpg, jpeg, png বা webp ফরম্যাট সমর্থিত।',
            'image.max'      => 'ছবির সর্বোচ্চ আকার ২ মেগাবাইট।',
            'link_url.url'   => 'সঠিক URL প্রদান করুন।',
            'placement.in'   => 'অবস্থান অবশ্যই top, middle অথবা sidebar এর একটি হতে হবে।',
            'ends_at.after_or_equal' => 'শেষের তারিখ শুরুর তারিখের পরে হতে হবে।',
        ];
    }
}
