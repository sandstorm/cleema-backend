<?php

namespace App\Http\Resources;

use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class ProjectsResource extends JsonResource
{
    /**
     * Indicates if the resource's collection keys should be preserved.
     *
     * @var bool
     */
    public $preserveKeys = true;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = Auth::guard('localAuth')->user();
        $isFaved = $user ? $user->projectsFavorited->contains('id', $this->id) : null;
        $isJoined = $user ? $user->projectsJoined->contains('id', $this->id) : null;

        return [
            'title' => $this->title,
            'summary' => $this->summary ?? '',
            'description' => $this->description ?? '',
            'startDate' => $this->start_date ?? null,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
            'publishedAt' => $this->published_at,
            'locale' => $this->locale,
            'goalType' => $this->goal_type,
            'phase' => $this->phase,
            'conclusion' => $this->conclusion,
            'uuid' => $this->uuid,
            'region' => new RegionsResource($this->region),
            'partner' => new PartnersResource($this->partner),
            // TODO: fix goalInvolvementResource && fully integrate goalInvolvement
            /*'goalInvolvement' => new GoalInvolvementResource($this->goalInvolvement) ?? [
                    'currentParticipants' => 0,
                    'maxParticipants' => 0,
                ],*/
            'goalInvolvement' => [
                    'currentParticipants' => 0,
                    'maxParticipants' => 0,
                ],
            'goalFunding' => $this->goalFunding,
            'location' => new LocationResource($this->location),
            'image' => new FilesResource($this->image),
            "teaserImage"=> new FilesResource($this->teaserImage),
            'relatedProjects' => $this->relatedProjects,
            'isFaved' => $isFaved,
            'joined' => $isJoined,
        ];
    }
}
