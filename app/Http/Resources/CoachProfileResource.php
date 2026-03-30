<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class CoachProfileResource extends ApiResource
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
            'bio' => $this->bio,
            'specialization' => $this->specialization,
            'experience_years' => $this->experience_years,
            'certification' => $this->certification,
            'rating' => (float) $this->rating,
            'total_clients' => $this->total_clients,
        ];
    }
}

