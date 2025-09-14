<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Enums\TaskStatusEnum;
class TaskStoreRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'             => ['required','string','max:255'],
            'description'      => ['nullable','string','max:2000'],
            'status'           => ['nullable', Rule::enum(TaskStatusEnum::class)],
            'priority'         => ['nullable','integer','between:0,255'],
            'assigned_user_id' => [
                'nullable','integer',
                Rule::exists('users','id')->where('available', true)
            ],
            'due_date'         => ['nullable','date'],
        ];
    }
}
