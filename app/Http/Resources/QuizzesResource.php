<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QuizzesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = Auth::guard('localAuth')->user();
        if(!$user){
            return ['error' => 'Authentication failed'];
        }

        $response = $this->responses()->where('id', '=', $user->id)->first();
        $quizQuestion = $this->quizQuestion()->first();

        return [
            'answers' => new QuizAnswersCollection($quizQuestion->answers()->get()),
            'question' => $quizQuestion->question,
            'correctAnswer' => $quizQuestion->correct_answer,
            'explanation' => $quizQuestion->explanation,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
            'publishedAt' => $this->published_at,
            'date' => $this->date,
            'locale' => $quizQuestion->locale,
            'response' => !empty($response) ? new QuizResponseResource($response): null,
            'streak' => new QuizStreaksResource($user->quiz_streak),
            'uuid' => $this->uuid,
        ];
    }
}
