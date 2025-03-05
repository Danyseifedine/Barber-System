<?php

namespace App\Console\Commands\Setup\Common\RolePermssion;

use App\Traits\Commands\ControllerFileHandler;
use App\Traits\Commands\JsFileHandler;
use App\Traits\Commands\RouteFileHandler;
use App\Traits\Commands\ViewFileHandler;
use Illuminate\Console\Command;

class RolePermissionCommand extends Command
{
    use ControllerFileHandler, JsFileHandler, RouteFileHandler, ViewFileHandler;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setup:permission-role-files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup Permission';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $permissionRoleIndexContent = <<<'HTML'
<!---------------------------
    Layout
---------------------------->
@extends('dashboard.layout.index')

<!---------------------------
    Title
---------------------------->
@section('title', 'Permission Role')

<!---------------------------
    Toolbar
---------------------------->
@section('toolbar')
    @include('dashboard.components.toolbar', [
        'title' => 'Permission Role',
        'currentPage' => 'Permission Role Management',
    ])
@endsection

<!---------------------------
    Columns
---------------------------->

@php
    $columns = [];
@endphp

<!---------------------------
    Main Content
---------------------------->
@section('content')
    <x-lebify-table id="permissionRoleTable" :columns="$columns" :create="false" :selected="false" :filter="false"
        :showCheckbox="false">
    </x-lebify-table>
@endsection


<!---------------------------
Filter Options
---------------------------->

@push('scripts')
    <script src="{{ asset('js/dashboard/privileges/permissionRole.js') }}" type="module" defer></script>
@endpush
HTML;



        $viewMainPath = 'dashboard/pages/privileges';
        $viewMap = [
            'permission' => [
                'index' => [
                    'fileName' => 'index.blade.php',
                    'content' => $permissionRoleIndexContent,
                ],
                'path' => $viewMainPath . '/permissionRole',
            ],
        ];

        if ($this->updateViewFile($viewMap['permission']['index']['fileName'], $viewMap['permission']['index']['content'], $viewMap['permission']['path'])) {
            $this->info('Permission Role has been updated successfully!');
        } else {
            $this->error('Failed to update permission role!');
        }

        $controllerContent = <<<'PHP'
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
PHP;

        if ($this->updateControllerFile("PermissionRoleController.php", $controllerContent, "Dashboard/Pages/Privileges")) {
            $this->info('Permission has been updated successfully!');
        } else {
            $this->error('Failed to update permission!');
        }


        $jsContent = <<<'JS'
/*=============================================================================
 * PermissionRole Management Module
 *
 * This module handles all permissionRole-related operations in the dashboard including:
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
 * @function buildApiUrl - Constructs API endpoints for permissionRole operations
 *--------------------------------------------------------------------------*/
const defaultErrorHandler = (err) => console.error('Error:', err);
const reloadDataTable = () => permissionRoleTable.reload();
const buildApiUrl = (path) => `${DASHBOARD_URL}/permission-role/${path}`;

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
const userActionHandlers = {
    //
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
    //
];

/*---------------------------------------------------------------------------
 * DataTable Configuration
 * Defines the structure and behavior of the PermissionRole management table
 *--------------------------------------------------------------------------*/
const tableColumns = [
    {
        "data": "role",
        "title": "Role",
        "className": "text-start"
    },
    {
        "data": "permission",
        "title": "Permission",
        "className": "text-center"
    },
];

const tableColumnDefinitions = [
    { targets: [0], customRender: (data) => `<span class="badge text-center badge-primary">${data}</span>`, dataClassName: 'text-center' },
    { targets: [1], customRender: (data) => `<span class="badge badge-primary">${data}</span>` },
];

/*---------------------------------------------------------------------------
 * Bulk Action Handler
 * Processes operations on multiple selected permissionRoles
 * @param {Array} selectedIds - Array of selected permissionRole IDs
 *--------------------------------------------------------------------------*/
const handleBulkActions = (selectedIds) => {
    // Implementation for bulk actions
    // Example: Delete multiple permissionRoles, change status, etc.
};

/*=============================================================================
 * DataTable Initialization
 * Creates and configures the main permissionRole management interface
 *============================================================================*/
export const permissionRoleTable = new $DatatableController('permissionRoleTable', {
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

// Initialize create permissionRole modal
createModalLoader({
    triggerSelector: '.create',
    endpoint: buildApiUrl('create')
});

// Global access for table reload
window.RDT = reloadDataTable;

JS;

        if ($this->updateJsFile("permissionRole.js", $jsContent, "js/dashboard/privileges")) {
            $this->info('Permission Role has been updated successfully!');
        } else {
            $this->error('Failed to update permission role!');
        }
    }
}
