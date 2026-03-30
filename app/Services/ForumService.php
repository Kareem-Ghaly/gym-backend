<?php

namespace App\Services;

use App\Models\Post;
use App\Models\User;
use App\Models\Comment;
use App\Models\Like;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ForumService
{
    /**
     * Create a new post
     *
     * @param int $userId
     * @param array $data
     * @param \Illuminate\Http\UploadedFile|null $image
     * @return Post
     */
    public function createPost(int $userId, array $data, $image = null): Post
    {
        return DB::transaction(function () use ($userId, $data, $image) {
            $imagePath = null;
            $user=User::findOrfail($userId);
            if ($image) {
                $imagePath = $image->store('posts', 'public');
            }
            return Post::create([
                'user_id' => $userId,
                'title' => $data['title'],
                'content' => $data['content'],
                'image' => $imagePath,
                'status' => $user->isAdmin() ? 'approved' : 'pending',
            ]);
        });
    }

    /**
     * Get approved posts
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getApprovedPosts()
    {
        return Post::where('status', 'approved')
            ->with(['user', 'likes', 'comments.user'])
            ->withCount(['likes', 'comments'])
            ->latest()
            ->get();
    }

    /**
     * Get single post
     *
     * @param int $postId
     * @return Post
     */
    public function getPost(int $postId): Post
    {
        return Post::where('status', 'approved')
            ->with(['user', 'likes.user', 'comments.user'])
            ->withCount(['likes', 'comments'])
            ->findOrFail($postId);
    }

    /**
     * Create comment
     *
     * @param int $userId
     * @param int $postId
     * @param string $content
     * @return Comment
     */
    public function createComment(int $userId, int $postId, string $content): Comment
    {
        return DB::transaction(function () use ($userId, $postId, $content) {
            $post = Post::findOrFail($postId);

            $comment = Comment::create([
                'post_id' => $postId,
                'user_id' => $userId,
                'content' => $content,
            ]);

            // Update comments count
            $post->increment('comments_count');

            return $comment->load('user');
        });
    }

    /**
     * Toggle like on post
     *
     * @param int $userId
     * @param int $postId
     * @return array
     */
    public function toggleLike(int $userId, int $postId): array
    {
        return DB::transaction(function () use ($userId, $postId) {
            $post = Post::findOrFail($postId);

            $like = Like::where('post_id', $postId)
                ->where('user_id', $userId)
                ->first();

            if ($like) {
                // Unlike
                $like->delete();
                $post->decrement('likes_count');
                $liked = false;
            } else {
                // Like
                Like::create([
                    'post_id' => $postId,
                    'user_id' => $userId,
                ]);
                $post->increment('likes_count');
                $liked = true;
            }

            return [
                'liked' => $liked,
                'likes_count' => $post->fresh()->likes_count,
            ];
        });
    }
        public function DeletePost(int $userId, int $postId): array
    {
        return DB::transaction(function () use ($userId, $postId) {
            $post = Post::findOrFail($postId);

            $post = Post::where('post_id', $postId)
                ->where('user_id', $userId)
                ->delete();
        });}
                    

}

