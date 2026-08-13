<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Only admins can create staff/reader accounts via this endpoint
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in([
                User::ROLE_ADMIN, User::ROLE_EDITOR, User::ROLE_AUTHOR, User::ROLE_READER,
            ])],
            'status' => ['sometimes', Rule::in([
                User::STATUS_ACTIVE, User::STATUS_INACTIVE, User::STATUS_SUSPENDED,
            ])],
            'avatar' => ['nullable', 'image', 'max:2048'],
            'bio' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:20'],
            'designation' => ['nullable', 'string', 'max:255'],
        ];
    }

     /**
     * Default values before validation.
     */
    protected function prepareForValidation(): void
    {
        if (!$this->filled('status')) {
            $this->merge([
                'status' => User::STATUS_ACTIVE,
            ]);
        }
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'email.unique' => 'This email address is already registered.',
            'password.confirmed' => 'Password confirmation does not match.',
            'avatar.image' => 'Avatar must be an image.',
        ];
    }
}
