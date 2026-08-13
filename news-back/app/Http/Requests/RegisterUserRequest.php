<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisterUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', Password::defaults()],
            'phone' => [
                'nullable', 
                'string', 
                'regex:/^01[3-9]\d{8}$/', 
                'unique:users,phone'
            ],
            'bio' => ['nullable', 'string'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'designation' => ['nullable', 'string', 'max:255'],
            // Standard registration allows READER or AUTHOR (defaults to READER)
            'role' => ['sometimes', Rule::in([User::ROLE_READER, User::ROLE_AUTHOR])],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'এই ইমেলটি ইতিমধ্যে নিবন্ধিত।',
            'phone.unique' => 'এই ফোন নম্বরটি ইতিমধ্যেই নিবন্ধিত।',
            'phone.regex' => 'ফোন নম্বরটি অবশ্যই ০১৩-০১৯ দিয়ে শুরু হওয়া একটি বৈধ ১১-সংখ্যার বাংলাদেশী নম্বর হতে হবে।',
            'avatar.image' => 'ছবিটি অবশ্যই jpeg, png, jpg বা webp ফরম্যাটের ফাইল হতে হবে।',
        ];
    }
}