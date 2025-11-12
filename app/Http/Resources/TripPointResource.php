<?php

namespace App\Http\Resources;

use App\Models\TripPoint;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class TripPointResource extends JsonResource
{
    /**
     * Class TripPoint
     *
     * @mixin TripPoint
     * */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'trip_uuid' => $this->trip_uuid,
            'point_uuid' => $this->point_uuid,
            'day' => $this->day,
            'time' => $this->time,
            'order' => $this->order,
            'note' => $this->note,
            'point' => new PointResource($this->whenLoaded('point')),
            'created_at' => (string) $this->created_at,
            'updated_at' => (string) $this->updated_at,
        ];
    }
}
