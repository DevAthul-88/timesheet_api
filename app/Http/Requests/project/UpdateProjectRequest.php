<?php

namespace App\Http\Requests\Project;

use App\Models\Attribute;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'sometimes',
                'string',
                'max:100',
                Rule::unique('projects', 'name')->ignore($this->route('project'))
            ],
            'status' => 'sometimes|in:pending,active,completed,in_progress,cancelled',
            'description' => 'nullable|string',
            'attributes' => 'nullable|array',
        ] + $this->getAttributeValidationRules();
    }

    protected function getAttributeValidationRules(): array
    {
        $rules = [];

        $projectAttributes = Attribute::where('entity_type', 'App\Models\Project')->get();

        foreach ($projectAttributes as $attribute) {
            $key = "attributes.{$attribute->name}";

            $ruleSet = ['nullable'];
            switch ($attribute->type) {
                case 'integer':
                    $ruleSet[] = 'integer';
                    break;
                case 'float':
                    $ruleSet[] = 'numeric';
                    break;
                case 'date':
                    $ruleSet[] = 'date';
                    break;
                case 'string':
                    $ruleSet[] = 'string';
                    break;
            }

            $options = json_decode($attribute->options, true);
            if ($options && is_array($options)) {
                $ruleSet[] = Rule::in($options);
            }

            $rules[$key] = $ruleSet;
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.string' => 'The project name must be a string.',
            'name.max' => 'The project name must not exceed 100 characters.',
            'name.unique' => 'A project with this name already exists.',
            'status.in' => 'The status must be one of: pending, active, completed, in_progress, cancelled.',
            'description.string' => 'The description must be a string.',
        ];
    }
}
