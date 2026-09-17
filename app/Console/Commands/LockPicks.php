<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;

class LockPicks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'week:lock-pick-editing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Lock picks to prevent users from changing their picks after games have started on that day';

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
    public function handle()
    {
        $currentWeek = // get current week from service container? make api call?
        $day = intval(Carbon::now()->setTimezone('America/Denver')->dayOfWeek);

        // if day is equal to day of 
    }
}
