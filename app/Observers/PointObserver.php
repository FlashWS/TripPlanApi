<?php

namespace App\Observers;

use App\Models\Point;
use App\Traits\UserIdTrait;

class PointObserver
{
    use UserIdTrait;
    public function creating(Point $point): void
    {
        dump($point);
        $this->setUserId($point);
    }
}
