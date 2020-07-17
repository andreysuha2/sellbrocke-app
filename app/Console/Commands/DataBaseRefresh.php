<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PHPUnit\Util\Filesystem;

class DataBaseRefresh extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'database-refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'refresh migrations, run seed, run passport auth, clear attachments folder';

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
        (new \Illuminate\Filesystem\Filesystem())->cleanDirectory("storage/app/attachments");
        $this->call("migrate:refresh", [ "--seed" => "default" ]);
        $this->call("passport-auth");
    }
}
