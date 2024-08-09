<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuizStreaksResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'participationStreak' => $this->participation_streak,
            'maxCorrectAnswerStreak' => $this->max_correct_answer_streak,
            'correctAnswerStreak' => $this->correctAnswerStreak,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}
