<?php

namespace App\Console\Commands;

use App\Models\CurrentWeek;
use App\Services\Sportsdata;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GetCurrentWeek extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schedule:get-current-week';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get the current NFL week';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(Sportsdata $sportsdata)
    {
        // if it's a Wed run code
        if (Carbon::now()->dayOfWeek() === 3) {
            $dbRecord = CurrentWeek::find(1);
            $currWeek = $sportsdata->getCurrentWeek();
            $currSeason = $sportsdata->getCurrentSeason();

            if (!$dbRecord) {
                CurrentWeek::create([
                    'current_nfl_week' => $currWeek,
                    'current_nfl_season' => $currSeason
                ]);
            } else {
                $dbRecord->current_nfl_week = $currWeek;
                $dbRecord->current_nfl_season = $currSeason;
                $dbRecord->save();
                die();
            }
        }        
    }
}
