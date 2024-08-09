<?php

namespace App\Http\Resources;

use App\Models\Challenges;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ChallengesCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return ['data' => $this->collection->sortByDesc(function ($challenge) {
            return $challenge->start_date;
        })->values()];
    }
}
