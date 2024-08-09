<?php

namespace App\Http\Resources;

use App\Models\ChallengeImages;
use App\Models\Files;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChallengeImage extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "uuid" => $this->uuid ?? "11111111-1111-1111-1111-111111111111",
            "createdAt"=> $this->createdAt,
            "updatedAt"=> $this->updated_at,
            "image" => [
                "name"=> $this->name,
                "alternativeText"=> $this->alternative_text,
                "caption"=> $this->caption,
                "width"=> $this->width,
                "height"=> $this->height,
                "formats"=> json_decode($this->formats),
                "hash"=> $this->hash,
                "ext"=> $this->ext,
                "mime"=> $this->mime,
                "size"=> floatval($this->size),
                "url" => 'storage/'.$this->url ?? 'uploads'.$this->folder_path.'/'.$this->name,
                "previewUrl"=> $this->previewUrl,
                "provider"=> $this->provider,
                "provider_metadata"=> $this->provider_metadata,
                "createdAt"=> $this->createdAt,
                "updatedAt"=> $this->updated_at,
                //"folderPath"=> $this->folder_path,
            ]
        ];
    }
}