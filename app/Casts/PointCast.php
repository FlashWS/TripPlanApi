<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PointCast implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        $point = unpack('x4/corder/Ltype/dlongitude/dlatitude', $value);

        return [
            'longitude' => $point['longitude'],
            'latitude' => $point['latitude'],
        ];
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return DB::raw(sprintf('ST_PointFromText(\'POINT(%f %f)\', 4326)', $value['latitude'], $value['longitude']));
    }
}
