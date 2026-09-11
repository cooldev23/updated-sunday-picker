<?php

namespace App\Models;

use App\Models\Schedule;
// TODO: not sure this class is useful; needs a second look; currently there is no DB table/migration
/**
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Week byes()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Week firstGameOfWeek(?int $week)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Week lastGameOfWeek(?int $week)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Week newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Week newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Week noByes()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Week query()
 */
class Week extends Schedule
{
    protected $dates = [
        'date'
    ];
    
    public static function formatForJson(int $week, User $user, int $leagueId)
    {
        if ($user->id !== auth()->id()) {
            return response()->json([
                'error' => 'User access error. You do not have access to this page'
            ]);
        }
    
        $games = Schedule::where([['nfl_week', $week], ['away_team', '<>', 'BYE']])->get();
        $byes = Schedule::where([['away_team', 'BYE'], ['nfl_week', $week]])->pluck('home_team')->toArray();
        $userPicks = $user->picks()->where([['user_id_FK', $user->id],['league_id_FK', $leagueId],['nfl_week', $week]])->get(['game_id as gameId', 'winner as team', 'weighted_order as weight', 'tiebreaker']);

        $disabled = $week < CurrentWeek::value('current_nfl_week') ? true : false;

        $lastGameOfWeek = Schedule::lastGameOfWeek($week);

        return response()->json([
            'allGames' => $games,
            'byes' => $byes,
            'userPicks' => $userPicks,
            'disabled' => $disabled,
            'lastGame' => $lastGameOfWeek
        ]);
    }

    /**
     * Returns an array of teams on a bye
     * for the week
     *
     * @param  int  $weekNumber
     * @return array
     */

    public static function getByes($weekNumber)
    {
        $byes = Schedule::where([['away_team', 'BYE'], ['nfl_week', $weekNumber]])->pluck('home_team')->toArray();

        return $byes;
    }

    /**
     * Returns an array of teams on a bye
     * for the week
     *
     * @param  int  $weekNumber
     * @return array
     */

    public static function getGamesCount(int $weekNumber)
    {
        $weeksGamesCount = Schedule::where([['away_team', '!=', 'BYE'], ['nfl_week', intval($weekNumber)]])->get();
        return $weeksGamesCount;
    }
}
