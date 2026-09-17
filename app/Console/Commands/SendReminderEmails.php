<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SendReminderEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:send-reminder-email {user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a reminder email to anyone that hasn\'t submitted picks before the first game of the week';

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
        //
    }
}
