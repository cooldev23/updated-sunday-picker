<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Score newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Score newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Score query()
 * @method void insertWeeklyScores(array $weekGames, int $currentWeek)
 * @method Team|string|null getWinner(object $theGame)
 * @method int getTotalPoints()
 * @property int $id
 * @property int $nfl_week
 * @property int $game_id
 * @property int|null $away_score
 * @property int|null $home_score
 * @property string $away_team
 * @property string $home_team
 * @property string|null $winner
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Score whereAwayScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Score whereAwayTeam($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Score whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Score whereGameId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Score whereHomeScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Score whereHomeTeam($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Score whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Score whereNflWeek($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Score whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Score whereWinner($value)
 */
class Score extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Turn on timestamps.
     *
     * @var boolean
     */
    public $timestamps = true;
    
    /**
     * Insert weekly scores.
     * 
     * @param array $weekGames
     * @return void
     */
    public static function insertWeeklyScores(array $weekGames): void
    {
        // some code here to loop through and insert into Scores table
        foreach ($weekGames as $game) {
            Score::firstOrCreate(
                ['game_id' => $game->GlobalGameID],
                [
                    'nfl_week' => $game->Week,
                    'away_score' => $game->AwayScore,
                    'home_score' => $game->HomeScore,
                    'away_team' => $game->AwayTeam,
                    'home_team' => $game->HomeTeam,
                    'winner' => self::getWinner($game),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]
            );
        }
        
        $users = User::all();
        foreach ($users as $user) {
            $user->setCorrectPicksAndPercentages();
        }
    }

    // TODO: this needs to be looked at again
    /**
     * Messed up function.
     *
     * @param object $theGame
     * @return Team|string|null
     */
    public static function getWinner(object $theGame): Team|string|null
    {
        if (!$theGame->AwayScore && $theGame->AwayScore !== 0) {
            return null;
        }

        if ($theGame->AwayScore > $theGame->HomeScore) {
            return $theGame->AwayTeam;
        }
        if ($theGame->AwayScore < $theGame->HomeScore) {
            return $theGame->HomeTeam;
        }
        return 'tie';
    }

    /**
     * Return total points scored for the game.
     *
     * @return int
     */
    public function getTotalPoints(): int
    {
        return $this->away_score + $this->home_score;
    }
}
