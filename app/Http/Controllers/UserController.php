<?php

namespace App\Http\Controllers;

use App\Models\CurrentWeek;
use Illuminate\Contracts\Database\Eloquent\Builder;

class UserController extends Controller
{
    public function Dashboard()
    {
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
