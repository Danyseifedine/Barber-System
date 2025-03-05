<?php

namespace App\Console\Commands\Setup\NotTranslation\Dashboard\Layout\Create\Common;

use App\Traits\Commands\ViewFileHandler;
use Illuminate\Console\Command;

class footer extends Command
{

    use ViewFileHandler;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lebify:dashboard-common-footer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new footer component';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $fileContent = <<<'HTML'
        <div
            class="app-container container-fluid d-flex flex-column flex-md-row flex-center flex-md-stack py-3">
            <!--begin::Copyright-->
            <div class="text-gray-900 order-2 order-md-1">
                <span class="text-muted fw-semibold me-1">2024&copy;</span>
                <a href="https://keenthemes.com" target="_blank" class="text-gray-800 text-hover-primary">Lebify</a>
            </div>
            <!--end::Copyright-->
            <!--begin::Menu-->
            <ul class="menu menu-gray-600 menu-hover-primary fw-semibold order-1">
                <li class="menu-item">
                    <a href="#" target="_blank" class="menu-link px-2">Dany Seifeddine</a>
                </li>
            </ul>
            <!--end::Menu-->
        </div>
        HTML;

        $fileName = [
            'dashboard' => 'dashboard/common',
            'file' => 'footer',
            'content' => $fileContent,
        ];

        if ($this->updateViewFile($fileName['file'], $fileName['content'], $fileName['dashboard'])) {
            $this->info('Footer file has been updated successfully!');
        } else {
            $this->error('Failed to update footer file!');
        }
    }
}
