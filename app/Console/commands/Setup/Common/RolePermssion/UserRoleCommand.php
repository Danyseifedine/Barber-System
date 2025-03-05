<?php

namespace App\Console\Commands\Setup\Common\RolePermssion;

use App\Traits\Commands\ControllerFileHandler;
use App\Traits\Commands\JsFileHandler;
use App\Traits\Commands\RouteFileHandler;
use App\Traits\Commands\ViewFileHandler;
use Illuminate\Console\Command;

class UserRoleCommand extends Command
{
    use ControllerFileHandler, JsFileHandler, RouteFileHandler, ViewFileHandler;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setup:user-role-files';

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
        $userRoleIndexContent = <<<'HTML'
<!---------------------------
    Layout
---------------------------->
@extends('dashboard.layout.index')

<!---------------------------
    Title
---------------------------->
@section('title', 'User Role')

<!---------------------------
    Toolbar
---------------------------->
@section('toolbar')
    @include('dashboard.components.toolbar', [
        'title' => 'User Role',
        'currentPage' => 'User Role Management',
    ])
@endsection

<!---------------------------
    Columns
---------------------------->

@php
    $columns = ['name', 'email', 'roles', 'actions'];
@endphp

<!---------------------------
    Main Content
---------------------------->
@section('content')
    <x-lebify-table id="userRoleTable" :columns="$columns" :create="false">


        {{-- start Filter Options --}}

    @section('filter-options')
        <label class="form-check form-check-sm form-check-custom form-check-solid">
            <select class="form-select form-select-solid" data-control="select2" data-placeholder="Select Role"
                name="role_id">
                <option></option>
                @foreach ($roles as $role)
                    <option value="{{ $role->id }}">{{ $role->name }}</option>
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
<script src="{{ asset('js/dashboard/privileges/userRole.js') }}" type="module" defer></script>
@endpush
HTML;

        $userRoleEditContent = <<<'HTML'
<div class="mb-3">
    <h2 class="fw-bold">Manage Roles: {{ $user->name }}</h2>
    <p class="text-muted">Drag and drop roles between the containers or use the arrows to assign/remove roles.</p>
</div>

<div class="row g-4">
    <!-- Available Roles -->
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header">
                <h4 class="card-title m-0">
                    <i class="bi bi-shield-lock me-2"></i>Available Roles
                </h4>
            </div>
            <div class="card-body">
                <div class="d-flex mb-3">
                    <div class="position-relative flex-grow-1">
                        <input type="text" class="form-control form-control-solid ps-10" id="available-roles-search"
                            placeholder="Search roles..." autocomplete="off">
                        <span class="position-absolute top-50 translate-middle-y ms-3">
                            <i class="bi bi-search text-gray-500"></i>
                        </span>
                        <span
                            class="position-absolute top-50 end-0 translate-middle-y me-3 cursor-pointer search-clear">
                            <i class="bi bi-x-lg text-gray-500"></i>
                        </span>
                    </div>
                </div>
                <div class="roles-container available-roles" data-container="available"
                    style="min-height: 300px; max-height: 300px; overflow-y: auto;">
                    @foreach ($roles->whereNotIn('id', $userRoles) as $role)
                        <div class="role-item d-flex align-items-center p-3 border rounded mb-2" draggable="true"
                            data-role-id="{{ $role->id }}">
                            <div class="d-flex align-items-center flex-grow-1">
                                <i class="bi bi-grip-vertical me-3 text-gray-500"></i>
                                <div class="role-content">
                                    <div class="fw-bold">{{ $role->display_name }}</div>
                                    <div class="text-muted small">{{ $role->name }}</div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-icon btn-light-primary assign-role"
                                data-role-id="{{ $role->id }}">
                                <i class="bi bi-arrow-right"></i>
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Assigned Roles -->
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header">
                <h4 class="card-title m-0">
                    <i class="bi bi-shield-check me-2"></i>Assigned Roles
                </h4>
            </div>
            <div class="card-body">
                <div class="d-flex mb-3">
                    <div class="position-relative flex-grow-1">
                        <input type="text" class="form-control form-control-solid ps-10" id="assigned-roles-search"
                            placeholder="Search assigned..." autocomplete="off">
                        <span class="position-absolute top-50 translate-middle-y ms-3">
                            <i class="bi bi-search text-gray-500"></i>
                        </span>
                        <span
                            class="position-absolute top-50 end-0 translate-middle-y me-3 cursor-pointer search-clear">
                            <i class="bi bi-x-lg text-gray-500"></i>
                        </span>
                    </div>
                </div>

                <div class="roles-container assigned-roles" data-container="assigned"
                    style="min-height: 300px; max-height: 300px; overflow-y: auto;">
                    @foreach ($roles->whereIn('id', $userRoles) as $role)
                        <div class="role-item d-flex align-items-center p-3 border rounded mb-2" draggable="true"
                            data-role-id="{{ $role->id }}">
                            <button type="button" class="btn btn-sm btn-icon btn-light-danger remove-role me-3"
                                data-role-id="{{ $role->id }}">
                                <i class="bi bi-arrow-left"></i>
                            </button>
                            <div class="d-flex align-items-center flex-grow-1">
                                <i class="bi bi-grip-vertical me-3 text-gray-500"></i>
                                <div class="role-content">
                                    <div class="fw-bold">{{ $role->display_name }}</div>
                                    <div class="text-muted small">{{ $role->name }}</div>
                                </div>
                            </div>
                            <input type="hidden" name="roles[]" value="{{ $role->id }}">
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .role-item {
        cursor: move;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        z-index: 1;
        opacity: 1;
        transform: translateY(0);
        user-select: none;
    }

    .role-item.dragging {
        opacity: 0.9;
        transform: scale(1.02);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        z-index: 1000;
    }

    .role-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .role-item-transition {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        opacity: 0;
        transform: translateY(-10px);
    }

    .role-item-show {
        opacity: 1;
        transform: translateY(0);
    }

    .role-item-hide {
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
    .roles-container::-webkit-scrollbar {
        width: 8px;
    }

    .roles-container::-webkit-scrollbar-track {
        border-radius: 4px;
    }

    .roles-container::-webkit-scrollbar-thumb {
        border-radius: 4px;
    }

    /* Add smooth container transitions */
    .roles-container {
        transition: all 0.3s ease;
        border: 2px solid transparent;
        border-radius: 6px;
        position: relative;
    }

    /* Improve drag visual feedback */
    .roles-container.drag-over {
        border: 2px dashed #009ef7;
        box-shadow: inset 0 0 10px rgba(0, 158, 247, 0.1);
    }

    .role-item.processing {
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
                    'path' => $viewMainPath . '/userRole/modal',
                    'edit' => [
                        'fileName' => 'edit.blade.php',
                        'content' => $userRoleEditContent,
                    ],
                ],
                'index' => [
                    'fileName' => 'index.blade.php',
                    'content' => $userRoleIndexContent,
                ],
                'path' => $viewMainPath . '/userRole',
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

PHP;

        if ($this->updateControllerFile("UserRoleController.php", $controllerContent, "Dashboard/Pages/Privileges")) {
            $this->info('User Role has been updated successfully!');
        } else {
            $this->error('Failed to update user role!');
        }


        $jsContent = <<<'JS'
/*=============================================================================
 * UserRole Management Module
 *
 * This module handles all userRole-related operations in the dashboard including:
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
 * @function buildApiUrl - Constructs API endpoints for userRole operations
 *--------------------------------------------------------------------------*/
const defaultErrorHandler = (err) => console.error('Error:', err);
const reloadDataTable = () => userRoleTable.reload();
const buildApiUrl = (path) => `${DASHBOARD_URL}/roles/users/${path}`;

// Add at the top with other utility functions
let isProcessingMove = false;

const debounce = (func, wait) => {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
};

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
};

/*=============================================================================
 * User Interface Event Handlers
 * Manages user interactions and connects them to appropriate API operations
 * Each handler:
 * 1. Receives user input
 * 2. Calls appropriate API operation
 * 3. Handles the response (success/error)
 *============================================================================*/
const userActionHandlers = {
    edit: function (id) {
        this.callCustomFunction('_EDIT_', id, buildApiUrl(`${id}/edit`), (response) => {


            function initializeDragAndDrop() {
                const roleItems = document.querySelectorAll('.role-item');
                const containers = document.querySelectorAll('.roles-container');

                roleItems.forEach(item => {
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
                    available: document.getElementById('available-roles-search'),
                    assigned: document.getElementById('assigned-roles-search')
                };

                const filterItems = (container, searchTerm) => {
                    if (!container) return;

                    const items = container.querySelectorAll('.role-item');
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
                            messageEl.innerHTML = '<i class="bi bi-search me-2"></i>No matching roles found';
                            container.appendChild(messageEl);
                        }
                        messageEl.style.display = '';
                    } else if (messageEl) {
                        messageEl.style.display = 'none';
                    }
                };

                Object.entries(searchInputs).forEach(([type, input]) => {
                    if (!input) return;

                    const container = document.querySelector(`.${type}-roles`);

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
                document.querySelectorAll('.assign-role').forEach(btn => {
                    btn.addEventListener('click', function (e) {
                        if (document.querySelector('.dragging')) {
                            return;
                        }
                        moveRole(this.closest('.role-item'), 'assigned');
                    });
                });

                document.querySelectorAll('.remove-role').forEach(btn => {
                    btn.addEventListener('click', function (e) {
                        if (document.querySelector('.dragging')) {
                            return;
                        }
                        moveRole(this.closest('.role-item'), 'available');
                    });
                });
            }

            function moveRole(item, targetContainer) {
                const roleId = item.dataset.roleId;
                const sourceContainer = item.parentElement.dataset.container;

                if (sourceContainer === targetContainer || isProcessingMove) return;

                if (document.querySelector(`.${targetContainer}-roles [data-role-id="${roleId}"]`)) {
                    console.log('Role already exists in target container');
                    return;
                }

                isProcessingMove = true;
                item.classList.add('processing');

                const action = targetContainer === 'assigned' ? 'attach' : 'detach';
                const endpoint = buildApiUrl(`${id}/roles/${action}/${roleId}`);

                const debouncedRequest = debounce(() => {
                    HttpRequest.post(endpoint)
                        .then(response => {
                            if (response.success) {
                                updateRoleUI(item, targetContainer, roleId);
                                reloadDataTable();
                            } else {
                                item.classList.remove('processing');
                                SweetAlert.error(response.message || 'Failed to update role');
                            }
                        })
                        .catch(error => {
                            item.classList.remove('processing');
                            SweetAlert.error('Failed to update role');
                            defaultErrorHandler(error);
                        })
                        .finally(() => {
                            isProcessingMove = false;
                        });
                }, 300);

                debouncedRequest();
            }

            function updateRoleUI(item, targetContainer, roleId) {
                const existingItem = document.querySelector(`.${targetContainer}-roles [data-role-id="${roleId}"]`);
                if (existingItem) {
                    existingItem.remove();
                }

                item.classList.remove('processing');

                const newItem = item.cloneNode(true);
                const roleContent = item.querySelector('.role-content');
                if (!roleContent) {
                    console.error('Role content not found');
                    return;
                }

                const displayName = roleContent.querySelector('.fw-bold').textContent;
                const name = roleContent.querySelector('.text-muted').textContent;

                newItem.setAttribute('draggable', 'true');
                newItem.dataset.roleId = roleId;
                newItem.className = 'role-item d-flex align-items-center p-3 border rounded mb-2';

                if (targetContainer === 'assigned') {
                    newItem.innerHTML = `
            <button type="button" class="btn btn-sm btn-icon btn-light-danger remove-role me-3"
                    data-role-id="${roleId}">
                <i class="bi bi-arrow-left"></i>
            </button>
            <div class="d-flex align-items-center flex-grow-1">
                <i class="bi bi-grip-vertical me-3 text-gray-500"></i>
                <div class="role-content">
                    <div class="fw-bold">${displayName}</div>
                    <div class="text-muted small">${name}</div>
                </div>
            </div>
            <input type="hidden" name="roles[]" value="${roleId}">
        `;
                } else {
                    newItem.innerHTML = `
            <div class="d-flex align-items-center flex-grow-1">
                <i class="bi bi-grip-vertical me-3 text-gray-500"></i>
                <div class="role-content">
                    <div class="fw-bold">${displayName}</div>
                    <div class="text-muted small">${name}</div>
                </div>
            </div>
            <button type="button" class="btn btn-sm btn-icon btn-light-primary assign-role"
                    data-role-id="${roleId}">
                <i class="bi bi-arrow-right"></i>
            </button>
        `;
                }

                const targetElement = document.querySelector(`.${targetContainer}-roles`);

                if (targetElement.firstChild) {
                    targetElement.insertBefore(newItem, targetElement.firstChild);
                } else {
                    targetElement.appendChild(newItem);
                }

                requestAnimationFrame(() => {
                    item.remove();
                    newItem.classList.add('role-item-transition');
                    requestAnimationFrame(() => {
                        newItem.classList.add('role-item-show');
                    });
                });

                initializeDragAndDrop();
                initializeButtons();
            }

            // Drag and Drop Handlers
            function handleDragStart(e) {
                e.target.classList.add('dragging');
                e.dataTransfer.effectAllowed = 'move';
                e.dataTransfer.setData('text/plain', e.target.dataset.roleId);

                requestAnimationFrame(() => {
                    e.target.style.opacity = '0.5';
                    document.querySelectorAll('.roles-container').forEach(container => {
                        container.classList.add('highlight-drop-zone');
                    });
                });
            }

            function handleDragEnd(e) {
                e.target.classList.remove('dragging');
                e.target.style.opacity = '';
                e.target.classList.remove('processing');
                isProcessingMove = false;

                document.querySelectorAll('.roles-container').forEach(container => {
                    container.classList.remove('highlight-drop-zone', 'drag-over');
                });
            }

            function handleDragOver(e) {
                e.preventDefault();
                e.dataTransfer.dropEffect = 'move';

                const container = e.target.closest('.roles-container');
                if (container && !container.classList.contains('drag-over')) {
                    document.querySelectorAll('.roles-container').forEach(c =>
                        c.classList.remove('drag-over'));
                    container.classList.add('drag-over');
                }
            }

            function handleDragLeave(e) {
                const container = e.target.closest('.roles-container');
                if (container && !container.contains(e.relatedTarget)) {
                    container.classList.remove('drag-over');
                }
            }

            function handleDrop(e) {
                e.preventDefault();
                const container = e.target.closest('.roles-container');
                if (!container) return;

                container.classList.remove('drag-over');
                const draggingItem = document.querySelector('.dragging');

                if (draggingItem) {
                    if (draggingItem.parentElement === container) {
                        draggingItem.classList.remove('processing');
                        draggingItem.style.opacity = '';
                        return;
                    }

                    // Disable button clicks during drag operation
                    const buttons = draggingItem.querySelectorAll('button');
                    buttons.forEach(btn => btn.style.pointerEvents = 'none');

                    moveRole(draggingItem, container.dataset.container);

                    // Re-enable button clicks after drop
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
    { event: 'click', selector: '.btn-edit', handler: userActionHandlers.edit },
];

/*---------------------------------------------------------------------------
 * DataTable Configuration
 * Defines the structure and behavior of the UserRole management table
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
        "data": "roles",
        "title": "Roles"
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
 * Processes operations on multiple selected userRoles
 * @param {Array} selectedIds - Array of selected userRole IDs
 *--------------------------------------------------------------------------*/
const handleBulkActions = (selectedIds) => {
    // Implementation for bulk actions
    // Example: Delete multiple userRoles, change status, etc.
};

/*=============================================================================
 * DataTable Initialization
 * Creates and configures the main userRole management interface
 *============================================================================*/
export const userRoleTable = new $DatatableController('userRoleTable', {
    lengthMenu: [[15, 50, 100, 200, -1], [15, 50, 100, 200, 'All']],
    selectedAction: handleBulkActions,
    ajax: {
        url: buildApiUrl('datatable'),
        data: (d) => ({
            ...d,
            role_id: $('select[name="role_id"]').val(),
        })
    },
    columns: tableColumns,
    columnDefs: $DatatableController.generateColumnDefs(tableColumnDefinitions),
    customFunctions: apiOperations,
    eventListeners: uiEventListeners
});

// Initialize create userRole modal
createModalLoader({
    triggerSelector: '.create',
    endpoint: buildApiUrl('create')
});

// Global access for table reload
window.RDT = reloadDataTable;

JS;

        if ($this->updateJsFile("userRole.js", $jsContent, "js/dashboard/privileges")) {
            $this->info('User Role has been updated successfully!');
        } else {
            $this->error('Failed to update user role!');
        }
    }
}
