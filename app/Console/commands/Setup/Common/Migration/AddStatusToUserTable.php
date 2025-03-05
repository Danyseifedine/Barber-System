<?php

namespace App\Console\Commands\Setup\Common\Migration;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class AddStatusToUserTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add:status-to-user-table';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add status column to user table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Adding status column to user table');

        // Create migration file
        $migrationName = 'add_status_to_user_table';
        $this->call('make:migration', ['name' => $migrationName]);

        // Get the latest migration file
        $migrationPath = database_path('migrations');
        $files = File::glob($migrationPath . '/*_' . $migrationName . '.php');
        $latestMigration = end($files);

        if ($latestMigration) {
            // Migration content
            $content = <<<'EOT'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('status', ['active', 'inactive'])->default('active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
EOT;

            // Write migration content
            File::put($latestMigration, $content);
            $this->info('Migration created successfully!');

            // Run the migration
            $this->call('migrate');
            $this->info('Migration executed successfully!');
        } else {
            $this->error('Failed to create migration file.');
        }
    }
}
