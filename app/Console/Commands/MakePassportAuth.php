<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MakePassportAuth extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'passport-auth';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate passport keys, create admins and merchants  clients';

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
        $this->call("passport:keys", [ "--force" => "default" ]);
        $this->call("passport:client" , [
            "--name" => "Admin client",
            "--personal" => "default"
        ]);
        $this->call("passport:client", [
            "--name" => "Merchant client",
            "--password" => "default",
            "--provider" => "merchants"
        ]);
    }
}
