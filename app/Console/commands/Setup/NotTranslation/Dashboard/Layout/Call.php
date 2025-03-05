<?php

namespace App\Console\Commands\Setup\NotTranslation\Dashboard\Layout;

use Illuminate\Console\Command;

class Call extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lebify:dashboard-call-layouts';

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
        $this->call('lebify:dashboard-layout');
        sleep(1);
        $this->call('lebify:dashboard-common-sidebar');
        sleep(1);
        $this->call('lebify:dashboard-common-navbar');
        sleep(1);
        $this->call('lebify:dashboard-common-footer');
    }
}
