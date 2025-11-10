<?php

namespace App\Observers;

use App\Models\Trip;
use App\Traits\UserIdTrait;

class TripObserver
{
    use UserIdTrait;

    public function creating(Trip $trip): void
    {
        $this->setUserId($trip);
    }
}
