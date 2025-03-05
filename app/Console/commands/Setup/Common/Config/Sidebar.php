<?php

namespace App\Console\Commands\Setup\Common\Config;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class Sidebar extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setup:sidebar-config';

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
        $this->info('Sidebar config has been updated successfully!');

        $content = <<<'PHP'
<?php

return [
    'menu_items' => [
        // Dashboard
        [
            'icon' => 'bi bi-grid fs-2',
            'title' => 'common.dashboard.dashboards',
            'route_in' => 'dashboard.index',
            'submenu' => [
                [
                    'title' => 'common.dashboard.default',
                    'link' => 'dashboard.index',
                    'is_route' => true,
                    'icon' => 'bi bi-house fs-2'
                ]
            ]
        ],

        // Section Title
        [
            'is_heading' => true,
            'title' => 'common.dashboard.pages',
        ],

        // User Profile Menu
        [
            'icon' => 'bi bi-person-lines-fill fs-2',
            'title' => 'common.dashboard.user_profile',
            'route_in' => 'dashboard.users.*',
            'submenu' => [
                [
                    'title' => 'common.dashboard.overview',
                    'link' => 'dashboard.users.index',
                    'is_route' => true,
                    'icon' => 'bi bi-person fs-2'
                ],
            ]
        ],
    ]
];
PHP;

        File::put(config_path('sidebar.php'), $content);

        if (File::exists(config_path('sidebar.php'))) {
            $this->info('Sidebar config has been updated successfully!');
        } else {
            $this->error('Failed to update sidebar config!');
        }
    }
}
