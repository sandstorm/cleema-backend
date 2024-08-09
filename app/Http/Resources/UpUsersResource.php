<?php

namespace App\Http\Resources;

use App\Models\UpUsers;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class UpUsersResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = Auth::guard('localAuth')->user();
        $following = $user->follows()->count();
        $followers = $user->followers()->count();

        return [
            'acceptsSurveys' => $this->accepts_surveys,
            'avatar' => new AvatarResource($this->avatar),
            'createdAt' => $this->created_at,
            'email' => $this->email,
            'confirmed' => $this->confirmed,
            'isSupporter' => $this->is_supporter,
            'provider' => $this->provider,
            // TODO
            "follows"=> [
                "followRequests"=> 0,
                "followers"=> $followers,
                "following"=> $following,
                "followingPending"=> 0
            ],
            'referralCode' => $this->referral_code,
            'username' => $this->username ?? 'anonymous',
            'region' => $this->region ? new RegionsResource($this->region) : null,
            'updatedAt' => $this->updated_at,
            'uuid' => $user->uuid ?? $this->uuid,
        ];
    }
}
