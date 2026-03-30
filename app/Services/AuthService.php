<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AuthService
{
    /**
     * Register a new user
     *
     * @param array $data
     * @return User
     */
    public function register(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'is_approved' => $data['role'] === 'user', // Users are auto-approved, coaches need approval
                'gender' => $data['gender'] ?? null,
            ]);

            // Assign role
            $user->assignRole($data['role']);

            // Create profile based on role
            if ($data['role'] === 'coach') {
                $user->coachProfile()->create([
                    'bio' => $data['bio'] ?? null,
                    'specialization' => $data['specialization'] ?? null,
                    'experience_years' => $data['experience_years'] ?? null,
                    'certification' => $data['certification'] ?? null,
                ]);
            } elseif ($data['role'] === 'user') {
                $user->userProfile()->create();
            }

            return $user;
        });
    }

    /**
     * Login user and create token
     *
     * @param string $email
     * @param string $password
     * @return array|null
     */
    public function login(string $email, string $password): ?array
    {
        $user = User::where('email', $email)->with('roles')->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return null;
        }

        // Check if user is approved (for coaches)
        if ($user->isCoach() && !$user->is_approved) {
            return ['error' => 'Your account is pending approval from admin.'];
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user->load('roles'),
            'token' => $token,
        ];
    }

    /**
     * Logout user
     *
     * @param User $user
     * @return void
     */
    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }
}

