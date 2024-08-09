<?php

namespace App\Http\Resources;

use App\Models\Challenges;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class TrophiesResource extends JsonResource
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

        $date = $user->trophies()->where('id', '=', $this->id)->first()->pivot->date;
        $notified = $user->trophies()->where('id', '=', $this->id)->first()->pivot->notified;
        return [
            'createdAt' => $this->created_at,
            'date' => Carbon::parse($date),
            'notified' => (bool)$notified,
            'trophy' => [
                'amount' => $this->amount ?? 0,
                'createdAt' => $this->created_at,
                'image' => new FilesResource($this->image),
                'kind' => $this->kind,
                'locale' => $this->locale,
                'publishedAt' => $this->published_at,
                'title' => $this->title,
                'uuid' => $this->uuid,
            ],
            'updatedAt' => $this->updated_at,
        ];
    }

}
