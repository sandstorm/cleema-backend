<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NewsTagsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'value' => $this->value,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
            'publishedAt' => $this->published_at,
            'locale' => $this->locale,
            'uuid' => $this->uuid,
        ];
    }
}
