<?php

namespace App\Http\Resources;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class JoinedChallengesAnswersCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     * @return \Illuminate\Support\Collection
     */
    public function toArray(Request $request): \Illuminate\Support\Collection
    {
        return $this->collection;
    }
}
