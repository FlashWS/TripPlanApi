<?php

namespace App\Observers;

use App\Models\TripPoint;
use App\Traits\UserIdTrait;

class TripPointObserver
{
    use UserIdTrait;
    public function creating(TripPoint $tripPoint): void
    {
        $this->setUserId($tripPoint);
    }
}
