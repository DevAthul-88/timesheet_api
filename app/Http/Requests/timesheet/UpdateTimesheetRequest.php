<?php

namespace App\Http\Requests\Timesheet;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTimesheetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'task_name' => 'sometimes|string|max:255',
            'date' => 'sometimes|date',
            'hours' => 'sometimes|integer|min:1|max:24',
        ];
    }

    public function messages(): array
    {
        return [
            'task_name.string' => 'The task name must be a string.',
            'task_name.max' => 'The task name may not be greater than 255 characters.',

            'date.date' => 'The date must be a valid date.',

            'hours.integer' => 'The hours must be an integer.',
            'hours.min' => 'The hours must be at least 1.',
            'hours.max' => 'The hours may not be greater than 24.',
        ];
    }
}
