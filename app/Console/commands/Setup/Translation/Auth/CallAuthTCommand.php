<?php

namespace App\Console\Commands\Setup\Translation\Auth;

use App\Traits\Commands\AnimationHandler;
use Illuminate\Console\Command;

class CallAuthTCommand extends Command
{

    use AnimationHandler;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setup:auth-t';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup Auth Translated';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Starting Auth Translated Setup...');

        $commands = [
            // Initial Setup
            ['setup:laravel-ui-composer', '📁 Laravel UI using Composer'],

            // Core Updates
            ['update:app-css-file-auth-t', '🎨 Updating app css file'],
            ['update:auth-route-t', '🌐 Updating auth route'],
            ['update:app-layout-t', '🎨 Updating app layout'],
            ['update:auth-controllers-t', '🌐 Updating auth controllers'],
            ['update:auth-files-t', '🎨 Updating auth files'],
            ['update:user-model-file-t', '🌐 Updating user model file'],
        ];

        $bar = $this->output->createProgressBar(count($commands));
        $bar->setFormat("%current%/%max% [%bar%] %percent:3s%%\n%message%");

        foreach ($commands as $index => [$command, $message]) {
            // Add a small delay before each command (0.5 seconds)
            if ($index > 0) {
                $this->addDelay(500000);
            }

            $bar->setMessage("$message...");
            $bar->advance();

            try {
                $this->call($command);

                // Add a small delay after successful execution (0.3 seconds)
                $this->addDelay(300000);

                $this->line("✅ <info>$message completed</info>");

                // Add loading animation
                if ($index < count($commands) - 1) {
                    $this->showLoadingAnimation();
                    // Or use other animation types:
                    // $this->showSpinner();
                    // $this->showProgressDots();
                }
            } catch (\Exception $e) {
                $this->error("❌ Failed to $message: " . $e->getMessage());
            }

            if ($index < count($commands) - 1) {
                $this->line(''); // Add spacing between commands
            }
        }

        $bar->finish();

        $this->line('');
        $this->info('✨ Auth Translated Setup completed successfully!');
        $this->line('');
    }
}
