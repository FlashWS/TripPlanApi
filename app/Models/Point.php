<?php

namespace App\Models;

use App\Casts\PointCast;
use App\Models\Scopes\UserScope;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 *
 *
 * @property string $uuid
 * @property int $user_id
 * @property string $name Название точки
 * @property string $address Адрес
 * @property mixed $location Локация
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\TFactory|null $use_factory
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\PointFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Point newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Point newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Point query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Point whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Point whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Point whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Point whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Point whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Point whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Point whereUuid($value)
 * @mixin \Eloquent
 */
class Point extends Model
{
    /** @use HasFactory<\Database\Factories\PointFactory> */
    use HasFactory;
    use HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;
    protected $primaryKey = 'uuid';

    protected $fillable = [
        'name',
        'address',
        'location',
    ];

    protected $casts = [
      'location' => PointCast::class,
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new UserScope);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
