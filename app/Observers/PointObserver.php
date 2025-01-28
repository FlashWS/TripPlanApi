<?php

namespace App\Observers;

use App\Models\Point;
use App\Traits\UserIdTrait;

class PointObserver
{
    use UserIdTrait;
    public function created(Point $point): void
    {
        $this->setUserId($point);
    }
}
