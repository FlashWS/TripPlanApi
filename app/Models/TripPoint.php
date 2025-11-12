<?php

namespace App\Models;

use App\Models\Scopes\UserScope;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 *
 *
 * @property string $uuid
 * @property int $user_id
 * @property string $trip_uuid
 * @property string $point_uuid
 * @property int $day
 * @property string|null $time
 * @property int $order
 * @property string|null $note Примечание
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\TFactory|null $use_factory
 * @method static \Database\Factories\TripPointFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TripPoint newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TripPoint newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TripPoint query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TripPoint whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TripPoint whereDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TripPoint whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TripPoint whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TripPoint wherePointUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TripPoint whereTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TripPoint whereTripUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TripPoint whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TripPoint whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TripPoint whereUuid($value)
 * @mixin \Eloquent
 */
class TripPoint extends Pivot
{
    /** @use HasFactory<\Database\Factories\TripPointFactory> */
    use HasFactory, HasUuids;

    protected $table = 'trip_point';

    protected $keyType = 'string';
    public $incrementing = false;
    protected $primaryKey = 'uuid';

    protected $fillable = [
        'day',
        'time',
        'order',
        'note',
        'point_uuid',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new UserScope);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    public function point(): BelongsTo
    {
        return $this->belongsTo(Point::class);
    }
}
