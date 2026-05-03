<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\UpgradeToVipRequest;
use App\Http\Resources\ApiResource;
use App\Http\Resources\CoachResource;
use App\Http\Resources\PlanResource;
use App\Http\Resources\VipRequestResource;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Get free workout plans
     */
    public function getFreeWorkoutPlans(): JsonResponse
    {
        $plans = $this->userService->getFreeWorkoutPlans();

        return ApiResource::success(
            PlanResource::collection($plans),
            'Free workout plans retrieved successfully'
        );
    }

    /**
     * Get free diet plans
     */
    public function getFreeDietPlans(): JsonResponse
    {
        $plans = $this->userService->getFreeDietPlans();

        return ApiResource::success(
            PlanResource::collection($plans),
            'Free diet plans retrieved successfully'
        );
    }

    /**
     * Upgrade to VIP
     */
    public function upgradeToVip(UpgradeToVipRequest $request): JsonResponse
    {
        try {
            $vipRequest = $this->userService->upgradeToVip(
                auth()->user(),
                $request->validated()
            );

            return ApiResource::success(
                new VipRequestResource($vipRequest->load('coach')),
                'VIP request submitted successfully'
            );
        } catch (\Exception $e) {
            return ApiResource::error('Failed to upgrade to VIP: ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * Get all coaches
     */
    public function getCoaches(): JsonResponse
    {
        $coaches = $this->userService->getCoaches();

        return ApiResource::success(
            CoachResource::collection($coaches),
            'Coaches retrieved successfully'
        );
    }

    /**
     * Get accepted coach plan
     */
    public function getAcceptedCoachPlan(): JsonResponse
    {
        $plan = $this->userService->getAcceptedCoachPlan(auth()->user());

        if (!$plan) {
            return ApiResource::error('No accepted coach plan found', null, 204);
        }

        return ApiResource::success([
            'diet_plan' => $plan['diet_plan'] ? new PlanResource($plan['diet_plan']) : null,
            'workout_plan' => $plan['workout_plan'] ? new PlanResource($plan['workout_plan']) : null,
            'coach' => new CoachResource($plan['coach']->load('coachProfile')),
        ], 'Coach plan retrieved successfully');
    }
}

