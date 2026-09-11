<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read \App\Models\League|null $league
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pick newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pick newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pick query()
 * @property int $id
 * @property int $user_id_FK
 * @property int $league_id_FK
 * @property int $game_id
 * @property int $nfl_week
 * @property string|null $winner
 * @property int|null $weighted_order
 * @property int|null $tiebreaker
 * @property int $correct
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pick whereCorrect($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pick whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pick whereGameId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pick whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pick whereLeagueIdFK($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pick whereNflWeek($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pick whereTiebreaker($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pick whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pick whereUserIdFK($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pick whereWeightedOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pick whereWinner($value)
 */
class Pick extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'picks_users';

    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Turn on timestamps for this model.
     *
     * @var boolean
     */
    public $timestamps = true;

    /**
     * Each pick belongs to a user.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Each pick belongs to a league.
     *
     * @return BelongsTo
     */
    public function league(): BelongsTo
    {
        return $this->belongsTo('App\Models\League', 'league_id_FK');
    }
}
