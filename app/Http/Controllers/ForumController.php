<?php

namespace App\Http\Controllers;

use App\Http\Requests\Forum\CreateCommentRequest;
use App\Http\Requests\Forum\CreatePostRequest;
use App\Http\Resources\ApiResource;
use App\Http\Resources\CommentResource;
use App\Http\Resources\PostResource;
use App\Services\ForumService;
use Illuminate\Http\JsonResponse;

class ForumController extends Controller
{
    protected ForumService $forumService;

    public function __construct(ForumService $forumService)
    {
        $this->forumService = $forumService;
    }

    /**
     * Create a new post
     */
    public function createPost(CreatePostRequest $request): JsonResponse
    {
        try {
            $post = $this->forumService->createPost(
                auth()->id(),
                $request->validated(),
                $request->file('image')
            );

            return ApiResource::success(
                new PostResource($post->load('user')),
                'Post created successfully and pending approval'
            );
        } catch (\Exception $e) {
            return ApiResource::error('Failed to create post: ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * Get all approved posts
     */
    public function getPosts(): JsonResponse
    {
        $posts = $this->forumService->getApprovedPosts();

        return ApiResource::success(
            PostResource::collection($posts),
            'Posts retrieved successfully'
        );
    }

    /**
     * Get single post
     */
    public function getPost(int $postId): JsonResponse
    {
        try {
            $post = $this->forumService->getPost($postId);

            return ApiResource::success(
                new PostResource($post),
                'Post retrieved successfully'
            );
        } catch (\Exception $e) {
            return ApiResource::error('Post not found', null, 404);
        }
    }

    /**
     * Create comment
     */
    public function createComment(CreateCommentRequest $request, int $postId): JsonResponse
    {
        try {
            $comment = $this->forumService->createComment(
                auth()->id(),
                $postId,
                $request->validated()['content']
            );

            return ApiResource::success(
                new CommentResource($comment),
                'Comment created successfully'
            );
        } catch (\Exception $e) {
            return ApiResource::error('Failed to create comment: ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * Toggle like on post
     */
    public function toggleLike(int $postId): JsonResponse
    {
        try {
            $result = $this->forumService->toggleLike(auth()->id(), $postId);

            return ApiResource::success($result, 'Like toggled successfully');
        } catch (\Exception $e) {
            return ApiResource::error('Failed to toggle like: ' . $e->getMessage(), null, 500);
        }
    }
}


