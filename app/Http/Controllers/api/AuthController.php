<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $existingUser = $this->authService->findUserByEmail($request->email);

            if ($existingUser) {
                return response()->json([
                    'message' => 'User with this email already exists.',
                ], 409);
            }

            $user = $this->authService->registerUser($request->validated());
            $token = $user->createToken('auth_token')->accessToken;
            DB::commit();

            return response()->json([
                'message' => 'User registered successfully',
                'user' => $user,
                'access_token' => $token,
                'token_type' => 'Bearer',
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Registration failed: ' . $e->getMessage());
            return response()->json([
                'message' => 'Registration failed. Please try again.',
            ], 500);
        }
    }


    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $credentials = $request->only('email', 'password');

            if (!Auth::attempt($credentials)) {
                return response()->json([
                    'message' => 'Invalid login credentials',
                ], 401);
            }

            $user = Auth::user();
            $token = $user->createToken('auth_token')->accessToken;

            return response()->json([
                'message' => 'Login successful',
                'user' => $user,
                'access_token' => $token,
                'token_type' => 'Bearer',
            ]);
        } catch (Exception $e) {
            Log::error('Login failed: ' . $e->getMessage());
            return response()->json([
                'message' => 'Login failed. Please try again.',
            ], 500);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        try {
            $request->user()->token()->revoke();
            return response()->json([
                'message' => 'Successfully logged out',
            ]);
        } catch (Exception $e) {
            Log::error('Logout failed: ' . $e->getMessage());
            return response()->json([
                'message' => 'Logout failed. Please try again.',
            ], 500);
        }
    }

    public function user(Request $request): JsonResponse
    {
        try {
            return response()->json($request->user());
        } catch (Exception $e) {
            Log::error('Fetching user failed: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to fetch user details.',
            ], 500);
        }
    }
}
