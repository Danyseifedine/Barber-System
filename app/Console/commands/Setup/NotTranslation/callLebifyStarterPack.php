<?php

namespace App\Console\Commands\Setup\NotTranslation;

use Illuminate\Console\Command;

class callLebifyStarterPack extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setup:lebify-starter-pack {--auth} {--dashboard} {--home} {--all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Call lebify starter pack';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Lebify Starter Pack installation...');

        $steps = [
            '⠋',
            '⠙',
            '⠹',
            '⠸',
            '⠼',
            '⠴',
            '⠦',
            '⠧',
            '⠇',
            '⠏'
        ];

        $i = 0;
        while ($i < 20) {
            echo "\r" . $steps[$i % count($steps)] . " Installing components...";
            usleep(100000); // Sleep for 100ms
            $i++;
        }
        echo "\r"; // Clear the animation line

        if ($this->option('all')) {
            $this->info('Installing all components...');
            $this->call('setup:auth-nt');
            $this->call('setup:dashboard-nt');
            $this->call('setup:home');
        } else {
            if ($this->option('auth')) {
                $this->info('Installing Auth components...');
                $this->call('setup:auth-nt');
            }
            if ($this->option('dashboard')) {
                $this->info('Installing Dashboard components...');
                $this->call('setup:dashboard-nt');
            }
            if ($this->option('home')) {
                $this->info('Installing Home components...');
                $this->call('setup:home');
            }

            $this->call('setup:sidebar-config');
        }

        $this->newLine();
        $this->info('✓ Lebify Starter Pack installed successfully!');
    }
}
