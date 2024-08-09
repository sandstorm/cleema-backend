<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChallengeTemplatesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'interval' => $this->interval,
            'isPublic' => $this->is_public,
            'kind' => $this->kind,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
            'publishedAt' => $this->published_at,
            'goalType' => $this->goal_type,
            'teaserText' => $this->teaser_text,
            'goalMeasurement' => new GoalTypeMeasurementResource($this->goalTypeMeasurement),
            'goalSteps' => new GoalTypeStepsResource($this->goalTypeSteps),
            'partner' => $this->partner ? new PartnersResource($this->partner) : null,
            'image' => $this->image ? new ChallengeImage($this->image) : null,
        ];
    }
}
