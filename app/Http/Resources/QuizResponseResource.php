<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuizResponseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $date = Carbon::parse($this->pivot->date);
        return [
          'answer' => $this->pivot->answer,
          'date' => $date,
        ];
    }
}
