<?php

namespace App\Http\Requests\Project;

use App\Models\Attribute;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:100|unique:projects',
            'status' => 'sometimes|in:pending,active,completed,in_progress,cancelled',
            'description' => 'nullable|string',
            'attributes' => 'nullable|array',
        ] + $this->getAttributeValidationRules();
    }

    protected function getAttributeValidationRules(): array
    {
        $rules = [];

        // Fetch all attributes for projects
        $projectAttributes = Attribute::where('entity_type', 'App\Models\Project')->get();

        foreach ($projectAttributes as $attribute) {
            $key = "attributes.{$attribute->name}";

            $rules[$key] = [
                'nullable',
                $this->getValidationRuleForAttributeType($attribute)
            ];
        }

        return $rules;
    }

    protected function getValidationRuleForAttributeType(Attribute $attribute)
    {
        switch ($attribute->type) {
            case 'integer':
                return 'integer';
            case 'float':
                return 'numeric';
            case 'date':
                return 'date';
            case 'string':
                $options = json_decode($attribute->options, true);
                return $options
                    ? Rule::in($options)
                    : 'string';
            default:
                return '';
        }
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The project name is required.',
            'name.unique' => 'A project with this name already exists.',
            'status.in' => 'The status must be one of: pending, active, completed, in_progress, cancelled.',
        ];
    }
}
