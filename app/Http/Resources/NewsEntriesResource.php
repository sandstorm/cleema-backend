<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class NewsEntriesResource extends JsonResource
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

        $isFaved = $user ? $user->favoritedNewsEntries->contains('id', $this->id) : false;
        return [
            'title' => $this->title ?? '',
            'description' => $this->description ?? '',
            'date' => $this->date,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
            'publishedAt' => $this->published_at,
            'locale' => $this->locale,
            'uuid' => $this->uuid,
            'teaser' => $this->teaser,
            'type' => $this->type,
            'region' => new RegionsResource($this->region),
            'tags' => new NewsTagsCollection($this->tags),
            'isFaved' => $isFaved,
            'image' => new FilesResource($this->image),
        ];
    }
}
