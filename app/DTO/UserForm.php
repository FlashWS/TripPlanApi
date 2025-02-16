<?php

namespace App\DTO;

use Spatie\LaravelData\Data;

class UserForm extends Data
{
    public function __construct(
        public string $name,
        public string $email,
    ) {
    }
}
