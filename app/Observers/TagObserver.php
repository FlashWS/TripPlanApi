<?php

namespace App\Observers;

use App\Models\Tag;
use App\Traits\UserIdTrait;

class TagObserver
{
    use UserIdTrait;

    public function creating(Tag $tag): void
    {
        $this->setUserId($tag);
    }
}
