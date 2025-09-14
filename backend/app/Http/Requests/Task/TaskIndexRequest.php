<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Enums\TaskStatusEnum;

class TaskIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'min:1', 'max:255'],
            'status' => ['nullable', Rule::enum(TaskStatusEnum::class)],
            'assigned_user_id' => ['nullable', 'integer'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $search = $this->input('search');
        $this->merge([
            'search' => $this->filled('search') ? trim((string)$search) : null,
        ]);
    }
}

