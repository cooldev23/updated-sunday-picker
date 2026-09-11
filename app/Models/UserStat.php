<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property int $league_id
 * @property int $week
 * @property int $total_correct_wk
 * @property int $total_games_wk
 * @property int|null $total_correct_yr
 * @property int|null $total_games_yr
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStat newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStat newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStat query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStat whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStat whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStat whereLeagueId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStat whereTotalCorrectWk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStat whereTotalCorrectYr($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStat whereTotalGamesWk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStat whereTotalGamesYr($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStat whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStat whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStat whereWeek($value)
 */
class UserStat extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_stats';

    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Each stat record belongs to a user.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id', 'user_id');
    }
}
