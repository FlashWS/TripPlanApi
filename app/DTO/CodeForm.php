<?php

namespace App\DTO;

use Spatie\LaravelData\Data;

class CodeForm extends Data
{
    public function __construct(
        public string $email,
    ) {
    }
}
