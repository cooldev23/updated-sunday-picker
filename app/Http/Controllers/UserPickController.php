<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Pick;
use App\Models\User;
use App\Models\League;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\Database\Eloquent\Builder;

class UserPickController extends Controller
{
    /**
     * Update the database
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function setPicks(Request $request, User $user, League $league): RedirectResponse
    {
        $user->load(['picks' => function(Builder $query) use ($request) {
            $query->where('nfl_week', $request->selectedWeek);
        }]);

        if (count($user->picks)) {
            $this->updatePicks($request, $user, $league);
            
            $request->session()->flash('alert', [
                'type' => 'success',
                'message' => 'Picks updated successfully',
            ]);
            
            return to_route('picks.update', [
                'user' => $user,
                'league' => $league
            ]);
        }

        $this->createPicks($request, $user, $league);

        $request->session()->flash('alert', [
            'type' => 'success',
            'message' => 'Picks saved successfully',
        ]);

        return to_route('picks.update', [
            'user' => $user,
            'league' => $league
        ]);
    }

    private function updatePicks(Request $request, User $user, League $league)
    {
        if ($league->league_type_id === 2) {
            foreach ($request->data as $game) {
                $survivorPick = $user->picks()->where([['nfl_week', $request->selectedWeek], ['league_id_fk', $league->id]])->first();
                $survivorPick->update([
                    'game_id' => $game['gameId'],
                    'winner' => $game['team'],
                    'nfl_week' => $request->selectedWeek,
                    'weighted_order' => $game['weight'] ?? null,
                    'updated_at' => Carbon::now(),
                ]);
            }
        }
        
        if (in_array($league->league_type_id, [1,3])) {
            foreach ($request->data as $game) {
                foreach ($user->picks()->where([['nfl_week', $request->selectedWeek], ['league_id_fk', $league->id]])->get() as $pick) {
                    $pick->updateOrCreate(
                        ['game_id' => $game['gameId'], 'league_id_FK' => $league->id, 'user_id_FK' => $user->id],
                        [
                            'winner' => $game['team'],
                            'nfl_week' => $request->selectedWeek,
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
    }

    private function createPicks(Request $request, User $user, League $league)
    {
        $lastGameOfWeek = Schedule::lastGameOfWeek();
        foreach ($request->data as $game) {
            $pick = Pick::create([
                'user_id_FK' => $user->id,
                'league_id_FK' => $league->id,
                'game_id' => $game['gameId'],
                'nfl_week' => $request->selectedWeek,
                'winner' => $game['team'],
                'weighted_order' => $game['weight'] ?? null,
            ]);
            if ($pick->game_id === $lastGameOfWeek->global_game_id) {
                $pick->tiebreaker = intval($request->tiebreaker);
                $pick->save();
            }
        }
    }
}
