<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAttributeValueRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'attribute_id' => 'required|exists:attributes,id',
            'entity_id' => 'required|integer',
            'entity_type' => 'required|string',
            'value' => 'required|string',
        ];
    }
}
