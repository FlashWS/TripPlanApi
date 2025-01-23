<?php

namespace App\DTO;

use Spatie\LaravelData\Data;

class UserForm extends Data
{
    public function __construct(
        public string $name,
        public string $email,
        public string $gender,
        public string $birthday,
        public string $weight_initial,
        public string $weight_current,
        public string $weight_desired,
        public string $height,
        public string $started_at,
        public string $finished_at,
    ) {
    }
}
