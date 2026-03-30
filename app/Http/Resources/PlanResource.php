<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class PlanResource extends ApiResource
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
            'type' => $this->type,
            'title' => $this->title,
            'description' => $this->description,
            // 'content' => $this->content,
            'daily_calories' => $this->daily_calories,
            'is_free' => $this->is_free,
            'coach' => new UserResource($this->whenLoaded('coach')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}


