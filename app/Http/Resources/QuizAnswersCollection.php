<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class QuizAnswersCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return \Illuminate\Support\Collection
     */
    public function toArray(Request $request): \Illuminate\Support\Collection
    {
        return $this->collection;
    }
}
