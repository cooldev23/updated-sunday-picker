<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Week;
use App\Models\League;
use App\Models\Schedule;
use Illuminate\View\View;
use App\Models\CurrentWeek;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class WeekController extends Controller
{
    /**
     * Show the form for editing the specified resource
     * 
     * @param  User $user
     * @param  League $league
     * @return \Inertia\Response
     */
    public function edit(User $user, League $league, ?int $week = null)
    {
        // if ($user->cannot('view-other-users')) {
        //     return redirect()->route('unauthorized');
        // }

        $currentWeek = $week ? $week : intval(app('currentWeek')); 
        $user = User::with(['leagues', 'picks'])->find($user->id);
        $userPicks = $user->picks()
                        ->where([['league_id_FK', $league->id],['nfl_week', $currentWeek]])
                        ->get(['game_id as gameId', 'winner as team', 'weighted_order as weight', 'tiebreaker']);

        $thisWeek = Schedule::where('nfl_week', $currentWeek)->noByes()->orderBy('game_time')->get();

        $byes = Schedule::where('nfl_week', $currentWeek)->byes()->pluck('home_team')->toArray();

        $lastGameOfWeek = Schedule::lastGameOfWeek($currentWeek);

        return inertia('picks/PicksForm', [
            'thisWeek' => $thisWeek,
            'byes' => $byes,
            'currentWeek' => $currentWeek, 
            'user' => $user,
            'userPicks' => $userPicks,
            'league' => $league,
            'lastGameOfWeek' => $lastGameOfWeek,
            'timezone' => $user->timezone
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $weekNumber
     * @return \Illuminate\View\View|\Illuminate\Http\Response
     */
    public function changeWeek(Request $request, User $user, League $league): View|JsonResponse
    {   
        // if ($user->cannot('view-other-users')) {
        //     return redirect()->route('unauthorized');
        // }

        $weekNumber = $request->weekSelected;

        // if (request()->wantsJson() || request()->ajax()) {
        return Week::formatForJson($weekNumber, $user, $league->id);
        // }

        // $userPicks = [];
        // $currentWeek = $this->currWeek;
        // $user->load('leagues');

        // $thisWeek = Schedule::where('nfl_week', $currentWeek)->noByes()->get();

        // $byes = Schedule::where('nfl_week', $currentWeek)->byes()->pluck('home_team')->toArray();

        // $lastGameOfWeek = Schedule::lastGameOfWeek();

        // return view('week.show', compact('thisWeek', 'byes', 'currentWeek', 'user', 'userPicks', 'league', 'lastGameOfWeek'));
    }
}
