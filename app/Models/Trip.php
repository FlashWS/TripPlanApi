<?php

namespace App\Models;

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
 * @property string $name Название путешествия
 * @property string $date_start Дата старта
 * @property string $date_end Дата окончания
 * @property int|null $days
 * @property string|null $note Примечание
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\TFactory|null $use_factory
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\TripFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trip newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trip newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trip query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trip whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trip whereDateEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trip whereDateStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trip whereDays($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trip whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trip whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trip whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trip whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trip whereUuid($value)
 * @mixin \Eloquent
 */
class Trip extends Model
{
    /** @use HasFactory<\Database\Factories\TripFactory> */
    use HasFactory, HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;
    protected $primaryKey = 'uuid';

    protected $fillable = [
        'name',
        'date_start',
        'date_end',
        'note',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new UserScope);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function points()
    {
        return $this->belongsToMany(Point::class, 'trip_point')
            ->using(TripPoint::class)
            ->withPivot([
                'day',
                'time',
                'note',
            ])
            ->withTimestamps();
    }
}
