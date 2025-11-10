<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;

trait UserIdTrait
{
    public function setUserId(Model $model): Model
    {
        if (!$model->user_id && auth()->id()) {
            $model->user_id = (int)auth()->id();
        }

        return $model;
    }
}
