<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AboutsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'content' => $this->content,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
            'publishedAt' => $this->published_at,
            'locale' => $this->locale,
        ];
    }
}
