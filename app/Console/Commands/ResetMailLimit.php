<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use DB;
use App\Models\User;

class ResetMailLimit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reset:mailLimit';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * @return int
     */
    public function handle()
    {
        User::where('is_admin',0)->update(array(
            'mailSent' => 0
        ));
        return 0;
    }
}
