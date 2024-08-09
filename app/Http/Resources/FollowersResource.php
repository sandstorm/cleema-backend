<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Ramsey\Uuid\Uuid;

class FollowersResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $isRequest = (bool)$this->pivot->is_request;
        return [
            'isRequest' => $isRequest,
            'user' => [
                'avatar' => new AvatarResource($this->avatar),
                'username' => $this->username,
                'uuid' => $this->uuid,
            ],
            'uuid' => $this->uuid,
        ];
    }
}
