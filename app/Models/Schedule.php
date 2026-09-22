<?php

namespace App\Models;

use App\Models\Team;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Schedule byes()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Schedule firstGameOfWeek(?int $week = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Schedule lastGameOfWeek(?int $week = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Schedule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Schedule newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Schedule noByes()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Schedule query()
 * @method void setCurrentWeeks()
 * @method bool areThereGamesToday()
 * @property int $id
 * @property int $nfl_year
 * @property int $nfl_week
 * @property string $home_team
 * @property string $away_team
 * @property int $global_game_id
 * @property int|null $global_away_team_id
 * @property int|null $global_home_team_id
 * @property \Carbon\CarbonImmutable|null $game_time
 * @property string|null $channel
 * @property string|null $stadium_name
 * @property string|null $city
 * @property string|null $state
 * @property string|null $playing_surface
 * @property string|null $stadium_type
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Schedule whereAwayTeam($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Schedule whereChannel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Schedule whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Schedule whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Schedule whereGameTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Schedule whereGlobalAwayTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Schedule whereGlobalGameId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Schedule whereGlobalHomeTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Schedule whereHomeTeam($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Schedule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Schedule whereNflWeek($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Schedule whereNflYear($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Schedule wherePlayingSurface($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Schedule whereStadiumName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Schedule whereStadiumType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Schedule whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Schedule whereUpdatedAt($value)
 */
class Schedule extends Model
{
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'game_time' => 'datetime'
    ];

    /**
     * Each game has an away team.
     *
     * @return BelongsTo
     */
    public function awayTeam() : BelongsTo
    {
        return $this->belongsTo(Team::class, 'global_away_team_id', 'global_team_id');
    }

    /**
     * Each game has a home team.
     *
     * @return BelongsTo
     */
    public function homeTeam() : BelongsTo
    {
        return $this->belongsTo(Team::class, 'global_home_team_id', 'global_team_id');
    }

    /**
     * Local query scope for no byes.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeNoByes(Builder $query): Builder
    {
        return $query->where('away_team', '!=', 'BYE');
    }

    /**
     * Local query scope for byes.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeByes(Builder $query): Builder
    {
        return $query->where('away_team', 'BYE');
    }

    // TODO: Turn this into a relationship.
    /**
     * This should prob be a relationship.
     *
     * @param Builder $query
     * @param integer|null $week
     * @return void
     */
    public function scopeLastGameOfWeek(Builder $query, ?int $week)
    {
        if (!$week) {
            $week = app('currentWeek');
        }
        
        return $query->where([['nfl_week', $week], ['away_team', '!=', 'BYE']])->latest('game_time')->first();
    }
 
    // TODO: Turn this into a relationship.
    /**
     * This should prob be a relationship.
     *
     * @param Builder $query
     * @param integer|null $week
     * @return void
     */
    public function scopeFirstGameOfWeek(Builder $query, ?int $week)
    {
        if (!$week) {
            $week = CurrentWeek::value('current_nfl_week');
        }
        
        return $query->where([['nfl_week', $week], ['away_team', '!=', 'BYE']])->latest('game_time')->first();
    }
 
    /**
     * Apparently setting the current weeks. No clue.
     *
     * @return void
     */
    public static function setCurrentWeeks(): void
    {
        for ($i=1; $i <= 18; $i++) { 
            $start = Schedule::select('game_time', 'id')->where('nfl_week', $i)->first();
            $startTime = Carbon::parse($start->game_time)->addDay();
            $end = Schedule::select('game_time', 'id')->where('nfl_week', $i)->latest('game_time')->first();
            $endTime = Carbon::parse($end->game_time)->addDay();

            CurrentWeek::create([
                'current_nfl_week' => $i,
                'week_start' => $startTime,
                'week_end' => $endTime
            ]);
        }
    }

    /**
     * Check if there are football games today.
     *
     * @return boolean
     */
    public static function areThereGamesToday(): bool
    {
        return false;
    }
}
