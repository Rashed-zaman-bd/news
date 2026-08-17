<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCategoryRequest extends FormRequest
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
                'parent_id' => [
                'nullable',
                'exists:categories,id',
                Rule::notIn([$this->route('category')?->id]),
            ],
        ];
    }
}
