<?php

namespace App\Http\Resources;

use App\Models\Point;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TripResource extends JsonResource
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
            'date_start' => (string) $this->date_start,
            'date_end' => (string) $this->date_end,
            'days' => $this->days,
            'note' => $this->note,
            'created_at' => (string) $this->created_at,
            'updated_at' => (string) $this->updated_at,
        ];
    }
}
