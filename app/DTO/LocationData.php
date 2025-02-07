<?php

namespace App\DTO;

use Spatie\LaravelData\Data;

class LocationData extends Data
{
    public function __construct(
        public float $longitude,
        public float $latitude,
    ) {
    }
}
