<?php

namespace App\Console\Commands\Setup\Translation\Dashboard\Layout;

use Illuminate\Console\Command;

class CallDesign extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lebify:dashboard-call-layouts-t';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Call a dashboard layout component';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->call('lebify:dashboard-layout-t');
        sleep(1);
        $this->call('lebify:dashboard-common-sidebar-t');
        sleep(1);
        $this->call('lebify:dashboard-common-navbar-t');
        sleep(1);
        $this->call('lebify:dashboard-common-footer-t');
    }
}
