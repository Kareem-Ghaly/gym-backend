<?php

namespace App\Http\Controllers;
use app\Models\User;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\ApiResource;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Register (User or Coach)
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            $user = $this->authService->register($data);

            // If registering as coach → pending approval → no token
            if ($data['role'] === 'coach') {
                return ApiResource::success([
                    'status' => 'pending_approval',
                    'user' => new UserResource($user),
                ], 'Coach account created and pending admin approval.');
            }

            // If a normal user → login immediately
            $token = $user->createToken('auth_token')->plainTextToken;

            return ApiResource::success([
                'user' => new UserResource($user->load('roles')),
                'token' => $token,
            ], 'Registration successful');
        } catch (\Exception $e) {
            return ApiResource::error('Registration failed: ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * Login
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login(
            $request->validated()['email'],
            $request->validated()['password']
        );

        // Invalid credentials
        if (!$result) { 
            return ApiResource::error('Invalid credentials', null, 401);
        }

        // Error from service (role mismatch, not approved, etc.)
        if (isset($result['error'])) {
            return ApiResource::error($result['error'], null, 403);
        }

        return ApiResource::success([
            'user' => new UserResource($result['user']),
            'token' => $result['token'],
        ], 'Login successful');
    }

    public function logout(): JsonResponse
    {
        $this->authService->logout(auth()->user());
        return ApiResource::success(null, 'Logout successful');
    }

    public function me(): JsonResponse
    {
        return ApiResource::success(
            new UserResource(auth()->user()->load('roles')),
            'User retrieved successfully'
        );
    }
    
}
