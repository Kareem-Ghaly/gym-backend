<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\CreateFreePlanRequest;
use App\Http\Resources\ApiResource;
use App\Http\Resources\CoachResource;
use App\Http\Resources\PlanResource;
use App\Http\Resources\PostResource;
use App\Http\Resources\UserResource;
use App\Services\AdminService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    protected AdminService $adminService;

    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }

    /**
     * Get pending coaches
     */
    public function getPendingCoaches(): JsonResponse
    {
        $coaches = $this->adminService->getPendingCoaches();

        return ApiResource::success(
            CoachResource::collection($coaches),
            'Pending coaches retrieved successfully'
        );
    }

    /**
     * Approve coach
     */
    public function approveCoach(int $coachId): JsonResponse
    {
        try {
            $coach = $this->adminService->approveCoach($coachId);

            return ApiResource::success(
                new CoachResource($coach->load('coachProfile')),
                'Coach approved successfully'
            );
        } catch (\Exception $e) {
            return ApiResource::error('Failed to approve coach: ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * Decline coach
     */
    public function declineCoach(int $coachId): JsonResponse
    {
        try {
            $coach = $this->adminService->declineCoach($coachId);

            return ApiResource::success(
                new CoachResource($coach->load('coachProfile')),
                'Coach declined successfully'
            );
        } catch (\Exception $e) {
            return ApiResource::error('Failed to decline coach: ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * Get pending posts
     */
    public function getPendingPosts(): JsonResponse
    {
        $posts = $this->adminService->getPendingPosts();

        return ApiResource::success(
            PostResource::collection($posts),
            'Pending posts retrieved successfully'
        );
    }

    /**
     * Approve post
     */
    public function approvePost(int $postId): JsonResponse
    {
        try {
            $post = $this->adminService->approvePost($postId);

            return ApiResource::success(
                new PostResource($post->load('user')),
                'Post approved successfully'
            );
        } catch (\Exception $e) {
            return ApiResource::error('Failed to approve post: ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * Decline post
     */
    public function declinePost(int $postId): JsonResponse
    {
        try {
            $post = $this->adminService->declinePost($postId);

            return ApiResource::success(
                new PostResource($post->load('user')),
                'Post declined successfully'
            );
        } catch (\Exception $e) {
            return ApiResource::error('Failed to decline post: ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * Create free workout plan
     */
    public function createFreeWorkoutPlan(CreateFreePlanRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            
            // Ensure content is properly formatted
            // if (isset($data['content'])) {
            //         if (is_string($data['content'])) {
            //         $data['content'] = json_decode($data['content'], true);
            //         if (json_last_error() !== JSON_ERROR_NONE || !is_array($data['content'])) {
            //             return ApiResource::error('Invalid JSON format in content field', null, 422);
            //         }
            //     }
            // //Convert all content items to strings
            //     if (is_array($data['content'])) {
            //         $data['content'] = array_map(function($item) {
            //             return (string) $item;
            //         }, $data['content']);
            //     }
            // }
            
            $plan = $this->adminService->createFreeWorkoutPlan($data);

            return ApiResource::success(
                new PlanResource($plan),
                'Free workout plan created successfully'
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            return ApiResource::error('Validation failed', $e->errors(), 422);
        } catch (\Exception $e) {
            Log::error('Failed to create workout plan: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'data' => $data ?? null
            ]);
            return ApiResource::error('Failed to create workout plan: ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * Create free diet plan
     */
    public function createFreeDietPlan(CreateFreePlanRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            
            // Ensure content is properly formatted
            // if (isset($data['content'])) {
            //     if (is_string($data['content'])) {
            //         $data['content'] = json_decode($data['content'], true);
            //         if (json_last_error() !== JSON_ERROR_NONE || !is_array($data['content'])) {
            //             return ApiResource::error('Invalid JSON format in content field', null, 422);
            //         }
            //     }
            //     // Convert all content items to strings
            //     if (is_array($data['content'])) {
            //         $data['content'] = array_map(function($item) {
            //             return (string) $item;
            //         }, $data['content']);
            //     }
            // }
            
            $plan = $this->adminService->createFreeDietPlan($data);

            return ApiResource::success(
                new PlanResource($plan),
                'Free diet plan created successfully'
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            return ApiResource::error('Validation failed', $e->errors(), 422);
        } catch (\Exception $e) {
            Log::error('Failed to create diet plan: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'data' => $data ?? null
            ]);
            return ApiResource::error('Failed to create diet plan: ' . $e->getMessage(), null, 500);
        }
    }
}

