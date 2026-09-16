<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Services\Sportsdata;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TeamsController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, Sportsdata $sportsdata)
    {
        $allTeams = $sportsdata->getTeams();

        foreach ($allTeams as $teamObj) {
            
            $team = new Team();
            
            $team->team_key = $teamObj->Key;
            $team->conference = $teamObj->Conference;
            $team->division = $teamObj->Division;
            $team->fullname = $teamObj->FullName;
            $team->bye_week = $teamObj->ByeWeek;
            $team->global_team_id = $teamObj->GlobalTeamID;
            $team->primary_color = $teamObj->PrimaryColor;
            $team->secondary_color = $teamObj->SecondaryColor;
            $team->tertiary_color = $teamObj->TertiaryColor;
            $team->quaternary_color = $teamObj->QuaternaryColor;

            $team->save();
        }
    }
}
