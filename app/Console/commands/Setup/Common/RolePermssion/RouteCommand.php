<?php

namespace App\Console\Commands\Setup\Common\RolePermssion;

use App\Traits\Commands\RouteFileHandler;
use Illuminate\Console\Command;

class RouteCommand extends Command
{

    use RouteFileHandler;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setup:route-role-permission-files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup User Role';

    /**
     * Execute the console command.
     */

    public function handle()
    {

        $content = <<<'EOT'
<?php

use App\Http\Controllers\Dashboard\Pages\Privileges\UserPermissionController;
use App\Http\Controllers\Dashboard\Pages\Privileges\UserRoleController;
use App\Http\Controllers\Dashboard\Pages\Privileges\PermissionRoleController;
use App\Http\Controllers\Dashboard\Pages\Privileges\PermissionController;
use App\Http\Controllers\Dashboard\Pages\Privileges\RoleController;

use Illuminate\Support\Facades\Route;

// ======================================================================= //
// ====================== START ROLE DATATABLE =========================== //
// ======================================================================= //

Route::controller(RoleController::class)
    ->prefix('roles')
    ->name('roles.')
    ->group(function () {
        Route::post('/update', 'update')->name('update');
        Route::get('/{id}/show', 'show')->name('show');
        Route::get('/datatable', 'datatable')->name('datatable');
        Route::get('/{id}/attach-permissions-modal', 'attachPermissionsModal')->name('attachPermissionsModal');
        Route::post('/{id}/permissions/{action}/{permissionId}', 'updatePermissions')->name('updatePermissions');

        // ======================================================================= //
        // ==================== START USER ROLE DATATABLE ======================== //
        // ======================================================================= //

        Route::controller(UserRoleController::class)
            ->prefix('users')
            ->name('users.')
            ->group(function () {
                Route::get('/datatable', 'datatable')->name('datatable');
                Route::post('/{id}/roles/{action}/{roleId}', 'updateRoles')->name('updateRoles');
            });

        Route::resource('users', UserRoleController::class)
            ->except(['show', 'update']);

        // ======================================================================= //
        // ==================== END USERROLE DATATABLE =========================== //
        // ======================================================================= //
    });

Route::resource('roles', RoleController::class)
    ->except(['show', 'update']);

// ======================================================================= //
// ======================== END ROLE DATATABLE =========================== //
// ======================================================================= //

// ======================================================================= //
// =================== START PERMISSION DATATABLE ======================== //
// ======================================================================= //

Route::controller(PermissionController::class)
    ->prefix('permissions')
    ->name('permissions.')
    ->group(function () {
        Route::post('/update', 'update')->name('update');
        Route::get('/{id}/show', 'show')->name('show');
        Route::get('/datatable', 'datatable')->name('datatable');
        Route::get('/{id}/attach-roles-modal', 'attachRolesModal')->name('attachRolesModal');
        Route::post('/{id}/roles/{action}/{roleId}', 'updateRoles')->name('updateRoles');


        // ======================================================================= //
        // ================== START USERPERMISSION DATATABLE ===================== //
        // ======================================================================= //

        Route::controller(UserPermissionController::class)
            ->prefix('users')
            ->name('users.')
            ->group(function () {
                Route::get('/datatable', 'datatable')->name('datatable');
                Route::post('/{id}/permissions/{action}/{permissionId}', 'updatePermissions')->name('updatePermissions');
            });

        Route::resource('users', UserPermissionController::class)
            ->except(['show', 'update']);

        // ======================================================================= //
        // ================== END USERPERMISSION DATATABLE ======================= //
        // ======================================================================= //
    });

Route::resource('permissions', PermissionController::class)
    ->except(['show', 'update']);

// ======================================================================= //
// ==================== END PERMISSION DATATABLE ========================= //
// ======================================================================= //

// ======================================================================= //
// ================ START PERMISSIONROLE DATATABLE ======================= //
// ======================================================================= //

Route::controller(PermissionRoleController::class)
    ->prefix('permission-role')
    ->name('permission-role.')
    ->group(function () {
        Route::post('/update', 'update')->name('update');
        Route::get('/{id}/show', 'show')->name('show');
        Route::get('/datatable', 'datatable')->name('datatable');
    });

Route::resource('permission-role', PermissionRoleController::class)
    ->except(['show', 'update']);

// ======================================================================= //
// ================== END PERMISSIONROLE DATATABLE ======================= //
// ======================================================================= //

EOT;

        $this->updateRouteFile("Privileges.php", $content);

    }


}
