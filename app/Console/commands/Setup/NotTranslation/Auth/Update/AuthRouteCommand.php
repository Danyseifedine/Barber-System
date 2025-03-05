<?php

namespace App\Console\Commands\Setup\NotTranslation\Auth\Update;

use App\Traits\Commands\RouteFileHandler;
use Illuminate\Console\Command;

class AuthRouteCommand extends Command
{

    use RouteFileHandler;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:auth-route';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update route for auth';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $routeContent = <<< ROUTE
<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes(['verify' => true]);

// Logout route
Route::middleware(['auth'])->group(function () {
    Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
});

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->middleware('verified')->name('home');
ROUTE;

        if ($this->updateRouteFile('web.php', $routeContent)) {
            $this->info('Auth route file updated successfully');
        } else {
            $this->error('Failed to update auth route file');
        }
    }
}
