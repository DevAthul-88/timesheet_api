<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AttributeValue;
use App\Http\Requests\StoreAttributeValueRequest;
use Illuminate\Http\Request;

class AttributeValueController extends Controller
{
    public function store(StoreAttributeValueRequest $request)
    {
        $attributeValue = AttributeValue::create($request->validated());
        return response()->json($attributeValue, 201);
    }

    public function getEntityAttributes($entityType, $entityId)
    {
        $values = AttributeValue::where('entity_type', $entityType)
                                ->where('entity_id', $entityId)
                                ->with('attribute')
                                ->get();

        return response()->json($values);
    }
}
