<?php

namespace App\Console\Commands;

use App\Models\Score;
use App\Services\Sportsdata;
use Illuminate\Console\Command;

class GetWeeklyScores extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scores:get-weekly-scores';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get scores from api and insert into database';

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
     * @return void
     */
    public function handle(Sportsdata $sportsdata)
    {
        $allScores = $sportsdata->getScoresByWeek();
        Score::insertWeeklyScores($allScores);
        die();
    }
}
