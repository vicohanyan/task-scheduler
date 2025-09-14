<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Enums\TaskStatusEnum;

class TaskUpdateRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'             => ['sometimes','string','max:255'],
            'description'      => ['sometimes','nullable','string','max:2000'],
            'status'           => ['sometimes', Rule::enum(TaskStatusEnum::class)],
            'priority'         => ['sometimes','integer','between:0,255'],
            'assigned_user_id' => [
                'sometimes','nullable','integer',
                Rule::exists('users','id')->where('available', true)
            ],
            'due_date'         => ['sometimes','nullable','date'],
        ];
    }
}
