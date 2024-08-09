<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AvatarResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "createdAt"=> $this->created_at ?? null,
            'image' => new FilesResource($this->image),
            "locale"=> $this->locale ?? null,
            "publishedAt"=> $this->published_at ?? null,
            "updatedAt"=> $this->updated_at ?? null,
            "uuid"=> $this->uuid ?? null,
        ];
    }
}
