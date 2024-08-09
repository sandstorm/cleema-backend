<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'street' => $this->street,
            'housenumber' => $this->housenumber,
            'city' => $this->city,
            'zip' => $this->zip,
        ];
    }
}
