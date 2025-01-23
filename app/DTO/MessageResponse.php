<?php

namespace App\DTO;

use Spatie\LaravelData\Data;

class MessageResponse extends Data
{
    public function __construct(
        public string $message,
    ) {
    }
}
