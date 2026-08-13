<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        $target = $this->route('user');

        // Admins can edit anyone; users can edit their own profile
        return $this->user()?->isAdmin() || $this->user()?->is($target);
    }

    public function rules(): array
    {
        $userId = $this->route('user')?->id;

        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'password' => ['sometimes', 'string', 'min:8', 'confirmed'],

            // Only an admin should be able to touch role/status —
            // enforce that here rather than trusting the frontend.
            'role' => [
                'sometimes',
                Rule::in([User::ROLE_ADMIN, User::ROLE_EDITOR, User::ROLE_AUTHOR, User::ROLE_READER]),
                Rule::prohibitedIf(fn () => !$this->user()?->isAdmin()),
            ],
            'status' => [
                'sometimes',
                Rule::in([User::STATUS_ACTIVE, User::STATUS_INACTIVE, User::STATUS_SUSPENDED]),
                Rule::prohibitedIf(fn () => !$this->user()?->isAdmin()),
            ],

            'avatar' => ['nullable', 'image', 'max:2048'],
            'bio' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:20'],
            'designation' => ['nullable', 'string', 'max:255'],
            'is_subscribed' => ['sometimes', 'boolean'],
            'subscription_expires_at' => ['nullable', 'date'],
        ];
    }
}
