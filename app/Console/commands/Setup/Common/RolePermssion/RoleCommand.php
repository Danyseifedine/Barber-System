<?php

namespace App\Console\Commands\Setup\Common\RolePermssion;

use App\Traits\Commands\ControllerFileHandler;
use App\Traits\Commands\JsFileHandler;
use App\Traits\Commands\RouteFileHandler;
use App\Traits\Commands\ViewFileHandler;
use Illuminate\Console\Command;

class RoleCommand extends Command
{
    use ControllerFileHandler, JsFileHandler, RouteFileHandler, ViewFileHandler;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setup:role-files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup Role';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $roleIndexContent = <<<'HTML'
<!---------------------------
    Layout
---------------------------->
@extends('dashboard.layout.index')

<!---------------------------
    Title
---------------------------->
@section('title', 'Role')

<!---------------------------
    Toolbar
---------------------------->
@section('toolbar')
    @include('dashboard.components.toolbar', [
        'title' => 'Role',
        'currentPage' => 'Role Management',
    ])
@endsection

<!---------------------------
    Columns
---------------------------->

@php
    $columns = ['name', 'display_name', 'description', 'Actions'];
@endphp

<!---------------------------
    Main Content
---------------------------->
@section('content')
    <x-lebify-table id="roleTable" :columns="$columns" :filter="false"></x-lebify-table>
@endsection


<!---------------------------
Filter Options
---------------------------->


<!---------------------------
Modals
---------------------------->
<x-lebify-modal modal-id="create-modal" size="lg" submit-form-id="createForm" title="Create"></x-lebify-modal>
<x-lebify-modal modal-id="edit-modal" size="lg" submit-form-id="editForm" title="Edit"></x-lebify-modal>
<x-lebify-modal modal-id="show-modal" size="lg" :show-submit-button="false" title="Show"></x-lebify-modal>
<x-lebify-modal modal-id="attach-permissions-modal" size="lg" :show-submit-button="false"
    title="Attach Permissions"></x-lebify-modal>

<!---------------------------
Scripts
---------------------------->
@push('scripts')
    <script src="{{ asset('js/dashboard/privileges/role.js') }}" type="module" defer></script>
@endpush

HTML;

        $roleCreateContent = <<<'HTML'
<form id="create-role-form" form-id="createForm" http-request route="{{ route('dashboard.roles.store') }}"
    identifier="single-form-post-handler" feedback close-modal success-toast on-success="RDT">

    <div class="mb-3">
        <label for="name" class="form-label">name</label>
        <input type="text" feedback-id="name-feedback" d-required placeholder="Enter name"
            class="form-control form-control-solid" name="name" id="name">
        <div id="name-feedback" class="invalid-feedback"></div>
    </div>

    <div class="mb-3">
        <label for="display_name" class="form-label">Display Name</label>
        <input type="text" feedback-id="display_name-feedback" d-required placeholder="Enter display name"
            class="form-control form-control-solid" name="display_name" id="display_name">
        <div id="display_name-feedback" class="invalid-feedback"></div>
    </div>

    <div class="mb-3">
        <label for="description" class="form-label">Description</label>
        <textarea type="text" feedback-id="description-feedback" placeholder="Enter description"
            class="form-control form-control-solid" name="description" id="description"></textarea>
        <div id="description-feedback" class="invalid-feedback"></div>
    </div>

</form>

HTML;

        $roleEditContent = <<<'HTML'
<form id="edit-role-form" form-id="editForm" http-request route="{{ route('dashboard.roles.update') }}"
    identifier="single-form-post-handler" feedback close-modal success-toast on-success="RDT">
    <input type="hidden" name="id" id="id" value="{{ $role->id }}">

    <div class="mb-3">
        <label for="name" class="form-label">name</label>
        <input type="text" value="{{ $role->name }}" feedback-id="name-feedback" class="form-control form-control-solid"
            name="name" id="name">
        <div id="name-feedback" class="invalid-feedback"></div>
    </div>

    <div class="mb-3">
        <label for="display_name" class="form-label">Display Name</label>
        <input type="text" value="{{ $role->display_name }}" feedback-id="display_name-feedback"
            class="form-control form-control-solid" name="display_name" id="display_name">
        <div id="display_name-feedback" class="invalid-feedback"></div>
    </div>

    <div class="mb-3">
        <label for="description" class="form-label">Description</label>
        <textarea type="text" feedback-id="description-feedback" placeholder="Enter description"
            class="form-control form-control-solid" name="description" id="description">{{ $role->description }}</textarea>
        <div id="description-feedback" class="invalid-feedback"></div>
    </div>
</form>
HTML;

        $roleShowContent = <<<'HTML'
<div class="d-flex flex-column gap-7 gap-lg-10">
    <!-- Role Details Section -->
    <div class="card card-flush">
        <div class="card-header">
            <div class="card-title">
                <h2>Role Details</h2>
            </div>
        </div>
        <div class="card-body pt-0">
            <div class="row mb-7">
                <label class="col-lg-4 fw-semibold">Display Name</label>
                <div class="col-lg-8">
                    <span class="fw-bold">{{ $role->display_name }}</span>
                </div>
            </div>
            <div class="row mb-7">
                <label class="col-lg-4 fw-semibold">Name</label>
                <div class="col-lg-8">
                    <span class="fw-bold">{{ $role->name }}</span>
                </div>
            </div>
            <div class="row mb-7">
                <label class="col-lg-4 fw-semibold">Description</label>
                <div class="col-lg-8">
                    <span class="fw-bold">{{ $role->description ?: 'No description available' }}</span>
                </div>
            </div>
            <div class="row mb-7">
                <label class="col-lg-4 fw-semibold">Created At</label>
                <div class="col-lg-8">
                    <span class="fw-bold">{{ $role->created_at->format('M d, Y H:i:s') }}</span>
                </div>
            </div>
            <div class="row">
                <label class="col-lg-4 fw-semibold">Last Updated</label>
                <div class="col-lg-8">
                    <span class="fw-bold">{{ $role->updated_at->format('M d, Y H:i:s') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Usage Statistics -->
    <div class="card card-flush">
        <div class="card-header">
            <div class="card-title">
                <h2>Usage Statistics</h2>
            </div>
        </div>
        <div class="card-body pt-0">
            <div class="d-flex flex-wrap">
                <!-- Users Count -->
                <div class="border border-dashed rounded py-3 px-4 me-6 mb-3">
                    <div class="d-flex align-items-center">
                        <div class="symbol symbol-30px me-4">
                            <span class="symbol-label">
                                <i class="bi bi-people fs-2"></i>
                            </span>
                        </div>
                        <div>
                            <div class="fs-2 fw-bold">{{ $role->users_count }}</div>
                            <div class="fw-semibold fs-6 opacity-75">Users</div>
                        </div>
                    </div>
                </div>

                <!-- Permissions Count -->
                <div class="border border-dashed rounded py-3 px-4 mb-3">
                    <div class="d-flex align-items-center">
                        <div class="symbol symbol-30px me-4">
                            <span class="symbol-label">
                                <i class="bi bi-shield-check fs-2"></i>
                            </span>
                        </div>
                        <div>
                            <div class="fs-2 fw-bold">
                                {{ $role->permissions->count() }}</div>
                            <div class="fw-semibold fs-6 opacity-75">Permissions</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Permissions List -->
    <div class="card card-flush">
        <div class="card-header">
            <div class="card-title">
                <h2>Assigned Permissions</h2>
            </div>
            <div class="card-toolbar">
                <span class="badge badge-light-primary">
                    Total: {{ $role->permissions->count() }}
                </span>
            </div>
        </div>
        <div class="card-body pt-0">
            @if ($role->permissions->count() > 0)
                <div class="row g-4">
                    @foreach ($role->permissions as $permission)
                        <div class="col-lg-4">
                            <div class="d-flex align-items-center border rounded-3 p-3">
                                <div class="symbol symbol-35px me-3">
                                    <span class="symbol-label">
                                        <i class="bi bi-shield fs-4"></i>
                                    </span>
                                </div>
                                <div class="d-flex flex-column flex-grow-1 overflow-hidden">
                                    <span class="fw-bold text-truncate">{{ $permission->display_name }}</span>
                                    <span class="opacity-75 fs-7 text-truncate">{{ $permission->name }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="notice d-flex rounded border border-dashed p-6">
                    <i class="bi bi-exclamation-triangle fs-2x me-4"></i>
                    <div class="d-flex flex-stack flex-grow-1">
                        <div>
                            <h4 class="fw-bold">No Permissions Assigned</h4>
                            <div class="fs-6 opacity-75">
                                This role currently has no permissions assigned to it.
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
HTML;

        $roleAttachContent = <<<'HTML'
<div class="mb-3">
    <h2 class="fw-bold">Manage Permissions: {{ $role->name }}</h2>
    <p class="text-muted">Drag and drop permissions between the containers or use the arrows to assign/remove
        permissions.</p>
</div>

<div class="row g-4">
    <!-- Available Permissions -->
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header">
                <h4 class="card-title m-0">
                    <i class="bi bi-shield-lock me-2"></i>Available Permissions
                </h4>
            </div>
            <div class="card-body">
                <div class="d-flex mb-3">
                    <div class="position-relative flex-grow-1">
                        <input type="text" class="form-control form-control-solid ps-10"
                            id="available-permissions-search" placeholder="Search permissions..." autocomplete="off">
                        <span class="position-absolute top-50 translate-middle-y ms-3">
                            <i class="bi bi-search text-gray-500"></i>
                        </span>
                        <span
                            class="position-absolute top-50 end-0 translate-middle-y me-3 cursor-pointer search-clear">
                            <i class="bi bi-x-lg text-gray-500"></i>
                        </span>
                    </div>
                </div>
                <div class="permissions-container available-permissions" data-container="available"
                    style="min-height: 300px; max-height: 300px; overflow-y: auto;">
                    @foreach ($permissions->whereNotIn('id', $role->permissions->pluck('id')) as $permission)
                        <div class="permission-item d-flex align-items-center p-3 border rounded mb-2" draggable="true"
                            data-permission-id="{{ $permission->id }}">
                            <div class="d-flex align-items-center flex-grow-1">
                                <i class="bi bi-grip-vertical me-3 text-gray-500"></i>
                                <div class="permission-content">
                                    <div class="fw-bold">{{ $permission->display_name }}</div>
                                    <div class="text-muted small">{{ $permission->name }}</div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-icon btn-light-primary assign-permission"
                                data-permission-id="{{ $permission->id }}">
                                <i class="bi bi-arrow-right"></i>
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Assigned Permissions -->
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header">
                <h4 class="card-title m-0">
                    <i class="bi bi-shield-check me-2"></i>Assigned Permissions
                </h4>
            </div>
            <div class="card-body">
                <div class="d-flex mb-3">
                    <div class="position-relative flex-grow-1">
                        <input type="text" class="form-control form-control-solid ps-10"
                            id="assigned-permissions-search" placeholder="Search assigned..." autocomplete="off">
                        <span class="position-absolute top-50 translate-middle-y ms-3">
                            <i class="bi bi-search text-gray-500"></i>
                        </span>
                        <span
                            class="position-absolute top-50 end-0 translate-middle-y me-3 cursor-pointer search-clear">
                            <i class="bi bi-x-lg text-gray-500"></i>
                        </span>
                    </div>
                </div>
                <div class="permissions-container assigned-permissions" data-container="assigned"
                    style="min-height: 300px; max-height: 300px; overflow-y: auto;">
                    @foreach ($role->permissions as $permission)
                        <div class="permission-item d-flex align-items-center p-3 border rounded mb-2" draggable="true"
                            data-permission-id="{{ $permission->id }}">
                            <button type="button" class="btn btn-sm btn-icon btn-light-danger remove-permission me-3"
                                data-permission-id="{{ $permission->id }}">
                                <i class="bi bi-arrow-left"></i>
                            </button>
                            <div class="d-flex align-items-center flex-grow-1">
                                <i class="bi bi-grip-vertical me-3 text-gray-500"></i>
                                <div class="permission-content">
                                    <div class="fw-bold">{{ $permission->display_name }}</div>
                                    <div class="text-muted small">{{ $permission->name }}</div>
                                </div>
                            </div>
                            <input type="hidden" name="permissions[]" value="{{ $permission->id }}">
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .permission-item {
        cursor: move;
        /* background: #fff; */
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        z-index: 1;
        opacity: 1;
        transform: translateY(0);
        user-select: none;
    }

    .permission-item.dragging {
        opacity: 0.9;
        transform: scale(1.02);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        z-index: 1000;
    }

    .permission-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .permission-item-transition {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        opacity: 0;
        transform: translateY(-10px);
    }

    .permission-item-show {
        opacity: 1;
        transform: translateY(0);
    }

    .permission-item-hide {
        opacity: 0;
        transform: translateY(-20px);
    }

    /* Improve button interactions */
    .btn-icon {
        transition: all 0.2s ease;
    }

    .btn-icon:hover {
        transform: scale(1.1);
    }

    /* Improve scrollbar appearance */
    .permissions-container::-webkit-scrollbar {
        width: 8px;
    }

    .permissions-container::-webkit-scrollbar-track {
        /* background: #f1f1f1; */
        border-radius: 4px;
    }

    .permissions-container::-webkit-scrollbar-thumb {
        border-radius: 4px;
    }

    /* Add smooth container transitions */
    .permissions-container {
        transition: all 0.3s ease;
        border: 2px solid transparent;
        border-radius: 6px;
        position: relative;
    }

    /* Improve drag visual feedback */
    .permissions-container.drag-over {
        border: 2px dashed #009ef7;
        box-shadow: inset 0 0 10px rgba(0, 158, 247, 0.1);
    }

    .permission-item.processing {
        pointer-events: none;
        opacity: 0.5;
    }

    .search-clear {
        display: none;
        cursor: pointer;
        padding: 5px;
        opacity: 0.7;
        transition: opacity 0.2s ease;
    }

    .search-clear:hover {
        opacity: 1;
        color: #009ef7 !important;
    }

    .form-control:not(:placeholder-shown)+.search-clear {
        display: block;
    }

    .no-results-message {
        border: 1px dashed #ddd;
        border-radius: 6px;
        margin: 10px 0;
        color: #6c757d;
    }
</style>
HTML;


        $viewMainPath = 'dashboard/pages/privileges';
        $viewMap = [
            'role' => [
                'modal' => [
                    'path' => $viewMainPath . '/role/modal',
                    'create' => [
                        'fileName' => 'create.blade.php',
                        'content' => $roleCreateContent,
                    ],
                    'edit' => [
                        'fileName' => 'edit.blade.php',
                        'content' => $roleEditContent,
                    ],
                    'show' => [
                        'fileName' => 'show.blade.php',
                        'content' => $roleShowContent,
                    ],
                    'attach' => [
                        'fileName' => 'attach.blade.php',
                        'content' => $roleAttachContent,
                    ],
                ],
                'index' => [
                    'fileName' => 'index.blade.php',
                    'content' => $roleIndexContent,
                ],
                'path' => $viewMainPath . '/role',
            ],
        ];

        if ($this->updateViewFile($viewMap['role']['index']['fileName'], $viewMap['role']['index']['content'], $viewMap['role']['path'])) {
            $this->info('Role has been updated successfully!');
        } else {
            $this->error('Failed to update role!');
        }

        if ($this->updateViewFile($viewMap['role']['modal']['attach']['fileName'], $viewMap['role']['modal']['attach']['content'], $viewMap['role']['modal']['path'])) {
            $this->info('Role has been updated successfully!');
        } else {
            $this->error('Failed to update role!');
        }

        if ($this->updateViewFile($viewMap['role']['modal']['show']['fileName'], $viewMap['role']['modal']['show']['content'], $viewMap['role']['modal']['path'])) {
            $this->info('Role has been updated successfully!');
        } else {
            $this->error('Failed to update role!');
        }

        if ($this->updateViewFile($viewMap['role']['modal']['create']['fileName'], $viewMap['role']['modal']['create']['content'], $viewMap['role']['modal']['path'])) {
            $this->info('Role has been updated successfully!');
        } else {
            $this->error('Failed to update role!');
        }

        if ($this->updateViewFile($viewMap['role']['modal']['edit']['fileName'], $viewMap['role']['modal']['edit']['content'], $viewMap['role']['modal']['path'])) {
            $this->info('Role has been updated successfully!');
        } else {
            $this->error('Failed to update role!');
        }

        $controllerContent = <<<'PHP'
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
PHP;

        if ($this->updateControllerFile("RoleController.php", $controllerContent, "Dashboard/Pages/Privileges")) {
            $this->info('Role has been updated successfully!');
        } else {
            $this->error('Failed to update role!');
        }


        $jsContent = <<<'JS'
/*=============================================================================
 * Role Management Module
 *
 * This module handles all role-related operations in the dashboard including:
 * - CRUD operations through DataTable
 * - Modal interactions
 * - Event handling
 * - API communications
 *============================================================================*/

 import { HttpRequest } from '../../../core/global/services/httpRequest.js';
import { DASHBOARD_URL } from '../../../core/global/config/app-config.js';
import { SweetAlert } from '../../../core/global/notifications/sweetAlert.js';
import { $DatatableController, SimpleWatcher } from '../../../core/global/advanced/advanced.js';
import { ModalLoader } from '../../../core/global/advanced/advanced.js';

/*---------------------------------------------------------------------------
 * Utility Functions
 * @function defaultErrorHandler - Global error handler for consistency
 * @function reloadDataTable - Refreshes the DataTable after operations
 * @function buildApiUrl - Constructs API endpoints for role operations
 *--------------------------------------------------------------------------*/
const defaultErrorHandler = (err) => console.error('Error:', err);
const reloadDataTable = () => roleTable.reload();
const buildApiUrl = (path) => `${DASHBOARD_URL}/roles/${path}`;

/*---------------------------------------------------------------------------
 * Modal Configuration Factory
 * Creates consistent modal configurations with error handling
 * @param {Object} config - Modal configuration options
 * @returns {ModalLoader} Configured modal instance
 *--------------------------------------------------------------------------*/
const createModalLoader = (config) => new ModalLoader({
    modalBodySelector: config.modalBodySelector || '.modal-body',
    endpoint: config.endpoint,
    triggerSelector: config.triggerSelector,
    onSuccess: config.onSuccess,
    onError: config.onError || defaultErrorHandler
});

/*=============================================================================
 * API Operation Handlers
 * Manages all HTTP requests with consistent error handling and response processing
 * Each method follows a similar pattern:
 * 1. Executes the request
 * 2. Handles success callback
 * 3. Manages errors through defaultErrorHandler
 *============================================================================*/
const apiOperations = {
    _DELETE_: async (endpoint, onSuccess) => {
        try {
            const confirmDelete = await SweetAlert.deleteAction();
            if (confirmDelete) {
                const response = await HttpRequest.del(endpoint);
                onSuccess(response);
            }
        } catch (error) {
            defaultErrorHandler(error);
        }
    },

    _SHOW_: async (id, endpoint) => {
        createModalLoader({
            modalBodySelector: '#show-modal .modal-body',
            endpoint,
            onError: defaultErrorHandler
        });
    },

    _EDIT_: async (id, endpoint, onSuccess) => {
        createModalLoader({
            modalBodySelector: '#edit-modal .modal-body',
            endpoint,
            onSuccess,
            onError: defaultErrorHandler
        });
    },


    _ATTACH_PERMISSIONS_: async (endpoint, onSuccess) => {
        createModalLoader({
            modalBodySelector: '#attach-permissions-modal .modal-body',
            endpoint,
            onSuccess,
            onError: defaultErrorHandler
        });
    },
};
/*=============================================================================
 * User Interface Event Handlers
 * Manages user interactions and connects them to appropriate API operations
 * Each handler:
 * 1. Receives user input
 * 2. Calls appropriate API operation
 * 3. Handles the response (success/error)
 *============================================================================*/
const roleActionHandlers = {
    delete: function (id) {
        this.callCustomFunction('_DELETE_', buildApiUrl(id), (response) => {
            response.risk ? SweetAlert.error() : (SweetAlert.deleteSuccess(), reloadDataTable());
        });
    },

    show: function (id) {
        this.callCustomFunction('_SHOW_', id, buildApiUrl(`${id}/show`));
    },

    edit: function (id) {
        this.callCustomFunction('_EDIT_', id, buildApiUrl(`${id}/edit`), (response) => {
            // Handler for successful edit operation
        });
    },

    attachPermissions: function (id) {
        this.callCustomFunction('_ATTACH_PERMISSIONS_', buildApiUrl(`${id}/attach-permissions-modal`), (response) => {
            initializeDragAndDrop();
            initializeSearch();
            initializeButtons();

            function initializeDragAndDrop() {
                const permissionItems = document.querySelectorAll('.permission-item');
                const containers = document.querySelectorAll('.permissions-container');

                permissionItems.forEach(item => {
                    item.addEventListener('dragstart', handleDragStart);
                    item.addEventListener('dragend', handleDragEnd);
                });

                containers.forEach(container => {
                    container.addEventListener('dragover', handleDragOver);
                    container.addEventListener('dragleave', handleDragLeave);
                    container.addEventListener('drop', handleDrop);
                });
            }

            function initializeSearch() {
                const searchInputs = {
                    available: document.getElementById('available-permissions-search'),
                    assigned: document.getElementById('assigned-permissions-search')
                };

                // Function to filter items
                const filterItems = (container, searchTerm) => {
                    if (!container) return;

                    const items = container.querySelectorAll('.permission-item');
                    const normalizedSearchTerm = searchTerm.toLowerCase().trim();

                    let hasVisibleItems = false;

                    items.forEach(item => {
                        const displayName = item.querySelector('.fw-bold')?.textContent || '';
                        const name = item.querySelector('.text-muted')?.textContent || '';
                        console.log(name)

                        const matches = displayName.toLowerCase().includes(normalizedSearchTerm) ||
                            name.toLowerCase().includes(normalizedSearchTerm);

                        // Force display style to either 'none' or 'flex'
                        matches ? item.classList.remove('d-none') : item.classList.add('d-none');

                        if (matches) {
                            hasVisibleItems = true;
                        }
                    });

                    // Show "no results" message if no items are visible
                    updateNoResultsMessage(container, !hasVisibleItems);
                };

                // Function to show/hide no results message
                const updateNoResultsMessage = (container, show) => {
                    let messageEl = container.querySelector('.no-results-message');

                    if (show) {
                        if (!messageEl) {
                            messageEl = document.createElement('div');
                            messageEl.className = 'no-results-message text-center text-muted py-4';
                            messageEl.innerHTML = '<i class="bi bi-search me-2"></i>No matching permissions found';
                            container.appendChild(messageEl);
                        }
                        messageEl.style.display = '';
                    } else if (messageEl) {
                        messageEl.style.display = 'none';
                    }
                };

                // Add input event listeners
                Object.entries(searchInputs).forEach(([type, input]) => {
                    if (!input) return;

                    const container = document.querySelector(`.${type}-permissions`);

                    // Add clear button functionality
                    const clearBtn = input.parentElement.querySelector('.search-clear');
                    if (clearBtn) {
                        clearBtn.addEventListener('click', () => {
                            input.value = '';
                            filterItems(container, '');
                            clearBtn.style.display = 'none';
                        });
                    }

                    // Handle input changes
                    input.addEventListener('input', (e) => {
                        const searchTerm = e.target.value;
                        filterItems(container, searchTerm);

                        // Toggle clear button visibility
                        if (clearBtn) {
                            clearBtn.style.display = searchTerm.length > 0 ? 'block' : 'none';
                        }
                    });

                    // Handle escape key
                    input.addEventListener('keyup', (e) => {
                        if (e.key === 'Escape') {
                            input.value = '';
                            filterItems(container, '');
                            if (clearBtn) {
                                clearBtn.style.display = 'none';
                            }
                        }
                    });
                });
            }

            function initializeButtons() {
                // Assign permission buttons
                document.querySelectorAll('.assign-permission').forEach(btn => {
                    btn.addEventListener('click', function () {
                        movePermission(this.closest('.permission-item'), 'assigned');
                    });
                });

                // Remove permission buttons
                document.querySelectorAll('.remove-permission').forEach(btn => {
                    btn.addEventListener('click', function () {
                        movePermission(this.closest('.permission-item'), 'available');
                    });
                });
            }

            function movePermission(item, targetContainer) {
                const permissionId = item.dataset.permissionId;
                const sourceContainer = item.parentElement.dataset.container;

                if (sourceContainer === targetContainer) return;

                // Prevent duplicate moves
                if (document.querySelector(`.${targetContainer}-permissions [data-permission-id="${permissionId}"]`)) {
                    console.log('Permission already exists in target container');
                    return;
                }

                // Prepare the action type and endpoint
                const action = targetContainer === 'assigned' ? 'attach' : 'detach';
                const endpoint = buildApiUrl(`${id}/permissions/${action}/${permissionId}`);
                console.log(endpoint)

                // Send HTTP request
                HttpRequest.post(endpoint)
                    .then(response => {
                        if (response.success) {
                            updatePermissionUI(item, targetContainer, permissionId);
                        }
                    })
                    .catch(error => {
                        SweetAlert.error('Failed to update permission');
                        defaultErrorHandler(error);
                    });
            }

            function updatePermissionUI(item, targetContainer, permissionId) {
                // Remove any existing duplicates in the target container
                const existingItem = document.querySelector(`.${targetContainer}-permissions [data-permission-id="${permissionId}"]`);
                if (existingItem) {
                    existingItem.remove();
                }

                // Remove processing class if it exists
                item.classList.remove('processing');

                // Clone the item and modify it based on the target container
                const newItem = item.cloneNode(true);
                const permissionContent = item.querySelector('.permission-content');
                if (!permissionContent) {
                    console.error('Permission content not found');
                    return;
                }

                // Extract just the text content divs, excluding the grip icon
                const displayName = permissionContent.querySelector('.fw-bold').textContent;
                const name = permissionContent.querySelector('.text-muted').textContent;

                // Preserve the draggable attribute and permission ID
                newItem.setAttribute('draggable', 'true');
                newItem.dataset.permissionId = permissionId;

                // Clear existing classes except 'permission-item'
                newItem.className = 'permission-item d-flex align-items-center p-3 border rounded mb-2';

                if (targetContainer === 'assigned') {
                    newItem.innerHTML = `
                        <button type="button" class="btn btn-sm btn-icon btn-light-danger remove-permission me-3"
                                data-permission-id="${permissionId}">
                            <i class="bi bi-arrow-left"></i>
                        </button>
                        <div class="d-flex align-items-center flex-grow-1">
                            <i class="bi bi-grip-vertical me-3 text-gray-500"></i>
                            <div class="permission-content">
                                <div class="fw-bold">${displayName}</div>
                                <div class="text-muted small">${name}</div>
                            </div>
                        </div>
                        <input type="hidden" name="permissions[]" value="${permissionId}">
                    `;
                } else {
                    newItem.innerHTML = `
                        <div class="d-flex align-items-center flex-grow-1">
                            <i class="bi bi-grip-vertical me-3 text-gray-500"></i>
                            <div class="permission-content">
                                <div class="fw-bold">${displayName}</div>
                                <div class="text-muted small">${name}</div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-icon btn-light-primary assign-permission"
                                data-permission-id="${permissionId}">
                            <i class="bi bi-arrow-right"></i>
                        </button>
                    `;
                }

                // Get target container and prepare for animation
                const targetElement = document.querySelector(`.${targetContainer}-permissions`);

                // Add the new item to the top of the container
                if (targetElement.firstChild) {
                    targetElement.insertBefore(newItem, targetElement.firstChild);
                } else {
                    targetElement.appendChild(newItem);
                }

                // Add transition classes
                requestAnimationFrame(() => {
                    // Remove the original item
                    item.remove();

                    // Add show animation to new item
                    newItem.classList.add('permission-item-transition');
                    requestAnimationFrame(() => {
                        newItem.classList.add('permission-item-show');
                    });
                });

                // Reinitialize event listeners
                initializeDragAndDrop();
                initializeButtons();
            }

            function handleDragStart(e) {
                e.target.classList.add('dragging');
                e.dataTransfer.effectAllowed = 'move';
                e.dataTransfer.setData('text/plain', e.target.dataset.permissionId);

                // Add visual feedback
                requestAnimationFrame(() => {
                    e.target.style.opacity = '0.5';
                    document.querySelectorAll('.permissions-container').forEach(container => {
                        container.classList.add('highlight-drop-zone');
                    });
                });
            }

            function handleDragEnd(e) {
                e.target.classList.remove('dragging');
                e.target.style.opacity = '';
                e.target.classList.remove('processing');

                // Remove all drag-related visual feedback
                document.querySelectorAll('.permissions-container').forEach(container => {
                    container.classList.remove('highlight-drop-zone', 'drag-over');
                });
            }

            function handleDragOver(e) {
                e.preventDefault();
                e.dataTransfer.dropEffect = 'move';

                const container = e.target.closest('.permissions-container');
                if (container && !container.classList.contains('drag-over')) {
                    // Remove drag-over from all containers
                    document.querySelectorAll('.permissions-container').forEach(c =>
                        c.classList.remove('drag-over'));
                    // Add to current container
                    container.classList.add('drag-over');
                }
            }

            function handleDragLeave(e) {
                const container = e.target.closest('.permissions-container');
                if (container && !container.contains(e.relatedTarget)) {
                    container.classList.remove('drag-over');
                }
            }

            function handleDrop(e) {
                e.preventDefault();
                const container = e.target.closest('.permissions-container');
                if (!container) return;

                container.classList.remove('drag-over');
                const draggingItem = document.querySelector('.dragging');

                if (draggingItem) {
                    // Remove processing class if dropping in same container
                    if (draggingItem.parentElement === container) {
                        draggingItem.classList.remove('processing');
                        draggingItem.style.opacity = '';
                        return;
                    }

                    // Only proceed if not already being processed
                    if (!draggingItem.classList.contains('processing')) {
                        draggingItem.classList.add('processing');
                        movePermission(draggingItem, container.dataset.container);
                    }
                }
            }
        });
    }
};

/*---------------------------------------------------------------------------
 * Event Listener Configurations
 * Maps DOM events to their respective handlers
 * Structure:
 * - event: The DOM event to listen for
 * - selector: The DOM element selector to attach the listener to
 * - handler: The function to execute when the event occurs
 *--------------------------------------------------------------------------*/
const uiEventListeners = [
    { event: 'click', selector: '.delete-btn', handler: roleActionHandlers.delete },
    { event: 'click', selector: '.btn-show', handler: roleActionHandlers.show },
    { event: 'click', selector: '.btn-edit', handler: roleActionHandlers.edit },
    { event: 'click', selector: '.attach-permissions', handler: roleActionHandlers.attachPermissions },
];

/*---------------------------------------------------------------------------
 * DataTable Configuration
 * Defines the structure and behavior of the Role management table
 *--------------------------------------------------------------------------*/
const tableColumns = [
    { data: 'id' },
    { data: 'name', title: 'Name' },
    { data: 'display_name', title: 'Display Name' },
    { data: 'description', title: 'Description' },
    { data: null, }
];

const tableColumnDefinitions = [
    { targets: [0], orderable: false, htmlType: 'selectCheckbox' },
    {
        targets: [-1],
        htmlType: 'dropdownActions',
        className: 'text-center',
        orderable: false,
        actionButtons: {
            edit: {
                icon: 'bi bi-pencil',
                text: 'Edit',
                type: 'modal',
                class: 'btn-edit',
                modalTarget: '#edit-modal',
                color: 'primary'
            },
            view: {
                icon: 'bi bi-eye',
                text: 'View Details',
                class: 'btn-show',
                type: 'modal',
                modalTarget: '#show-modal',
                color: 'info'
            },
            divider1: { divider: true },
            status: {
                icon: 'bi bi-person-lines-fill',
                text: 'Attach Permissions',
                class: 'attach-permissions',
                type: 'modal',
                modalTarget: '#attach-permissions-modal',
                color: 'warning'
            },
            divider2: { divider: true },
            delete: {
                icon: 'bi bi-trash',
                text: 'Delete',
                class: 'delete-btn',
                color: 'danger'
            }
        }
    }
];

/*---------------------------------------------------------------------------
 * Bulk Action Handler
 * Processes operations on multiple selected roles
 * @param {Array} selectedIds - Array of selected role IDs
 *--------------------------------------------------------------------------*/
const handleBulkActions = (selectedIds) => {
    // Implementation for bulk actions
    // Example: Delete multiple roles, change status, etc.
};

/*=============================================================================
 * DataTable Initialization
 * Creates and configures the main role management interface
 *============================================================================*/
export const roleTable = new $DatatableController('roleTable', {
    lengthMenu: [[15, 50, 100, 200, -1], [15, 50, 100, 200, 'All']],
    selectedAction: handleBulkActions,
    ajax: {
        url: buildApiUrl('datatable'),
        data: (d) => ({
            ...d,
            // Add your custom filters here
        })
    },
    columns: tableColumns,
    columnDefs: $DatatableController.generateColumnDefs(tableColumnDefinitions),
    customFunctions: apiOperations,
    eventListeners: uiEventListeners
});

// Initialize create role modal
createModalLoader({
    triggerSelector: '.create',
    endpoint: buildApiUrl('create')
});

// Global access for table reload
window.RDT = reloadDataTable;

JS;

        if ($this->updateJsFile("role.js", $jsContent, "js/dashboard/privileges")) {
            $this->info('Role has been updated successfully!');
        } else {
            $this->error('Failed to update role!');
        }
    }
}

