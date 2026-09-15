<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Otp;
use App\Models\User;
use App\Models\Score;
use App\Models\League;
use App\Models\Schedule;
use App\Models\LeagueType;
use App\Models\CurrentWeek;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class LeagueController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $types = LeagueType::all();
        
        return inertia('league/CreateLeague', [
            'leagueTypes' => $types
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {   
        $attributes = $request->validate([
            'leagueName' => 'required|unique:leagues,name',
            'leagueMotto' => 'nullable',
            'leagueTypeId' => 'required|numeric'
        ]);

        $league = League::create([
            'name' => $attributes['leagueName'],
            'motto' => $attributes['leagueMotto'],
            'league_type_id' => $attributes['leagueTypeId'],
            'league_creator_id' => auth()->id()
        ]);

        $user = User::with('leagues')->where('id', auth()->id())->first();
        $league->users()->attach(auth()->id());

        if ($request->addMembers) {
            if (!Otp::generate($request->addMembers, $league, $user)) {
                request()->session()->flash('alert', [
                    'type' => 'danger',
                    'message' => 'Something went wrong.  The league was created, but your invites failed. Please, try again later'
                ]);

                return to_route('user.dashboard');
            }
        }

        request()->session()->flash('alert', [
            'type' => 'success',
            'message' => 'League ' . $league->name . ' successfully created' . $request->addMembers ? ' and invitations sent' : ''
        ]);
        
        return to_route('user.dashboard');
    }

    /**
     * Display the specified resource.
     */
    public function show(League $league)
    {
        $winnerId = null;
        $currentWeek = CurrentWeek::value('current_nfl_week');
        $score = Score::where('nfl_week', $currentWeek)->first();
        $user = auth()->user();
        $league->load(['users.stats', 'users.statsLatest', 'users.picks' => function ($query) use ($currentWeek) {
            $query->where('nfl_week', $currentWeek);
        }]);
        $totalGamesWeek = Schedule::where('nfl_week', $currentWeek)->noByes()->count();
        $now = Carbon::now();
        $lastGameOfWeek = Schedule::lastGameOfWeek();
        $lastGameOfWeekDateTime = $lastGameOfWeek->game_time->timezone($user->timezone);
        // if ($now->timezone($user->timezone)->toDateString() > $lastGameOfWeekDateTime->addHours(6)->toDateString() && $score) {
        //     switch ($league->league_type_id) {
        //         case 1:
        //             $winnerId = $league->getStandardWeekWinner($currentWeek);
        //             break;
                
        //         case 2:
                    
        //             break;
                
        //         case 3:
        //             $winnerId = $league->getWeightedWeekWinner($currentWeek);
        //             break;
                
        //         default:
        //             # code...
        //             break;
        //     }
        // }
        return inertia('league/ShowResults', [
            'league' => $league,
            'user' => $user,
            'totalGamesWeek'=> $totalGamesWeek,
            'winnerId' => $winnerId,
            'lastGameOfWeek' => $lastGameOfWeek,
            'leagueCanShowGames' => $league->canShowGames()
        ]);
    }

    /**
     * Display the weekly picks for all league users.
     * 
     * @param League $league
     */
    public function weeklyPicks(League $league)
    {
        $currentWeek = CurrentWeek::value('current_nfl_week');
        $score = Score::where('nfl_week', $currentWeek)->first();
        $user = auth()->user();
        $league->load(['users.picks' => function ($query) use ($currentWeek, $league) {
            $query->where('nfl_week', $currentWeek);
            $query->where('league_id_fk', $league->id);
        }]);
    //    dd($league->users);
        $totalGamesWeek = Schedule::where('nfl_week', $currentWeek)->noByes()->count();
        $weeksGames = Schedule::where('nfl_week', $currentWeek)->noByes()->orderBy('game_time')->get();
        // dd($weeksGames);
        $now = Carbon::now();
        $lastGameOfWeek = Schedule::lastGameOfWeek();
        $lastGameOfWeekDateTime = $lastGameOfWeek->game_time->timezone($user->timezone);
        // if ($now->timezone($user->timezone)->toDateString() > $lastGameOfWeekDateTime->addHours(6)->toDateString() && $score) {
        //     switch ($league->league_type_id) {
        //         case 1:
        //             $winnerId = $league->getStandardWeekWinner($currentWeek);
        //             break;
                
        //         case 2:
                    
        //             break;
                
        //         case 3:
        //             $winnerId = $league->getWeightedWeekWinner($currentWeek);
        //             break;
                
        //         default:
        //             # code...
        //             break;
        //     }
        // }
        return inertia('League/WeeklyPicks', [
            'league' => $league,
            'user' => $user,
            'totalGamesWeek'=> $totalGamesWeek,
            'lastGameOfWeek' => $lastGameOfWeek,
            'leagueCanShowGames' => $league->canShowGames(),
            'weeksGames' => $weeksGames
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
