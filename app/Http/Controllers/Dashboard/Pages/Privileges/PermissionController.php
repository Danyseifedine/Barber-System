<?php

namespace App\Http\Controllers\Dashboard\Pages\Privileges;

use App\Http\Controllers\BaseController;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class PermissionController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        return view('dashboard.pages.privileges.permission.index', compact('user'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return $this->componentResponse(view('dashboard.pages.privileges.permission.modal.create'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'display_name' => 'required|string'
        ]);

        Permission::create($request->all());
        return $this->modalToastResponse('Permission created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $permission = Permission::with(['roles'])->findOrFail($id);

        $usersCount = DB::table('users')
            ->where(function ($query) use ($permission) {
                $query->whereExists(function ($subquery) use ($permission) {
                    $subquery->select(DB::raw(1))
                        ->from('permission_user')
                        ->whereColumn('permission_user.user_id', 'users.id')
                        ->where('permission_user.permission_id', $permission->id);
                })
                    ->orWhereExists(function ($subquery) use ($permission) {
                        $subquery->select(DB::raw(1))
                            ->from('role_user')
                            ->join('permission_role', 'role_user.role_id', '=', 'permission_role.role_id')
                            ->whereColumn('role_user.user_id', 'users.id')
                            ->where('permission_role.permission_id', $permission->id);
                    });
            })->count();

        $permission->users_count = $usersCount;
        return $this->componentResponse(view('dashboard.pages.privileges.permission.modal.show', compact('permission')));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $permission = Permission::find($id);
        return $this->componentResponse(view('dashboard.pages.privileges.permission.modal.edit', compact('permission')));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'display_name' => 'required|string'
        ]);

        $permission = Permission::find($request->id);
        $permission->update($request->all());
        return $this->modalToastResponse('Permission updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $permission = Permission::find($id);
        $permission->delete();
        return response()->json(['message' => 'Permission deleted successfully']);
    }

    /**
     * Datatable Initialization
     */
    public function datatable(Request $request)
    {
        $search = request()->get('search');
        $value = isset($search['value']) ? $search['value'] : null;

        $permissions = Permission::select(
            'id',
            'name',
            'display_name',
            'description',
            'created_at',
        )
            ->when($value, function ($query) use ($value) {
                return $query->where(function ($query) use ($value) {
                    $query->where('name', 'like', '%' . $value . '%')
                        ->orWhere('display_name', 'like', '%' . $value . '%')
                        ->orWhere('description', 'like', '%' . $value . '%');
                });
            });

        return DataTables::of($permissions->latest())
            ->editColumn('created_at', function ($permission) {
                return $permission->created_at->diffForHumans();
            })
            ->make(true);
    }

    /**
     * Attach Roles Modal
     */
    public function attachRolesModal($id)
    {
        $permission = Permission::find($id);
        $roles = Role::all();
        return $this->componentResponse(view('dashboard.pages.privileges.permission.modal.attach', compact('permission', 'roles')));
    }

    /**
     * Update Roles
     */
    public function updateRoles($id, $action, $roleId)
    {
        try {
            $permission = Permission::findOrFail($id);
            $role = Role::findOrFail($roleId);

            switch ($action) {
                case 'attach':
                    // Check if role is already attached to avoid duplicates
                    if (!$role->permissions()->where('permission_id', $id)->exists()) {
                        $role->permissions()->attach($id);
                    }
                    $message = 'Role attached successfully';
                    break;

                case 'detach':
                    $role->permissions()->detach($id);
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