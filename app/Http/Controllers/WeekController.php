<?php

namespace App\Http\Controllers;

use App\Models\League;
use App\Models\Pick;
use App\Models\Schedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class WeekController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // need this here for wayfinder
    }

    /**
     * Show the form for creating the specified resource
     * 
     * @param  League $league
     * @return \Inertia\Response
     */
    public function create(League $league, int $week): Response
    {
        $currentWeek = $week ? $week : intval(app('currentWeek'));
        $byes = Schedule::where('nfl_week', $currentWeek)->byes()->pluck('home_team')->toArray();
        $lastGameOfWeek = Schedule::lastGameOfWeek($currentWeek); 

        $thisWeek = Schedule::with([
            'awayTeam' => function ($query) {
                $query->select('global_team_id', 'team_key', 'logo_url', 'word_mark_url');
            }, 
            'homeTeam' => function ($query) {
                $query->select('global_team_id', 'team_key', 'logo_url', 'word_mark_url');
            },
            ])->where('nfl_week', $currentWeek)->noByes()->orderBy('game_time')->get();
            
        return inertia('picks/PicksForm', [
            'thisWeek' => $thisWeek,
            'byes' => $byes,
            'currentWeek' => $currentWeek, 
            'league' => $league,
            'lastGameOfWeek' => $lastGameOfWeek
        ]);
    }

    public function store(Request $request, League $league, int $week): RedirectResponse
    {
        dd($request->all());
        $lastGameOfWeek = Schedule::lastGameOfWeek();
        foreach ($request->data as $game) {
            $pick = Pick::create([
                'user_id_FK' => auth()->id(),
                'league_id_FK' => $league->id,
                'game_id' => $game['gameId'],
                'nfl_week' => $week,
                'winner' => $game['team'],
                'weighted_order' => $game['weight'] ?? null,
            ]);
            if ($pick->game_id === $lastGameOfWeek->global_game_id) {
                $pick->tiebreaker = intval($request->tiebreaker);
                $pick->save();
            }
        }

        Inertia::flash('success', 'something here');
        return to_route('dashboard');
    }

    /**
     * Show the form for editing the specified resource
     * 
     * @param  League $league
     * @return \Inertia\Response
     */
    public function edit(League $league, ?int $week = null): Response
    {
        // if ($user->cannot('view-other-users')) {
        //     return redirect()->route('unauthorized');
        // }
        $user = auth()->user();
        $currentWeek = $week ? $week : intval(app('currentWeek'));
        $byes = Schedule::where('nfl_week', $currentWeek)->byes()->pluck('home_team')->toArray();
        $lastGameOfWeek = Schedule::lastGameOfWeek($currentWeek);

        $userPicks = $user->picks()
                        ->where([['league_id_FK', $league->id],['nfl_week', $currentWeek]])
                        ->get(['game_id as gameId', 'winner as team', 'weighted_order as weight', 'tiebreaker']);

        $thisWeek = Schedule::with([
            'awayTeam' => function ($query) {
                $query->select('global_team_id', 'team_key', 'logo_url', 'word_mark_url');
            }, 
            'homeTeam' => function ($query) {
                $query->select('global_team_id', 'team_key', 'logo_url', 'word_mark_url');
            },
            ])->where('nfl_week', $currentWeek)->noByes()->orderBy('game_time')->get();
            
        return inertia('picks/PicksForm', [
            'thisWeek' => $thisWeek,
            'byes' => $byes,
            'currentWeek' => $currentWeek, 
            'userPicks' => $userPicks,
            'league' => $league,
            'lastGameOfWeek' => $lastGameOfWeek
        ]);
    }

    public function update(Request $request, League $league, int $week): RedirectResponse
    {
        dd($request->all());
        $user = auth()->user();

        if ($league->league_type_id === 2) {
            foreach ($request->data as $game) {
                $survivorPick = $user->picks()->where([['nfl_week', $week], ['league_id_fk', $league->id]])->first();
                $survivorPick->update([
                    'game_id' => $game['gameId'],
                    'winner' => $game['team'],
                    'nfl_week' => $week,
                    'weighted_order' => $game['weight'] ?? null
                ]);
            }
        }
        
        if (in_array($league->league_type_id, [1,3])) {
            foreach ($request->data as $game) {
                foreach ($user->picks()->where([['nfl_week', $week], ['league_id_fk', $league->id]])->get() as $pick) {
                    $pick->updateOrCreate(
                        ['game_id' => $game['gameId'], 'league_id_FK' => $league->id, 'user_id_FK' => $user->id],
                        [
                            'winner' => $game['team'],
                            'nfl_week' => $week,
                            'weighted_order' => $game['weight'] ?? null  
                        ]
                    );
                }
            }
            $lastGameOfWeek = Schedule::lastGameOfWeek();
            $tiebreakerGame = $user->picks()->where('game_id', $lastGameOfWeek->global_game_id)->first();
            if ($tiebreakerGame) {
                $tiebreakerGame->tiebreaker = $request->tiebreaker;
                $tiebreakerGame->save();
            }
        }
        
        Inertia::flash('success', 'something here');

        return to_route('dashboard');
    }
}
