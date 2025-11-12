<?php

namespace App\DTO;

use Spatie\LaravelData\Data;

class TripPointForm extends Data
{
    public function __construct(
        public string $point_uuid,
        public int $day,
        public ?string $time = null,
        public int $order = 0,
        public ?string $note = null,
    ) {
    }
}
