<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{

    public function index(Request $request)
    {
        $users = User::query()
            ->when($request->input('search'), function($query, $search) {
                $query->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
            })
            ->paginate($request->input('per_page', 15));

        return UserResource::collection($users);
    }


    public function show(User $user)
    {
        return new UserResource($user);
    }

    public function update(Request $request, User $user)
    {
        DB::beginTransaction();
        try {
            $validatedData = $request->validate([
                'first_name' => 'sometimes|string|max:255',
                'last_name' => 'sometimes|string|max:255',
                'email' => 'sometimes|email|unique:users,email,' . $user->id,
            ]);

            $user->update($validatedData);

            DB::commit();
            return new UserResource($user);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'User update failed',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(User $user)
    {
        DB::beginTransaction();
        try {
            $user->delete();

            DB::commit();
            return response()->json([
                'message' => 'User deleted successfully'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'User deletion failed',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function restore($id)
    {
        DB::beginTransaction();
        try {
            $user = User::withTrashed()->findOrFail($id);
            $user->restore();

            DB::commit();
            return new UserResource($user);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'User restoration failed',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
