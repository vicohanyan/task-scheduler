<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UserIndexRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'available' => ['nullable','boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('available')) {
            $this->merge(['available' => filter_var($this->input('available'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)]);
        }
    }
}
