<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Schedule;
use App\Services\Sportsdata;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   
        //$weeks = Schedule::select('nfl_week')->distinct()->get();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        return inertia('schedule/GetSchedule');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, Sportsdata $sportsdata): JsonResponse
    {   
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

        return response()->json([
            'success' => 'Schedule saved successfully'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return int
     */
    public function gamesCount(): int
    {   
        // need to figure out how to get this from DB
        $currentWeek =$this->currWeek;
        $games = Schedule::where('nfl_week', $currentWeek)->noByes()->get();
        return count($games);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
