<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine whether the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $authenticatedUser = $this->user();

        if (!$authenticatedUser) {
            return false;
        }

        // Admin can update any user.
        if ($authenticatedUser->isAdmin()) {
            return true;
        }

        // Normal users can only update themselves.
        $targetUser = $this->route('user');

        // For /api/me there is no {user} route parameter.
        if (!$targetUser) {
            return true;
        }

        return $authenticatedUser->is($targetUser);
    }

    /**
     * Validation rules.
     */
    public function rules(): array
    {
        $authenticatedUser = $this->user();
        $targetUser = $this->route('user');

        // Determine target ID (Admin editing target user OR current user editing self via /me)
        $userId = $targetUser?->id ?? $authenticatedUser?->id;
        $isAdmin = $authenticatedUser?->isAdmin() ?? false;

        return [
            'name' => ['sometimes', 'string', 'max:255'],

            'email' => [
                'sometimes',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],

            'phone' => [
                'sometimes',
                'nullable',
                'string',
                'regex:/^01[3-9]\d{8}$/',
                Rule::unique('users', 'phone')->ignore($userId),
            ],

            'password' => [
                'sometimes',
                'required',
                'string',
                'min:8',
                'confirmed',
            ],

            /*
            |--------------------------------------------------------------------------
            | Admin Only Fields
            |--------------------------------------------------------------------------
            */
            'role' => [
                'sometimes',
                Rule::in([
                    User::ROLE_ADMIN,
                    User::ROLE_EDITOR,
                    User::ROLE_AUTHOR,
                    User::ROLE_READER,
                ]),
                Rule::prohibitedIf(!$isAdmin),
            ],

            'status' => [
                'sometimes',
                Rule::in([
                    User::STATUS_ACTIVE,
                    User::STATUS_INACTIVE,
                    User::STATUS_SUSPENDED,
                ]),
                Rule::prohibitedIf(!$isAdmin),
            ],

            'is_subscribed' => [
                'sometimes',
                'boolean',
                Rule::prohibitedIf(!$isAdmin),
            ],

            'subscription_expires_at' => [
                'sometimes',
                'nullable',
                'date',
                Rule::prohibitedIf(!$isAdmin),
            ],

            /*
            |--------------------------------------------------------------------------
            | Profile Fields
            |--------------------------------------------------------------------------
            */
            'avatar' => [
                'sometimes',
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

            'bio' => ['sometimes', 'nullable', 'string'],
            'designation' => ['sometimes', 'nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'email.unique' => 'এই ইমেলটি ইতিমধ্যে ব্যবহার করা হয়েছে।',
            'phone.unique' => 'এই ফোন নম্বরটি ইতিমধ্যে ব্যবহার করা হয়েছে।',
            'phone.regex' => '১১-সংখ্যার একটি বৈধ বাংলাদেশী ফোন নম্বর প্রদান করুন।',
            'password.min' => 'পাসওয়ার্ডটি অন্তত ৮ অক্ষরের হতে হবে।',
            'password.confirmed' => 'পাসওয়ার্ড নিশ্চিতকরণ মিলছে না।',
            'avatar.image' => 'আপলোড করা ফাইলটি একটি বৈধ ছবি হতে হবে।',
        ];
    }
}