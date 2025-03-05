<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AttributeResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'description' => $this->description,
            'options' => $this->when($this->options, function () {
                return is_array($this->options) ? $this->options : json_decode($this->options, true) ?? [];
            }, []),
            'is_required' => (bool)$this->is_required,
            'meta' => [
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
                'options_count' => is_array($this->options) ? count($this->options) : count(json_decode($this->options, true) ?? []),
            ],
        ];

    }
}
