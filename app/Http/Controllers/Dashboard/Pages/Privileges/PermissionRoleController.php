<?php

namespace App\Http\Controllers\Dashboard\Pages\Privileges;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class PermissionRoleController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        return view('dashboard.pages.privileges.permissionRole.index', compact('user'));
    }

    /**
     * Datatable Initialization
     */
    public function datatable(Request $request)
    {
        $search = request()->get('search');
        $value = isset($search['value']) ? $search['value'] : null;

        $permissionRoles = DB::table('permission_role')
            ->join('permissions', 'permission_role.permission_id', '=', 'permissions.id')
            ->join('roles', 'permission_role.role_id', '=', 'roles.id')
            ->select([
                'permission_role.permission_id',
                'permission_role.role_id',
                'permissions.name as permission_name',
                'permissions.display_name as permission_display_name',
                'roles.name as role_name',
                'roles.display_name as role_display_name',

                ])
            ->when($value, function ($query) use ($value) {
                return $query->where(function ($query) use ($value) {
                    $query->where('permissions.name', 'like', '%' . $value . '%')
                        ->orWhere('permissions.display_name', 'like', '%' . $value . '%')
                        ->orWhere('roles.name', 'like', '%' . $value . '%')
                        ->orWhere('roles.display_name', 'like', '%' . $value . '%');
                });
            });

        return DataTables::of($permissionRoles)
            ->addColumn('permission', function ($row) {
                return $row->permission_display_name ?? $row->permission_name;
            })
            ->addColumn('role', function ($row) {
                return $row->role_display_name ?? $row->role_name;
            })
            ->make(true);
    }
}