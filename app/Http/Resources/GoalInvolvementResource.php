<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GoalInvolvementResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // TODO currentParticipants
        return [
            'currentParticipants' => $this->current_participants ?? 0,
            'maxParticipants' => $this->max_participants ?? 0,
        ];
    }
}
