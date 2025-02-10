<?php

namespace App\DTO;

use Spatie\LaravelData\Data;

class TagForm extends Data
{
    public function __construct(
        public string $name,
        public string $icon,
        public ?string $color,
    ) {
    }
}
