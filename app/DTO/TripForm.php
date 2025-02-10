<?php

namespace App\DTO;

use Spatie\LaravelData\Data;

class TripForm extends Data
{
    public function __construct(
        public string $name,
        public ?string $date_start,
        public ?string $date_end,
        public ?string $note,
    ) {
    }
}
