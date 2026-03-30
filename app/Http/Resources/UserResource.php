<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class UserResource extends ApiResource
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
            'name' => $this->name,
            'email' => $this->email,
            'is_approved' => $this->is_approved,
            'gender' => $this->gender,
            'roles' => $this->roles->pluck('name'),
            'created_at' => $this->created_at,
        ];
    }
}

