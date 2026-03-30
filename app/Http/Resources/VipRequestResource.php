<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class VipRequestResource extends ApiResource
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
            'user' => new UserResource($this->whenLoaded('user')),
            'coach' => new UserResource($this->whenLoaded('coach')),
            'age' => $this->age,
            'height' => (float) $this->height,
            'weight' => (float) $this->weight,
            'goal' => $this->goal,
            'activity_level' => $this->activity_level,
            'allergies' => $this->allergies,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

