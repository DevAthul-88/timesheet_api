<?php

namespace App\Http\Requests\Timesheet;

use Illuminate\Foundation\Http\FormRequest;

class StoreTimesheetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'project_id' => 'required|exists:projects,id',
            'task_name' => 'required|string|max:255',
            'date' => 'required|date',
            'hours' => 'required|integer|min:1|max:24',
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'The user ID is required.',
            'user_id.exists' => 'The selected user ID is invalid.',

            'project_id.required' => 'The project ID is required.',
            'project_id.exists' => 'The selected project ID is invalid.',

            'task_name.required' => 'The task name is required.',
            'task_name.string' => 'The task name must be a string.',
            'task_name.max' => 'The task name may not be greater than 255 characters.',

            'date.required' => 'The date is required.',
            'date.date' => 'The date must be a valid date.',

            'hours.required' => 'The hours field is required.',
            'hours.integer' => 'The hours must be an integer.',
            'hours.min' => 'The hours must be at least 1.',
            'hours.max' => 'The hours may not be greater than 24.',
        ];
    }
}
