<?php

namespace App\Services;

use App\Models\User;
use App\Models\Post;
use App\Models\Plan;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Request;

class AdminService
{
    /**
     * Approve coach
     *
     * @param int $coachId
     * @return User
     */
    public function approveCoach(int $coachId) : User
    {
        $coach = User::role('coach')
            ->where('id', $coachId)
            ->firstOrFail();

        $coach->update(['is_approved' => true]);

        return $coach;
    }

    /**
     * Decline coach
     *
     * @param int $coachId
     * @return User
     */
    public function declineCoach(int $coachId): User
    {
        $coach = User::role('coach')
            ->where('id', $coachId)
            ->firstOrFail();

        $coach->update(['is_approved' => false]);
        
        return $coach;
    }

    /**
     * Get pending coaches
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPendingCoaches()
    {
        return User::role('coach')
            ->where('is_approved', false)
            ->with('coachProfile')
            ->latest()
            ->get();
    }

    /**
     * Approve post
     *
     * @param int $postId
     * @return Post
     */
    public function approvePost(int $postId): Post
    {
        $post = Post::findOrFail($postId);
        $post->update(['status' => 'approved']);

        return $post;
    }

    /**
     * Decline post
     *
     * @param int $postId
     * @return Post
     */
    public function declinePost(int $postId): Post
    {
        $post = Post::findOrFail($postId);
        $post->update(['status' => 'rejected']);

        return $post;
    }

    /**
     * Get pending posts
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPendingPosts()
    {
        return Post::where('status', 'pending')
            ->with('user')
            ->latest()
            ->get();
    }

    /**
     * Create free workout plan
     *
     * @param array $data
     * @return Plan
     */
    public function createFreeWorkoutPlan(array $data): Plan
    {
        return Plan::create([
            'type' => 'workout',
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'is_free' => true,
        ]);
    }

    /**
     * Create free diet plan
     *
     * @param array $data
     * @return Plan
     */
    public function createFreeDietPlan(array $data): Plan
    {
        return Plan::create([
            'type' => 'diet',
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'daily_calories' => $data['daily_calories'] ?? null,
            'is_free' => true,
        ]);
    }
}

