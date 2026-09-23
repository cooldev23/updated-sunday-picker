<?php

namespace App\Http\Controllers;

use App\Models\League;
use App\Models\Pick;
use App\Models\Schedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
     * @return Response
     */
    public function create(League $league, int $week): Response
    {
        $user = auth()->user();
        $byes = Schedule::where('nfl_week', $week)->byes()->pluck('home_team')->toArray();
        $lastGameOfWeek = Schedule::lastGameOfWeek($week);
        $otherWeeksWithPicks = [];

        $weeksWithPicks = $user->picks()->select(DB::raw('DISTINCT nfl_week'))->get('nfl_week');
        
        foreach ($weeksWithPicks as $weekWithPick) {
            if ($weekWithPick->nfl_week === $week) {
                continue;
            }
            $otherWeeksWithPicks[] = $weekWithPick->nfl_week;
        }

        $thisWeek = Schedule::with([
            'awayTeam' => function ($query) {
                $query->select('global_team_id', 'team_key', 'logo_url', 'word_mark_url');
            },
            'homeTeam' => function ($query) {
                $query->select('global_team_id', 'team_key', 'logo_url', 'word_mark_url');
            },
        ])->where('nfl_week', $week)->noByes()->orderBy('game_time')->get();

        return inertia('picks/PicksForm', [
            'thisWeek' => $thisWeek,
            'byes' => $byes,
            'currentWeek' => $week,
            'otherWeeksWithPicks' => $otherWeeksWithPicks,
            'league' => $league,
            'lastGameOfWeek' => $lastGameOfWeek,
            'isEdit' => false
        ]);
    }

    public function store(Request $request, League $league, int $week): RedirectResponse
    {
        $lastGameOfWeek = Schedule::lastGameOfWeek($week);
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

        Inertia::flash('success', 'Picks successfully saved!');
        return to_route('dashboard');
    }

    /**
     * Show the form for editing the specified resource
     * 
     * @param  League $league
     * @return Response
     */
    public function edit(League $league, int $week): Response
    {
        // if ($user->cannot('view-other-users')) {
        //     return redirect()->route('unauthorized');
        // }
        $user = auth()->user();
        $byes = Schedule::where('nfl_week', $week)->byes()->pluck('home_team')->toArray();
        $lastGameOfWeek = Schedule::lastGameOfWeek($week);
        $otherWeeksWithPicks = [];

        $weeksWithPicks = $user->picks()->select(DB::raw('DISTINCT nfl_week'))->get('nfl_week');
        
        foreach ($weeksWithPicks as $weekWithPick) {
            if ($weekWithPick->nfl_week === $week) {
                continue;
            }
            $otherWeeksWithPicks[] = $weekWithPick->nfl_week;
        }

        $thisWeeksPicks = $user->picks()
            ->where([['league_id_FK', $league->id], ['nfl_week', $week]])
            ->get(['game_id as gameId', 'winner as team', 'weighted_order as weight', 'tiebreaker']);

        $thisWeek = Schedule::with([
            'awayTeam' => function ($query) {
                $query->select('global_team_id', 'team_key', 'logo_url', 'word_mark_url');
            },
            'homeTeam' => function ($query) {
                $query->select('global_team_id', 'team_key', 'logo_url', 'word_mark_url');
            },
        ])->where('nfl_week', $week)->noByes()->orderBy('game_time')->get();

        return inertia('picks/PicksForm', [
            'thisWeek' => $thisWeek,
            'byes' => $byes,
            'currentWeek' => $week,
            'userPicks' => $thisWeeksPicks,
            'otherWeeksWithPicks' => $otherWeeksWithPicks,
            'league' => $league,
            'lastGameOfWeek' => $lastGameOfWeek,
            'isEdit' => true
        ]);
    }

    public function update(Request $request, League $league, int $week): RedirectResponse
    {
        $user = auth()->user();

        if ($league->league_type_id === 2) {
            if ($request->data) {
                Pick::updateOrCreate(
                    ['nfl_week' => $week, 'league_id_FK' => $league->id],
                    [
                        'game_id' => $request->data[0]['gameId'],
                        'winner' => $request->data[0]['team'],
                        'weighted_order' => $request->data[0]['weight']
                    ]
                );
            } else {
                $survivorPick = $user->picks()->where([['nfl_week', $week], ['league_id_fk', $league->id]])->first();
                if ($survivorPick) {
                    $survivorPick->delete();
                }
            }
        }

        if (in_array($league->league_type_id, [1, 3])) {
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
            $lastGameOfWeek = Schedule::lastGameOfWeek($week);
            $tiebreakerGame = $user->picks()->where('game_id', $lastGameOfWeek->global_game_id)->first();
            if ($tiebreakerGame) {
                $tiebreakerGame->tiebreaker = $request->tiebreaker;
                $tiebreakerGame->save();
            }
        }

        Inertia::flash('success', 'Picks saved successfully!');

        return to_route('dashboard');
    }
}
