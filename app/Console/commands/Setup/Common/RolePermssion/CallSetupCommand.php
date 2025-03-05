<?php

namespace App\Console\Commands\Setup\Common\RolePermssion;
use Illuminate\Console\Command;

class CallSetupCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'call:role-permission';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup User Role';

    /**
     * Execute the console command.
     */

    public function handle()
    {
        $this->call('setup:user-role-files');
        $this->call('setup:user-permission-files');
        $this->call('setup:permission-role-files');
        $this->call('setup:permission-files');
        $this->call('setup:role-files');
        $this->call('setup:route-role-permission-files');
    }


}
