<?php

namespace App\Http\Controllers;

use App\Http\Requests\Coach\AcceptVipRequestRequest;
use App\Http\Requests\Coach\UpdatePlanRequest;
use App\Http\Resources\ApiResource;
use App\Http\Resources\PlanResource;
use App\Http\Resources\VipRequestResource;
use App\Services\CoachService;
use Illuminate\Http\JsonResponse;

class CoachController extends Controller
{
    protected CoachService $coachService;

    public function __construct(CoachService $coachService)
    {
        $this->coachService = $coachService;
    }

    /**
     * Get pending VIP requests
     */
    public function getPendingVipRequests(): JsonResponse
    {
        $requests = $this->coachService->getPendingVipRequests(auth()->user());

        return ApiResource::success(
            VipRequestResource::collection($requests),
            'Pending VIP requests retrieved successfully'
        );
    }

    /**
     * Accept VIP request
     */
    public function acceptVipRequest(AcceptVipRequestRequest $request, int $vipRequestId): JsonResponse
    {
        try {
            $result = $this->coachService->acceptVipRequest(
                auth()->user(),
                $vipRequestId,
                $request->validated()['diet_plan'],
                $request->validated()['workout_plan']
            );

            return ApiResource::success([
                'diet_plan' => new PlanResource($result['diet_plan']),
                'workout_plan' => new PlanResource($result['workout_plan']),
                'daily_calories' => $result['daily_calories'],
            ], 'VIP request accepted and plans created successfully');
        } catch (\Exception $e) {
            return ApiResource::error('Failed to accept VIP request: ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * Decline VIP request
     */
    public function declineVipRequest(int $vipRequestId): JsonResponse
    {
        try {
            $this->coachService->declineVipRequest(auth()->user(), $vipRequestId);

            return ApiResource::success(null, 'VIP request declined successfully');
        } catch (\Exception $e) {
            return ApiResource::error('Failed to decline VIP request: ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * Update plan
     */
    public function updatePlan(UpdatePlanRequest $request, int $planId): JsonResponse
    {
        try {
            $plan = $this->coachService->updatePlan(
                auth()->user(),
                $planId,
                $request->validated()
            );

            return ApiResource::success(
                new PlanResource($plan),
                'Plan updated successfully'
            );
        } catch (\Exception $e) {
            return ApiResource::error('Failed to update plan: ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * Regenerate plan
     */
    public function regeneratePlan(UpdatePlanRequest $request, int $planId): JsonResponse
    {
        try {
            $plan = $this->coachService->regeneratePlan(
                auth()->user(),
                $planId,
                $request->validated()
            );

            return ApiResource::success(
                new PlanResource($plan),
                'Plan regenerated successfully'
            );
        } catch (\Exception $e) {
            return ApiResource::error('Failed to regenerate plan: ' . $e->getMessage(), null, 500);
        }
    }
}

