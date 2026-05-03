<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\ApiResource;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Exception;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Register a new User or Coach.
     * Checks if email is already registered.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            if (User::where('email', $data['email'])->exists()) {
                return ApiResource::error('The email address is already in use.', null, 422);
            }

            $user = $this->authService->register($data);

            // Case: Coach registration
            if (isset($data['role']) && $data['role'] === 'coach') {
                return ApiResource::success([
                    'status' => 'pending_approval',
                    'user' => new UserResource($user),
                ], 'Coach account created. Pending admin approval.', 201);
            }

            // Case: Regular User
            $token = $user->createToken('auth_token')->plainTextToken;

            return ApiResource::success([
                'user' => new UserResource($user->load('roles')),
                'token' => $token,
            ], 'User registered successfully.', 201);

        } catch (Exception $e) {
            return ApiResource::error(
                'Registration failed: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Login
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->login(
                $request->validated()['email'],
                $request->validated()['password']
            );

            if (!$result) { 
                return ApiResource::error('Invalid email or password.', null, 401);
            }

            if (isset($result['error'])) {
                return ApiResource::error($result['error'], null, 403);
            }

            return ApiResource::success([
                'user' => new UserResource($result['user']),
                'token' => $result['token'],
            ], 'Login successful.', 200);

        } catch (Exception $e) {
            return ApiResource::error('An error occurred during login.', null, 500);
        }
    }

    /**
     * Logout
     */
    public function logout(): JsonResponse
    {
        try {
            $user = Auth::user();
            if (!$user) return ApiResource::error('Unauthenticated.', null, 401);

            $this->authService->logout($user);
            return ApiResource::success(null, 'Logged out successfully.', 200);

        } catch (Exception $e) {
            return ApiResource::error('Logout failed.', null, 500);
        }
    }

    /**
     * Get Current User
     */
    public function me(): JsonResponse
    {
        $user = Auth::user();
        if (!$user) return ApiResource::error('Unauthenticated.', null, 401);

        return ApiResource::success(
            new UserResource($user->load('roles')),
            'User profile retrieved.',
            200
        );
    }
}