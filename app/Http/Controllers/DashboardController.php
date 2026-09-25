<?php

namespace App\Http\Controllers;
use App\Models\LeagueInvited;
use App\Models\Schedule;
use Illuminate\Contracts\Database\Eloquent\Builder;

class DashboardController extends Controller
{
    public function index()
    {
        if (Schedule::doesntExist()) {
            // return some other view that says season hasn't started yet
            return inertia('BetweenSeasons');
        }

        $user = auth()->user();
        $currentWeek = app('currentWeek');
        $user->load([
            'leagues.picks' => function (Builder $query) use ($currentWeek) {
                $query->where('nfl_week', $currentWeek);
            },
            'leagues.users'
        ]);
        $leagueInvites = LeagueInvited::with('league')->where('invited', 'like', '%' . $user->email . '%')->latest()->first();
        $hasLeagueInvites = $leagueInvites ? true : false;
        return inertia('Dashboard', [
            'leagues' => $user->leagues,
            'user' => $user,
            'hasLeagueInvites' => $hasLeagueInvites
        ]);
    }
}
