<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RegionsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
            'publishedAt' => $this->published_at,
            'uuid' => $this->uuid,
        ];
    }
}
