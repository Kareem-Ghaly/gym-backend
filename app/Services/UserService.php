<?php

namespace App\Services;

use App\Models\User;
use App\Models\Plan;
use App\Models\VipRequest;
use App\Models\UserProfile;
use Illuminate\Support\Facades\DB;

class UserService
{
    /**
     * Get free workout plans
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getFreeWorkoutPlans()
    {
        return Plan::where('type', 'workout')
            ->where('is_free', true)
            ->with('coach')
            ->latest()
            ->get();
    }

    /**
     * Get free diet plans
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getFreeDietPlans()
    {
        return Plan::where('type', 'diet')
            ->where('is_free', true)
            ->with('coach')
            ->latest()
            ->get();
    }

    /**
     * Upgrade user to VIP
     *
     * @param User $user
     * @param array $data
     * @return VipRequest
     */
    public function upgradeToVip(User $user, array $data): VipRequest
    {
        return DB::transaction(function () use ($user, $data) {
            // Create or update user profile
            $profile = $user->userProfile()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'age' => $data['age'],
                    'height' => $data['height'],
                    'weight' => $data['weight'],
                    'goal' => $data['goal'],
                    'activity_level' => $data['activity_level'],
                    'allergies' => $data['allergies'] ?? null,
                ]
            );

            // Create VIP request
            $vipRequest = VipRequest::create([
                'user_id' => $user->id,
                'coach_id' => $data['coach_id'] ?? null,
                'age' => $data['age'],
                'height' => $data['height'],
                'weight' => $data['weight'],
                'goal' => $data['goal'],
                'activity_level' => $data['activity_level'],
                'allergies' => $data['allergies'] ?? null,
                'status' => 'pending',
            ]);

            return $vipRequest;
        });
    }

    /**
     * Get all coaches
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getCoaches()
    {
        return User::role('coach')
            ->where('is_approved', true)
            ->with('coachProfile')
            ->get();
    }

    /**
     * Get user's accepted coach plan
     *
     * @param User $user
     * @return array|null
     */
    public function getAcceptedCoachPlan(User $user): ?array
    {
        $profile = $user->userProfile;

        if (!$profile || !$profile->is_vip || !$profile->coach_id) {
            return null;
        }

        // Find accepted VIP request for this user
        // We check by user_id, status accepted, and coach_id matches the profile coach_id
        $vipRequest = VipRequest::where('user_id', $user->id)
            ->where('status', 'accepted')
            ->where('coach_id', $profile->coach_id)
            ->latest()
            ->first();

        if (!$vipRequest) {
            // If no vip_request found, try to find plans directly by user_id and coach_id
            $dietPlan = Plan::where('user_id', $user->id)
                ->where('coach_id', $profile->coach_id)
                ->where('type', 'diet')
                ->where('is_free', false)
                ->latest()
                ->first();

            $workoutPlan = Plan::where('user_id', $user->id)
                ->where('coach_id', $profile->coach_id)
                ->where('type', 'workout')
                ->where('is_free', false)
                ->latest()
                ->first();

            if (!$dietPlan && !$workoutPlan) {
                return null;
            }

            $coach = User::find($profile->coach_id);
            
            return [
                'diet_plan' => $dietPlan,
                'workout_plan' => $workoutPlan,
                'coach' => $coach,
            ];
        }

        $dietPlan = Plan::where('vip_request_id', $vipRequest->id)
            ->where('type', 'diet')
            ->first();

        $workoutPlan = Plan::where('vip_request_id', $vipRequest->id)
            ->where('type', 'workout')
            ->first();

        return [
            'diet_plan' => $dietPlan,
            'workout_plan' => $workoutPlan,
            'coach' => $vipRequest->coach,
        ];
    }
}


