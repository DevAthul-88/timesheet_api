<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Attribute\StoreAttributeRequest;
use App\Models\Attribute;
use App\Http\Resources\AttributeResource;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class AttributeController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $attributes = Attribute::all();
            return response()->json(AttributeResource::collection($attributes));
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error retrieving attributes',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(StoreAttributeRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $validatedData = $request->validated();
            $attribute = Attribute::create($validatedData);

            DB::commit();
            return response()->json(new AttributeResource($attribute), Response::HTTP_CREATED);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error creating attribute',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(Attribute $attribute): JsonResponse
    {
        try {
            return response()->json(new AttributeResource($attribute));
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error retrieving attribute',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(StoreAttributeRequest $request, Attribute $attribute): JsonResponse
    {
        DB::beginTransaction();
        try {
            $validatedData = $request->validated();
            $attribute->update($validatedData);

            DB::commit();
            return response()->json(new AttributeResource($attribute));
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error updating attribute',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(Attribute $attribute): JsonResponse
    {
        DB::beginTransaction();
        try {
            $attribute->delete();

            DB::commit();
            return response()->json([
                'message' => 'Attribute deleted successfully'
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error deleting attribute',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
