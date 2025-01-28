<?php

namespace App\DTO;

use Spatie\LaravelData\Data;

class PointForm extends Data
{
    public function __construct(
        public string $name,
        public string $address,
        public string $location,
    ) {
    }
}
