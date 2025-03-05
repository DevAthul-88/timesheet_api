<?php

namespace App\Http\Requests\Attribute;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Attribute;

class StoreAttributeRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('attributes', 'name')
                    ->where('entity_type', $this->input('entity_type'))
            ],
            'type' => [
                'required',
                Rule::in([
                    Attribute::TYPE_TEXT,
                    Attribute::TYPE_DATE,
                    Attribute::TYPE_NUMBER,
                    Attribute::TYPE_SELECT,
                    Attribute::TYPE_BOOLEAN
                ])
            ],
            'description' => 'nullable|string|max:1000',
            'options' => [
                'nullable',
                'array'
            ],
            'is_required' => 'boolean',
            'entity_type' => [
                'required',
                'string',
                'max:255'
            ]
        ];
    }

    public function messages()
    {
        return [
            'name.unique' => 'An attribute with this name already exists for the selected entity type.',
            'type.in' => 'Invalid attribute type selected.',
            'options.array' => 'Options must be a valid array.',
            'is_required.boolean' => 'Is required must be a boolean value.',
        ];
    }
}
