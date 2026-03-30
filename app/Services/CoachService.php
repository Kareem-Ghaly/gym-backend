<?php

namespace App\Services;

use App\Models\User;
use App\Models\VipRequest;
use App\Models\Plan;
use App\Models\UserProfile;
use Illuminate\Support\Facades\DB;

class CoachService
{
    protected CalorieCalculationService $calorieService;

    public function __construct(CalorieCalculationService $calorieService)
    {
        $this->calorieService = $calorieService;
    }

    /**
     * Get pending VIP requests for coach
     *
     * @param User $coach
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPendingVipRequests(User $coach)
    {
        return VipRequest::where(function ($query) use ($coach) {
                $query->where('coach_id', $coach->id)
                      ->orWhereNull('coach_id');
            })
            ->where('status', 'pending')
            ->with('user')
            ->latest()
            ->get();
    }

    /**
     * Accept VIP request and create plans
     *
     * @param User $coach
     * @param int $vipRequestId
     * @param array $dietPlanData
     * @param array $workoutPlanData
     * @return array
     */
    public function acceptVipRequest(
        User $coach,
        int $vipRequestId,
        array $dietPlanData,
        array $workoutPlanData
    ): array {
        return DB::transaction(function () use ($coach, $vipRequestId, $dietPlanData, $workoutPlanData) {
            $vipRequest = VipRequest::where(function ($query) use ($coach) {
                    $query->where('coach_id', $coach->id)
                          ->orWhereNull('coach_id');
                })
                ->where('id', $vipRequestId)
                ->where('status', 'pending')
                ->firstOrFail();
            
            // Update coach_id in vip_request if it was null
            if (!$vipRequest->coach_id) {
                $vipRequest->update(['coach_id' => $coach->id]);
            }

            // Calculate daily calories
            $user = $vipRequest->user;
            $isMale = $user->gender === 'male';
            $dailyCalories = $this->calorieService->calculateDailyCalories(
                $vipRequest->weight,
                $vipRequest->height,
                $vipRequest->age,
                $isMale,
                $vipRequest->activity_level,
                $vipRequest->goal
            );

            // Create diet plan
            $dietPlan = Plan::create([
                'user_id' => $vipRequest->user_id,
                'coach_id' => $coach->id,
                'vip_request_id' => $vipRequest->id,
                'type' => 'diet',
                'title' => $dietPlanData['title'],
                'description' => $dietPlanData['description'] ?? null,
                // 'content' => $dietPlanData['content'],
                'daily_calories' => $dailyCalories,
                'is_free' => false,
            ]);

            // Create workout plan
            $workoutPlan = Plan::create([
                'user_id' => $vipRequest->user_id,
                'coach_id' => $coach->id,
                'vip_request_id' => $vipRequest->id,
                'type' => 'workout',
                'title' => $workoutPlanData['title'],
                'description' => $workoutPlanData['description'] ?? null,
                // 'content' => $workoutPlanData['content'],
                'is_free' => false,
            ]);

            // Update VIP request status
            $vipRequest->update(['status' => 'accepted']);

            // Update user profile
            $userProfile = UserProfile::updateOrCreate(
                ['user_id' => $vipRequest->user_id],
                [
                    'age' => $vipRequest->age,
                    'height' => $vipRequest->height,
                    'weight' => $vipRequest->weight,
                    'goal' => $vipRequest->goal,
                    'activity_level' => $vipRequest->activity_level,
                    'allergies' => $vipRequest->allergies,
                    'is_vip' => true,
                    'coach_id' => $coach->id,
                ]
            );

            return [
                'diet_plan' => $dietPlan,
                'workout_plan' => $workoutPlan,
                'daily_calories' => $dailyCalories,
            ];
        });
    }

    /**
     * Decline VIP request
     *
     * @param User $coach
     * @param int $vipRequestId
     * @return void
     */
    public function declineVipRequest(User $coach, int $vipRequestId): void
    {
        $vipRequest = VipRequest::where(function ($query) use ($coach) {
                $query->where('coach_id', $coach->id)
                      ->orWhereNull('coach_id');
            })
            ->where('id', $vipRequestId)
            ->where('status', 'pending')
            ->firstOrFail();

        // Update coach_id if it was null, then decline
        if (!$vipRequest->coach_id) {
            $vipRequest->update([
                'coach_id' => $coach->id,
                'status' => 'declined'
            ]);
        } else {
            $vipRequest->update(['status' => 'declined']);
        }
    }

    /**
     * Update plan
     *
     * @param User $coach
     * @param int $planId
     * @param array $data
     * @return Plan
     */
    public function updatePlan(User $coach, int $planId, array $data): Plan
    {
        $plan = Plan::where('coach_id', $coach->id)
            ->where('id', $planId)
            ->firstOrFail();

        $plan->update($data);

        // Recalculate calories if it's a diet plan and metrics changed
        if ($plan->type === 'diet' && $plan->vipRequest) {
            $vipRequest = $plan->vipRequest;
            $user = $vipRequest->user;
            $isMale = $user->gender === 'male';
            $dailyCalories = $this->calorieService->calculateDailyCalories(
                $vipRequest->weight,
                $vipRequest->height,
                $vipRequest->age,
                $isMale,
                $vipRequest->activity_level,
                $vipRequest->goal
            );
            $plan->update(['daily_calories' => $dailyCalories]);
        }

        return $plan->fresh();
    }

    /**
     * Regenerate plan (recalculate calories and update)
     *
     * @param User $coach
     * @param int $planId
     * @param array $data
     * @return Plan
     */
    public function regeneratePlan(User $coach, int $planId, array $data): Plan
    {
        $plan = Plan::where('coach_id', $coach->id)
            ->where('id', $planId)
            ->firstOrFail();

        // Recalculate calories if it's a diet plan
        if ($plan->type === 'diet' && $plan->vipRequest) {
            $vipRequest = $plan->vipRequest;
            $user = $vipRequest->user;
            $isMale = $user->gender === 'male';
            $dailyCalories = $this->calorieService->calculateDailyCalories(
                $vipRequest->weight,
                $vipRequest->height,
                $vipRequest->age,
                $isMale,
                $vipRequest->activity_level,
                $vipRequest->goal
            );
            $data['daily_calories'] = $dailyCalories;
        }

        $plan->update($data);

        return $plan->fresh();
    }
}


