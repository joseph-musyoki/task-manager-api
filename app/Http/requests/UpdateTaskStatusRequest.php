<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTaskStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $taskId = $this->route('task') ?? $this->route('id');

        return [
            'title' => [
                'required',
                'string',
                'max:255',
                // Same uniqueness rule: title + due_date, but ignore current task
                Rule::unique('tasks')->where(function ($query) {
                    return $query->where('due_date', $this->input('due_date'));
                })->ignore($taskId),
            ],
            'due_date' => [
                'required',
                'date',
                'date_format:Y-m-d',
                'after_or_equal:today',
            ],
            'priority' => [
                'required',
                Rule::in(['low', 'medium', 'high']),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'title.unique'            => 'A task with this title already exists for the given due date.',
            'due_date.after_or_equal' => 'The due date must be today or a future date.',
            'priority.in'             => 'Priority must be one of: low, medium, high.',
        ];
    }
}