<?php

namespace App\Console\Commands\Setup\Translation\Auth\Update;

use App\Traits\Commands\RouteFileHandler;
use Illuminate\Console\Command;

class AuthRouteTCommand extends Command
{

    use RouteFileHandler;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:auth-route-t';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update route for auth translated';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $routeContent = <<< ROUTE
<?php

use App\Http\Controllers\Auth\LoginController;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
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

    Route::group([
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath'],
    ], function () {

        // Landing page
        Route::get('/', function () {
            return view('welcome');
        });

        // Auth routes
        Auth::routes(['verify' => true]);

        // Logout route
        Route::middleware(['auth'])->group(function () {
            Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
        });

        Route::middleware(['verified'])->group(function () {
            Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
        });
    });

ROUTE;

        if ($this->updateRouteFile('web.php', $routeContent)) {
            $this->info('Auth route file updated successfully');
        } else {
            $this->error('Failed to update auth route file');
        }
    }
}
