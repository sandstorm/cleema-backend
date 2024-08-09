<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SurveysResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            // TODO survey components (participants)
            'title' => $this->title,
            'description' => $this->description,
            'surveyUrl' => OffersResource::getURL($this->survey_url),
            'evaluationUrl' => OffersResource::getURL($this->evaluation_url),
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
            'publishedAt' => $this->published_at,
            'locale' => $this->locale,
            'target' => $this->target,
            'trophyProcessed' => $this->trophy_processed,
            'uuid' => $this->uuid,
            'finished' => $this->finished,
        ];
    }
}
