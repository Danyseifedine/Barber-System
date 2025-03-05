<?php

namespace App\Http\Controllers\Dashboard\Pages\Privileges;

use App\Http\Controllers\BaseController;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class RoleController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        return view('dashboard.pages.privileges.role.index', compact('user'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return $this->componentResponse(view('dashboard.pages.privileges.role.modal.create'));
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

        Role::create($request->all());
        return $this->modalToastResponse('Role created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $role = Role::with(['permissions'])->findOrFail($id);

        // Get the count of users with this role
        $usersCount = DB::table('role_user')
            ->where('role_id', $role->id)
            ->count();

        $role->users_count = $usersCount;

        return $this->componentResponse(view('dashboard.pages.privileges.role.modal.show', compact('role')));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $role = Role::find($id);
        return $this->componentResponse(view('dashboard.pages.privileges.role.modal.edit', compact('role')));
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

        $role = Role::find($request->id);
        $role->update($request->all());
        return $this->modalToastResponse('Role updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $role = Role::find($id);
        $role->delete();
        return response()->json(['message' => 'Role deleted successfully']);
    }

    /**
     * Datatable Initialization
     */
    public function datatable(Request $request)
    {
        $search = request()->get('search');
        $value = isset($search['value']) ? $search['value'] : null;

        $roles = Role::select(
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

        return DataTables::of($roles->latest())
            ->editColumn('created_at', function ($role) {
                return $role->created_at->diffForHumans();
            })
            ->make(true);
    }

    /**
     * Attach Permissions Modal
     */
    public function attachPermissionsModal($id)
    {
        $role = Role::find($id);
        $permissions = Permission::all();
        return $this->componentResponse(view('dashboard.pages.privileges.role.modal.attach', compact('role', 'permissions')));
    }

    /**
     * Update Permissions
     */
    public function updatePermissions($id, $action, $permissionId)
    {
        try {
            $role = Role::findOrFail($id);
            $permission = Permission::findOrFail($permissionId);

            switch ($action) {
                case 'attach':
                    // Check if permission is already attached to avoid duplicates
                    if (!$role->hasPermission($permission->name)) {
                        $role->permissions()->attach($permissionId);
                    }
                    $message = 'Permission attached successfully';
                    break;

                case 'detach':
                    $role->permissions()->detach($permissionId);
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
}