<?php

namespace App\Console\Commands\Setup\Common\RolePermssion;

use App\Traits\Commands\ControllerFileHandler;
use App\Traits\Commands\JsFileHandler;
use App\Traits\Commands\RouteFileHandler;
use App\Traits\Commands\ViewFileHandler;
use Illuminate\Console\Command;

class UserPermissionCommand extends Command
{
    use ControllerFileHandler, JsFileHandler, RouteFileHandler, ViewFileHandler;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setup:user-permission-files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup User Permission';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $UserPermissionIndexContent = <<<'HTML'
<!---------------------------
    Layout
---------------------------->
@extends('dashboard.layout.index')

<!---------------------------
    Title
---------------------------->
@section('title', 'User Permission')

<!---------------------------
    Toolbar
---------------------------->
@section('toolbar')
    @include('dashboard.components.toolbar', [
        'title' => 'User Permission',
        'currentPage' => 'User Permission Management',
    ])
@endsection

<!---------------------------
    Columns
---------------------------->

@php
    $columns = ['name', 'email', 'permission', 'actions'];
@endphp

<!---------------------------
    Main Content
---------------------------->
@section('content')
    <x-lebify-table id="userPermissionTable" :columns="$columns" :create="false">


        {{-- start Filter Options --}}

    @section('filter-options')

        <label class="form-check form-check-sm form-check-custom form-check-solid">
            <select class="form-select form-select-solid" data-control="select2" data-placeholder="Select Status"
                name="permission_id">
                <option></option>
                @foreach ($permissions as $permission)
                    <option value="{{ $permission->id }}">{{ $permission->name }}</option>
                @endforeach
            </select>
        </label>
    @endsection

    {{-- End Filter Options --}}

</x-lebify-table>
@endsection


<!---------------------------
Filter Options
---------------------------->


<!---------------------------
Modals
---------------------------->
<x-lebify-modal modal-id="edit-modal" size="lg" submit-form-id="editForm" :show-submit-button="false"
title="Edit"></x-lebify-modal>

<!---------------------------
Scripts
---------------------------->
@push('scripts')
<script src="{{ asset('js/dashboard/privileges/userPermission.js') }}" type="module" defer></script>
@endpush
HTML;

        $UserPermissionEditContent = <<<'HTML'
<div class="mb-3">
    <h2 class="fw-bold">Manage Permissions: {{ $user->name }}</h2>
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
                    @foreach ($permissions->whereNotIn('id', $userPermissions) as $permission)
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
                    @foreach ($permissions->whereIn('id', $userPermissions) as $permission)
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
                    'path' => $viewMainPath . '/UserPermission/modal',
                    'edit' => [
                        'fileName' => 'edit.blade.php',
                        'content' => $UserPermissionEditContent,
                    ],
                ],
                'index' => [
                    'fileName' => 'index.blade.php',
                    'content' => $UserPermissionIndexContent,
                ],
                'path' => $viewMainPath . '/UserPermission',
            ],
        ];

        if ($this->updateViewFile($viewMap['role']['index']['fileName'], $viewMap['role']['index']['content'], $viewMap['role']['path'])) {
            $this->info('User Role has been updated successfully!');
        } else {
            $this->error('Failed to update user role!');
        }

        if ($this->updateViewFile($viewMap['role']['modal']['edit']['fileName'], $viewMap['role']['modal']['edit']['content'], $viewMap['role']['modal']['path'])) {
            $this->info('User Role has been updated successfully!');
        } else {
            $this->error('Failed to update user role!');
        }

        $controllerContent = <<<'PHP'
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
PHP;

        if ($this->updateControllerFile("UserPermissionController.php", $controllerContent, "Dashboard/Pages/Privileges")) {
            $this->info('User Role has been updated successfully!');
        } else {
            $this->error('Failed to update user role!');
        }


        $jsContent = <<<'JS'
/*=============================================================================
 * UserPermission Management Module
 *
 * This module handles all userPermission-related operations in the dashboard including:
 * - CRUD operations through DataTable
 * - Modal interactions
 * - Event handling
 * - API communications
 *============================================================================*/

 import { HttpRequest } from '../../../core/global/services/httpRequest.js';
import { DASHBOARD_URL } from '../../../core/global/config/app-config.js';
import { SweetAlert } from '../../../core/global/notifications/sweetAlert.js';
import { $DatatableController } from '../../../core/global/advanced/advanced.js';
import { ModalLoader } from '../../../core/global/advanced/advanced.js';

/*---------------------------------------------------------------------------
 * Utility Functions
 * @function defaultErrorHandler - Global error handler for consistency
 * @function reloadDataTable - Refreshes the DataTable after operations
 * @function buildApiUrl - Constructs API endpoints for userPermission operations
 *--------------------------------------------------------------------------*/
const defaultErrorHandler = (err) => console.error('Error:', err);
const reloadDataTable = () => userPermissionTable.reload();
const buildApiUrl = (path) => `${DASHBOARD_URL}/permissions/users/${path}`;

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
            onSuccess: (response) => {
                if (onSuccess) onSuccess(response);
            },
            onError: defaultErrorHandler
        });
    },

    _POST_: async (endpoint, onSuccess) => {
        try {
            const response = await HttpRequest.post(endpoint);
            onSuccess(response);
        } catch (error) {
            defaultErrorHandler(error);
        }
    },

    _PATCH_: async (endpoint, onSuccess) => {
        try {
            const response = await HttpRequest.patch(endpoint);
            onSuccess(response);
        } catch (error) {
            defaultErrorHandler(error);
        }
    },

    _GET_: async (endpoint, onSuccess) => {
        try {
            const response = await HttpRequest.get(endpoint);
            onSuccess(response);
        } catch (error) {
            defaultErrorHandler(error);
        }
    },

    _PUT_: async (endpoint, onSuccess) => {
        try {
            const response = await HttpRequest.put(endpoint);
            onSuccess(response);
        } catch (error) {
            defaultErrorHandler(error);
        }
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
let isProcessingMove = false;

const userActionHandlers = {
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

                const filterItems = (container, searchTerm) => {
                    if (!container) return;

                    const items = container.querySelectorAll('.permission-item');
                    const normalizedSearchTerm = searchTerm.toLowerCase().trim();

                    let hasVisibleItems = false;

                    items.forEach(item => {
                        const displayName = item.querySelector('.fw-bold')?.textContent || '';
                        const name = item.querySelector('.text-muted')?.textContent || '';

                        const matches = displayName.toLowerCase().includes(normalizedSearchTerm) ||
                            name.toLowerCase().includes(normalizedSearchTerm);

                        matches ? item.classList.remove('d-none') : item.classList.add('d-none');

                        if (matches) {
                            hasVisibleItems = true;
                        }
                    });

                    updateNoResultsMessage(container, !hasVisibleItems);
                };

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

                Object.entries(searchInputs).forEach(([type, input]) => {
                    if (!input) return;

                    const container = document.querySelector(`.${type}-permissions`);
                    const clearBtn = input.parentElement.querySelector('.search-clear');

                    if (clearBtn) {
                        clearBtn.addEventListener('click', () => {
                            input.value = '';
                            filterItems(container, '');
                            clearBtn.style.display = 'none';
                        });
                    }

                    input.addEventListener('input', (e) => {
                        const searchTerm = e.target.value;
                        filterItems(container, searchTerm);
                        if (clearBtn) {
                            clearBtn.style.display = searchTerm.length > 0 ? 'block' : 'none';
                        }
                    });

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
                document.querySelectorAll('.assign-permission').forEach(btn => {
                    btn.addEventListener('click', function (e) {
                        if (document.querySelector('.dragging')) {
                            return;
                        }
                        movePermission(this.closest('.permission-item'), 'assigned');
                    });
                });

                document.querySelectorAll('.remove-permission').forEach(btn => {
                    btn.addEventListener('click', function (e) {
                        if (document.querySelector('.dragging')) {
                            return;
                        }
                        movePermission(this.closest('.permission-item'), 'available');
                    });
                });
            }

            function movePermission(item, targetContainer) {
                const permissionId = item.dataset.permissionId;
                const sourceContainer = item.parentElement.dataset.container;

                if (sourceContainer === targetContainer || isProcessingMove) return;

                if (document.querySelector(`.${targetContainer}-permissions [data-permission-id="${permissionId}"]`)) {
                    console.log('Permission already exists in target container');
                    return;
                }

                isProcessingMove = true;
                item.classList.add('processing');

                const action = targetContainer === 'assigned' ? 'attach' : 'detach';
                const endpoint = buildApiUrl(`${id}/permissions/${action}/${permissionId}`);

                HttpRequest.post(endpoint)
                    .then(response => {
                        if (response.success) {
                            updatePermissionUI(item, targetContainer, permissionId);
                            reloadDataTable();
                        } else {
                            SweetAlert.error(response.message || 'Failed to update permission');
                        }
                    })
                    .catch(error => {
                        SweetAlert.error('Failed to update permission');
                        defaultErrorHandler(error);
                    })
                    .finally(() => {
                        isProcessingMove = false;
                        item.classList.remove('processing');
                    });
            }

            function updatePermissionUI(item, targetContainer, permissionId) {
                const existingItem = document.querySelector(`.${targetContainer}-permissions [data-permission-id="${permissionId}"]`);
                if (existingItem) {
                    existingItem.remove();
                }

                item.classList.remove('processing');

                const newItem = item.cloneNode(true);
                const permissionContent = item.querySelector('.permission-content');
                if (!permissionContent) {
                    console.error('Permission content not found');
                    return;
                }

                const displayName = permissionContent.querySelector('.fw-bold').textContent;
                const name = permissionContent.querySelector('.text-muted').textContent;

                newItem.setAttribute('draggable', 'true');
                newItem.dataset.permissionId = permissionId;
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

                const targetElement = document.querySelector(`.${targetContainer}-permissions`);

                if (targetElement.firstChild) {
                    targetElement.insertBefore(newItem, targetElement.firstChild);
                } else {
                    targetElement.appendChild(newItem);
                }

                requestAnimationFrame(() => {
                    item.remove();
                    newItem.classList.add('permission-item-transition');
                    requestAnimationFrame(() => {
                        newItem.classList.add('permission-item-show');
                    });
                });

                initializeDragAndDrop();
                initializeButtons();
            }

            // Drag and Drop Handlers
            function handleDragStart(e) {
                e.target.classList.add('dragging');
                e.dataTransfer.effectAllowed = 'move';
                e.dataTransfer.setData('text/plain', e.target.dataset.permissionId);

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
                isProcessingMove = false;

                document.querySelectorAll('.permissions-container').forEach(container => {
                    container.classList.remove('highlight-drop-zone', 'drag-over');
                });
            }

            function handleDragOver(e) {
                e.preventDefault();
                e.dataTransfer.dropEffect = 'move';

                const container = e.target.closest('.permissions-container');
                if (container && !container.classList.contains('drag-over')) {
                    document.querySelectorAll('.permissions-container').forEach(c =>
                        c.classList.remove('drag-over'));
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
                    if (draggingItem.parentElement === container) {
                        draggingItem.classList.remove('processing');
                        draggingItem.style.opacity = '';
                        return;
                    }

                    const buttons = draggingItem.querySelectorAll('button');
                    buttons.forEach(btn => btn.style.pointerEvents = 'none');

                    movePermission(draggingItem, container.dataset.container);

                    setTimeout(() => {
                        buttons.forEach(btn => btn.style.pointerEvents = '');
                    }, 100);
                }
            }

            initializeDragAndDrop();
            initializeSearch();
            initializeButtons();
        });
    },

    status: function (id) {
        this.callCustomFunction('_PATCH_', buildApiUrl(`${id}/status`), (response) => {
            console.log(response);
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
    { event: 'click', selector: '.delete-btn', handler: userActionHandlers.delete },
    { event: 'click', selector: '.btn-show', handler: userActionHandlers.show },
    { event: 'click', selector: '.btn-edit', handler: userActionHandlers.edit },
];

/*---------------------------------------------------------------------------
 * DataTable Configuration
 * Defines the structure and behavior of the UserPermission management table
 *--------------------------------------------------------------------------*/
const tableColumns = [
    {
        "data": "id"
    },
    {
        "data": "name",
        "title": "Name"
    },
    {
        "data": "email",
        "title": "Email"
    },
    {
        "data": "permissions",
        "title": "Permissions"
    },
    {
        "data": null
    }
];

const tableColumnDefinitions = [
    { targets: [0], orderable: false, htmlType: 'selectCheckbox' },
    {
        targets: [-1],
        htmlType: 'actions',
        className: 'text-end',
        actionButtons: {
            edit: true,
            delete: false,
            view: false
        }
    },
];

/*---------------------------------------------------------------------------
 * Bulk Action Handler
 * Processes operations on multiple selected userPermissions
 * @param {Array} selectedIds - Array of selected userPermission IDs
 *--------------------------------------------------------------------------*/
const handleBulkActions = (selectedIds) => {
    // Implementation for bulk actions
    // Example: Delete multiple userPermissions, change status, etc.
};

/*=============================================================================
 * DataTable Initialization
 * Creates and configures the main userPermission management interface
 *============================================================================*/
export const userPermissionTable = new $DatatableController('userPermissionTable', {
    lengthMenu: [[15, 50, 100, 200, -1], [15, 50, 100, 200, 'All']],
    selectedAction: handleBulkActions,
    ajax: {
        url: buildApiUrl('datatable'),
        data: (d) => ({
            ...d,
            permission_id: $('select[name="permission_id"]').val(),
        })
    },
    columns: tableColumns,
    columnDefs: $DatatableController.generateColumnDefs(tableColumnDefinitions),
    customFunctions: apiOperations,
    eventListeners: uiEventListeners
});

// Initialize create userPermission modal
createModalLoader({
    triggerSelector: '.create',
    endpoint: buildApiUrl('create')
});

// Global access for table reload
window.RDT = reloadDataTable;
JS;

        if ($this->updateJsFile("UserPermission.js", $jsContent, "js/dashboard/privileges")) {
            $this->info('User Role has been updated successfully!');
        } else {
            $this->error('Failed to update user role!');
        }
    }
}
