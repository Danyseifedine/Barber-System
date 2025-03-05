<?php

namespace App\Http\Controllers\Dashboard\Pages\Privileges;

use App\Http\Controllers\BaseController;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class UserPermissionController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        $permissions = Permission::all();
        return view('dashboard.pages.privileges.userPermission.index', compact('user', 'permissions'));
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::with('permissions')->find($id);
        $permissions = Permission::all();
        $userPermissions = $user->permissions()->pluck('id')->toArray();

        return $this->componentResponse(view(
            'dashboard.pages.privileges.userPermission.modal.edit',
            compact('user', 'permissions', 'userPermissions')
        ));
    }

    /**
     * Update Permissions
     */
    public function updatePermissions($id, $action, $permissionId)
    {
        try {
            $user = User::findOrFail($id);
            $permission = Permission::findOrFail($permissionId);

            switch ($action) {
                case 'attach':
                    // Check using isAbleTo or can method instead of hasPermission
                    // if (!$user->isAbleTo($permission->name)) {
                        $user->permissions()->attach($permission);
                    // }
                    $message = 'Permission attached successfully';
                    break;

                case 'detach':
                    $user->permissions()->detach($permission);
                    $message = 'Permission detached successfully';
                    break;

                default:
                    return $this->errorResponse('Invalid action specified');
            }

            return $this->successToastResponse($message);
        } catch (\Exception $e) {
            return $this->errorResponse('Error updating permissions: ' . $e->getMessage());
        }
    }

    /**
     * Datatable Initialization
     */
    public function datatable(Request $request)
    {
        $search = request()->get('search');
        $value = isset($search['value']) ? $search['value'] : null;

        $userPermissions = DB::table('users')
            ->leftJoin('permission_user', 'users.id', '=', 'permission_user.user_id')
            ->leftJoin('permissions', 'permission_user.permission_id', '=', 'permissions.id')
            ->select([
                'users.id',
                'users.name',
                'users.email',
                'users.created_at',
                DB::raw('GROUP_CONCAT(DISTINCT COALESCE(permissions.display_name, permissions.name) SEPARATOR "||") as permission_names')
            ])
            ->groupBy('users.id', 'users.name', 'users.email', 'users.created_at')
            ->when($value, function ($query) use ($value) {
                return $query->where(function ($query) use ($value) {
                    $query->where('users.name', 'like', '%' . $value . '%')
                        ->orWhere('users.email', 'like', '%' . $value . '%')
                        ->orWhere('permissions.name', 'like', '%' . $value . '%')
                        ->orWhere('permissions.display_name', 'like', '%' . $value . '%');
                });
            });

        if ($request->permission_id) {
            $userPermissions->where('permissions.id', $request->permission_id);
        }

        return DataTables::of($userPermissions)
            ->addColumn('permissions', function ($row) {
                if (empty($row->permission_names)) {
                    return '<span class="badge badge-light-secondary fs-7 m-1">No Permissions</span>';
                }

                return collect(explode('||', $row->permission_names))
                    ->map(function ($permissionName) {
                        return sprintf(
                            '<span class="badge badge-light-primary fs-7 m-1">%s</span>',
                            $permissionName
                        );
                    })->implode(' ');
            })
            ->editColumn('created_at', function ($row) {
                return \Carbon\Carbon::parse($row->created_at)->diffForHumans();
            })
            ->rawColumns(['permissions'])
            ->make(true);
    }
}