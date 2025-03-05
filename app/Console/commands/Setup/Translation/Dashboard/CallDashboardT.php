<?php

namespace App\Console\Commands\Setup\Translation\Dashboard;

use Illuminate\Console\Command;

class CallDashboardT extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setup:dashboard-t';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        info('Calling dashboard layout component');
        $this->call('lebify:dashboard-call-layouts-t');
        sleep(1);
        info('Calling dashboard route');
        $this->call('lebify:dashboard-route-t');
        sleep(1);
        info('Calling dashboard web route');
        $this->call('lebify:dashboard-web-route-t');
        sleep(1);
        info('Creating dashboard controllers');
        $this->call('lebify:dashboard-create-controllers-t');
        sleep(1);
        info('Creating dashboard views');
        $this->call('lebify:dashboard-create-folder-and-file-t');
    }
}
