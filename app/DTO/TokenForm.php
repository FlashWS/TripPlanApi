<?php

namespace App\DTO;

use Spatie\LaravelData\Data;

class TokenForm extends Data
{
    public function __construct(
        public string $email,
        public int $code,
        public string $device_name
    ) {
    }
}
