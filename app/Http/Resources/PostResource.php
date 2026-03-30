<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class PostResource extends ApiResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'image' => $this->image ? asset('storage/' . $this->image) : null,
            'status' => $this->status,
            'user' => new UserResource($this->whenLoaded('user')),
            'likes_count' => $this->likes_count ?? $this->whenLoaded('likes', fn() => $this->likes->count()),
            'comments_count' => $this->comments_count ?? $this->whenLoaded('comments', fn() => $this->comments->count()),
            'is_liked' => $this->when(
                $request->user(),
                fn() => $this->isLikedBy($request->user()->id)
            ),
            'comments' => CommentResource::collection($this->whenLoaded('comments')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}


