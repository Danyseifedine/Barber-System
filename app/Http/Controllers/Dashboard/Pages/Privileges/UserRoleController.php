<?php

namespace App\Http\Controllers\Dashboard\Pages\Privileges;

use App\Http\Controllers\BaseController;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class UserRoleController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        $roles = Role::all();
        return view('dashboard.pages.privileges.userRole.index', compact('user', 'roles'));
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::with('roles')->find($id);
        $roles = Role::all();
        $userRoles = $user->roles()->pluck('id')->toArray();

        return $this->componentResponse(view('dashboard.pages.privileges.userRole.modal.edit', compact('user', 'roles', 'userRoles')));
    }

    /**
     * Datatable Initialization
     */
    public function datatable(Request $request)
    {
        $search = request()->get('search');
        $value = isset($search['value']) ? $search['value'] : null;

        $userRoles = DB::table('users')
            ->leftJoin('role_user', 'users.id', '=', 'role_user.user_id')
            ->leftJoin('roles', 'role_user.role_id', '=', 'roles.id')
            ->select([
                'users.id',
                'users.name',
                'users.email',
                'users.created_at',
                DB::raw('GROUP_CONCAT(DISTINCT COALESCE(roles.display_name, roles.name) SEPARATOR "||") as role_names')
            ])
            ->groupBy('users.id', 'users.name', 'users.email', 'users.created_at')
            ->when($value, function ($query) use ($value) {
                return $query->where(function ($query) use ($value) {
                    $query->where('users.name', 'like', '%' . $value . '%')
                        ->orWhere('users.email', 'like', '%' . $value . '%')
                        ->orWhere('roles.name', 'like', '%' . $value . '%')
                        ->orWhere('roles.display_name', 'like', '%' . $value . '%');
                });
            });

        if ($request->role_id) {
            $userRoles->where('roles.id', $request->role_id);
        }

        return DataTables::of($userRoles)
            ->addColumn('roles', function ($row) {
                if (empty($row->role_names)) {
                    return '<span class="badge badge-light-secondary fs-7 m-1">No Roles</span>';
                }

                return collect(explode('||', $row->role_names))
                    ->map(function ($roleName) {
                        return sprintf(
                            '<span class="badge badge-light-primary fs-7 m-1">%s</span>',
                            $roleName
                        );
                    })->implode(' ');
            })
            ->editColumn('created_at', function ($row) {
                return \Carbon\Carbon::parse($row->created_at)->diffForHumans();
            })
            ->rawColumns(['roles'])
            ->make(true);
    }

    /**
     * Update Roles
     */
    public function updateRoles($id, $action, $roleId)
    {
        try {
            $user = User::findOrFail($id);
            $role = Role::findOrFail($roleId);

            switch ($action) {
                case 'attach':
                    // Check if role is already attached to avoid duplicates
                    if (!$user->hasRole($role->name)) {
                        $user->addRole($role);
                    }
                    $message = 'Role attached successfully';
                    break;

                case 'detach':
                    $user->removeRole($role);
                    $message = 'Role detached successfully';
                    break;

                default:
                    return $this->errorResponse('Invalid action specified');
            }

            return $this->successToastResponse($message);
        } catch (\Exception $e) {
            return $this->errorResponse('Error updating roles: ' . $e->getMessage());
        }
    }
}
