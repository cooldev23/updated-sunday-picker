<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Services\Sportsdata;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, Sportsdata $sportsdata)
    {
        dd('stop here');
        $allGames = $sportsdata->getSchedule();

        foreach ($allGames as $g) {
            
            $game = new Schedule;
            
            $game->nfl_year = $g->Season;
            $game->nfl_week = $g->Week;
            $game->home_team = $g->HomeTeam;
            $game->away_team = $g->AwayTeam;
            if($game->away_team === 'BYE') {
                $game->game_time = null;
                $game->channel = null;
                $game->stadium_name = null;
                $game->city = null;
                $game->state = null;
                $game->playing_surface = null;
                $game->stadium_type = null;
                $game->global_game_id = 0;
                $game->global_away_team_id = null;
                $game->global_home_team_id = null;

            } else {
                $game->game_time = Carbon::parse($g->DateTime)->setTimezone('+04:00');
                $game->channel = $g->Channel;
                $game->stadium_name = $g->StadiumDetails->Name;
                $game->city = $g->StadiumDetails->City;
                $game->state = $g->StadiumDetails->State;
                $game->playing_surface = $g->StadiumDetails->PlayingSurface;
                $game->stadium_type = $g->StadiumDetails->Type;
                $game->global_game_id = $g->GlobalGameID;
                $game->global_away_team_id = $g->GlobalAwayTeamID;
                $game->global_home_team_id = $g->GlobalHomeTeamID;
            }

            $game->save();
        }

        return inertia('admin/nflData/GetNFLData');
    }
}
