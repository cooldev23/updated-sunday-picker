<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $current_nfl_week
 * @property int $previous_nfl_week
 * @property int $current_nfl_season
 * @property boolean $games_locked
 * @property Carbon|null $locked_datetime
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CurrentWeek newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CurrentWeek newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CurrentWeek query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CurrentWeek thisWeek()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CurrentWeek whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CurrentWeek whereCurrentNflSeason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CurrentWeek whereCurrentNflWeek($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CurrentWeek whereGamesLocked($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CurrentWeek whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CurrentWeek whereLockedDatetime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CurrentWeek wherePreviousNflWeek($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CurrentWeek whereUpdatedAt($value)
 */
#[Fillable(['current_nfl_week'])]
class CurrentWeek extends Model
{
    protected $fillable = [
        'current_nfl_week',
        'current_nfl_season'
    ];

    protected $table = 'current_week';

    public function scopeThisWeek(Builder $query)
    {
        return $query->value('current_nfl_week');
    }
}
