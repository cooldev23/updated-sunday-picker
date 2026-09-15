<?php

namespace App\Http\Controllers;
use App\Models\CurrentWeek;
use App\Models\Schedule;
use Illuminate\Contracts\Database\Eloquent\Builder;

class DashboardController extends Controller
{
    public function index()
    {
        if (Schedule::doesntExist()) {
            return to_route('schedule.create');
        }
        
        $user = auth()->user();
        $currentWeek = intval(CurrentWeek::thisWeek());
        $user->load(['leagues.picks' => function(Builder $query) use ($currentWeek) {
            $query->where('nfl_week', $currentWeek);
        }, 'leagues.users']);
        return inertia('Dashboard', [
            'leagues' => $user->leagues,
            'user' => $user,
        ]);
    }
}
