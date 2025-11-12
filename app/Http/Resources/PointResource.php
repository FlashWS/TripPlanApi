<?php

namespace App\Http\Resources;

use App\Models\Point;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PointResource extends JsonResource
{
    /**
     * Class Point
     *
     * @mixin Point
     * */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'address' => $this->address,
            'location' => $this->location,
            'note' => $this->note,
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'created_at' => (string) $this->created_at,
            'updated_at' => (string) $this->updated_at,
        ];
    }
}
