<?php

namespace App\Http\Resources;


use App\Models\JoinedChallenges;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JoinedChallengeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'answers' => $this->answers ? new JoinedChallengesAnswersCollection($this->answers) : [],
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
            //'challenge' => $this->challenge->id,
        ];
    }
}
