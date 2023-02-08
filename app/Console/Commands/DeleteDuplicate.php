<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use DB;

class DeleteDuplicate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:duplicate';

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
        $sql = 'DELETE FROM register_url WHERE ID NOT IN (SELECT * FROM (SELECT MAX(ID) AS MaxRecordID FROM register_url GROUP BY user_id,itemName,itemImageUrl) AS tmptable);';
        DB::statement($sql);
        return 0;
    }
}
