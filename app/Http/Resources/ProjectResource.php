<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    public function toArray($request)
    {
        $data = is_array($this->resource) ? $this->resource : $this->resource->toArray();

        return [
            'id' => $data['id'] ?? null,
            'name' => $data['name'] ?? null,
            'status' => $data['status'] ?? null,
            'description' => $data['description'] ?? null,
            'created_at' => $data['created_at'] ?? null,
            'updated_at' => $data['updated_at'] ?? null,
            'users' => isset($data['users']) ? UserResource::collection($data['users']) : [],
            'timesheets' => isset($data['timesheets']) ? TimesheetResource::collection($data['timesheets']) : [],
            'attributes' => isset($data['attributeValues']) ? collect($data['attributeValues'])->map(function ($attributeValue) {
                $attribute = $attributeValue->attribute;
                return [
                    'name' => $attribute->name ?? null,
                    'value' => $attributeValue->value ?? null,
                    'options' => $this->decodeOptions($attribute->options),
                ];
            }) : null,
        ];
    }


    private function decodeOptions($options)
    {
        if (is_array($options)) {
            return $options;
        }
        if ($options) {
            $decodedOptions = json_decode($options, true);
            return is_array($decodedOptions) ? $decodedOptions : [];
        }

        return null;
    }

}
