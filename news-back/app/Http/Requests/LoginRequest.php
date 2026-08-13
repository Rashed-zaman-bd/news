<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'phone' => [
                'required',
                'string',
                'regex:/^01[3-9]\d{8}$/',
            ],
            'password' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.required' => 'ফোন নম্বর দেওয়া আবশ্যক।',
            'phone.regex' => '১১-সংখ্যার একটি বৈধ বাংলাদেশী ফোন নম্বর প্রদান করুন।',
            'password.required' => 'পাসওয়ার্ড দেওয়া আবশ্যক।',
        ];
    }
}