<?php

namespace App\Console\Commands\Setup\Common\Datatable;

use App\Traits\Commands\ControllerFileHandler;
use App\Traits\Commands\JsFileHandler;
use App\Traits\Commands\RouteFileHandler;
use App\Traits\Commands\ViewFileHandler;
use Illuminate\Console\Command;

class GenerateUserTable extends Command
{

    use ControllerFileHandler, ViewFileHandler, RouteFileHandler, JsFileHandler;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:user-datatable';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate user datatable';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        // $this->call('add:status-to-user-table');


        $controllerContent = <<<CONTROLLER
<?php

namespace App\Http\Controllers\Dashboard\Pages;

use App\Http\Controllers\BaseController;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class UserController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        \$user = auth()->user();
        return view('dashboard.pages.user.index', compact('user'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return \$this->componentResponse(view('dashboard.pages.user.modal.create'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request \$request)
    {
        \$request->validate([
            'name' => 'required|string',
            'email' => 'required|string',
        ]);

        User::create(\$request->all());
        return \$this->modalToastResponse('User created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string \$id)
    {
        \$user = User::find(\$id);
        return \$this->componentResponse(view('dashboard.pages.user.modal.show', compact('user')));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string \$id)
    {
        \$user = User::find(\$id);
        return \$this->componentResponse(view('dashboard.pages.user.modal.edit', compact('user')));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request \$request)
    {
        \$request->validate([
            'name' => 'required|string',
            'email' => 'required|string',
        ]);

        \$user = User::find(\$request->id);
        \$user->update(\$request->all());
        return \$this->modalToastResponse('User updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string \$id)
    {
        \$user = User::find(\$id);
        \$user->delete();
        return response()->json(['message' => 'User deleted successfully']);
    }

    public function datatable(Request \$request)
    {
        \$search = request()->get('search');
        \$value = isset(\$search['value']) ? \$search['value'] : null;

        \$users = User::select(
            'id',
            'name',
            'email',
            'email_verified_at',
            'status',
            'created_at'
        )
            ->when(\$value, function (\$query) use (\$value) {
                return \$query->where(function (\$query) use (\$value) {
                    \$query->where('name', 'like', '%' . \$value . '%')
                        ->orWhere('email', 'like', '%' . \$value . '%');
                });
            });

        if (\$request->status) {
            \$users->where('status', \$request->status);
        }

        if (\$request->verified) {
            \$users->where('email_verified_at', '!=', null);
        }

        if (\$request->not_verified) {
            \$users->where('email_verified_at', null);
        }

        return datatables::of(\$users->latest())
            ->editColumn('created_at', function (\$user) {
                return \$user->created_at->diffForHumans();
            })
            ->make(true);
    }

    public function status(string \$id)
    {
        \$user = User::find(\$id);
        if (\$user->status == 'active') {
            \$user->update(['status' => 'inactive']);
        } else {
            \$user->update(['status' => 'active']);
        }
        return response()->json(['message' => 'User status updated successfully']);
    }
}
CONTROLLER;

        $this->updateControllerFile('UserController', $controllerContent, 'Dashboard/Pages');

        $jsContent = <<<JS
/*=============================================================================
 * User Management Module
 *
 * This module handles all user-related operations in the dashboard including:
 * - CRUD operations through DataTable
 * - Modal interactions
 * - Event handling
 * - API communications
 *============================================================================*/

 import { HttpRequest } from '../../core/global/services/httpRequest.js';
import { DASHBOARD_URL } from '../../core/global/config/app-config.js';
import { SweetAlert } from '../../core/global/notifications/sweetAlert.js';
import { \$DatatableController } from '../../core/global/advanced/advanced.js';
import { ModalLoader } from '../../core/global/advanced/advanced.js';

/*---------------------------------------------------------------------------
 * Utility Functions
 * @function defaultErrorHandler - Global error handler for consistency
 * @function reloadDataTable - Refreshes the DataTable after operations
 * @function buildApiUrl - Constructs API endpoints for user operations
 *--------------------------------------------------------------------------*/
const defaultErrorHandler = (err) => console.error('Error:', err);
const reloadDataTable = () => userDataTable.reload();
const buildApiUrl = (path) => `\${DASHBOARD_URL}/users/\${path}`;

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
    delete: function (id) {
        this.callCustomFunction('_DELETE_', buildApiUrl(id), (response) => {
            response.risk ? SweetAlert.error() : (SweetAlert.deleteSuccess(), reloadDataTable());
        });
    },

    show: function (id) {
        this.callCustomFunction('_SHOW_', id, buildApiUrl(`\${id}/show`));
    },

    edit: function (id) {
        this.callCustomFunction('_EDIT_', id, buildApiUrl(`\${id}/edit`), (response) => {
            // Handler for successful edit operation
        });
    },

    status: function (id) {
        this.callCustomFunction('_PATCH_', buildApiUrl(`\${id}/status`), (response) => {
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
    { event: 'change', selector: '.status-toggle', handler: userActionHandlers.status }
];

/*---------------------------------------------------------------------------
 * DataTable Configuration
 * Defines the structure and behavior of the user management table
 *--------------------------------------------------------------------------*/
const tableColumns = [
    { data: 'id' },
    { data: 'name' },
    { data: 'email' },
    { data: 'email_verified_at', title: 'Verified' },
    { data: 'status', title: 'Status' },
    { data: 'created_at', title: 'Created At' },
    { data: null },
];

const tableColumnDefinitions = [
    { targets: [0], orderable: false, htmlType: 'selectCheckbox' },
    { targets: [1], htmlType: 'text', orderable: true },
    {
        targets: [3],
        orderable: true,
        customRender: (data) => data ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-danger">No</span>'
    },
    {
        targets: [4],
        htmlType: 'toggle',
        dataClassName: 'status-toggle',
        checkWhen: (data) => data === 'active',
        uncheckWhen: (data) => data === 'inactive'
    },
    {
        targets: [-1],
        htmlType: 'actions',
        className: 'text-end',
        actionButtons: {
            edit: true,
            delete: { type: 'null' },
            view: true
        }
    },
];

/*---------------------------------------------------------------------------
 * Bulk Action Handler
 * Processes operations on multiple selected users
 * @param {Array} selectedIds - Array of selected user IDs
 *--------------------------------------------------------------------------*/
const handleBulkActions = (selectedIds) => {
    // Implementation for bulk actions
    // Example: Delete multiple users, change status, etc.
};

/*=============================================================================
 * DataTable Initialization
 * Creates and configures the main user management interface
 *============================================================================*/
export const userDataTable = new \$DatatableController('user-datatable', {
    lengthMenu: [[15, 50, 100, 200, -1], [15, 50, 100, 200, 'All']],
    selectedAction: handleBulkActions,
    ajax: {
        url: buildApiUrl('datatable'),
        data: (d) => ({
            ...d,
            status: $('select[name="status"]').val(),
            verified: $('input[name="verified"]').is(':checked') ? 'verified' : null,
            not_verified: $('input[name="not_verified"]').is(':checked') ? 'not_verified' : null
        })
    },
    columns: tableColumns,
    columnDefs: \$DatatableController.generateColumnDefs(tableColumnDefinitions),
    customFunctions: apiOperations,
    eventListeners: uiEventListeners
});

// Initialize create user modal
createModalLoader({
    triggerSelector: '.create',
    endpoint: buildApiUrl('create')
});

// Global access for table reload
window.RDT = reloadDataTable;

JS;

        $this->updateJsFile('user.js', $jsContent, 'js/dashboard');


        $routeContent = <<<ROUTE
        <?php

use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\Pages\UserController;
// datatable controller
use Illuminate\Support\Facades\Route;
// Datatable Controllers



Route::prefix('dashboard')->name('dashboard.')->group(function () {

    // Dashboard routes
    Route::controller(DashboardController::class)->group(function () {
        Route::get('/', 'index')->name('index');
    });


    // ======================================================================= //
    // ====================== START USER DATATABLE =========================== //
    // ======================================================================= //

    Route::controller(UserController::class)->prefix("users")->name("users.")->group(function () {
        Route::post('/update', 'update')->name('update');
        Route::get('/{id}/show', 'show')->name('show');
        Route::get('/datatable', 'datatable')->name('datatable');
        Route::patch('/{id}/status', 'status')->name('status');
    });
    Route::resource('users', UserController::class)->except(['show', 'update']);

    // ======================================================================= //
    // ====================== END USER DATATABLE ============================= //
    // ======================================================================= //


});

ROUTE;


        $this->updateRouteFile('dashboard.php', $routeContent);


        $indexContent = <<<VIEW
        <!---------------------------
    Layout
---------------------------->
@extends('dashboard.layout.index')

<!---------------------------
    Title
---------------------------->
@section('title', 'user')

<!---------------------------
    Toolbar
---------------------------->
@section('toolbar')
    @include('dashboard.components.toolbar', [
        'title' => 'Users',
        'currentPage' => 'User Management',
    ])
@endsection

<!---------------------------
    Columns
---------------------------->

@php
\$columns = ['name', 'email', 'Verified', 'Status', 'Created At', 'Action'];
@endphp

<!---------------------------
    Main Content
---------------------------->
@section('content')
    <x-lebify-table
    id="user-datatable"
    :columns="\$columns"

    {{-- create="true"                         // BY DEFAULT TRUE
    selected="true"                            // BY DEFAULT TRUE
    filter="true"                              // BY DEFAULT TRUE
    showCheckbox="true"                        // BY DEFAULT TRUE
    showSearch="true"                          // BY DEFAULT TRUE
    showColumnVisibility="true"                // BY DEFAULT TRUE
    columnVisibilityPlacement="bottom-end"     // BY DEFAULT BOTTOM-END
    columnSettingsTitle="Column Settingss"     // BY DEFAULT COLUMN SETTINGS
    columnToggles=""                           // BY DEFAULT EMPTY
    tableClass="table-class"                   // BY DEFAULT EMPTY
    searchPlaceholder="Search..."              // BY DEFAULT SEARCH...
    selectedText="Selected"                    // BY DEFAULT SELECTED
    selectedActionButtonClass="btn-success"    // BY DEFAULT btn-danger
    selectedActionButtonText="Delete Selected" // BY DEFAULT DELETE SELECTED
    selectedAction=""                          // BY DEFAULT EMPTY
    --}}
    >


{{-- start Filter Options --}}

@section('filter-options')
    <label class="form-check form-check-sm form-check-custom form-check-solid">
        <select class="form-select form-select-solid" data-control="select2" data-placeholder="Select Status" name="status">
            <option></option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
        </select>
    </label>
    <div class="separator border-2 my-3"></div>
    <div class="d-flex gap-3 justify-content-between">
        <label class="form-check mt-3 form-check-sm form-check-custom form-check-solid">
            <input type="checkbox" class="form-check-input" name="verified" value="verified">
            <span class="form-check-label">Verified</span>
        </label>
        <label class="form-check mt-3 form-check-sm form-check-custom form-check-solid">
            <input type="checkbox" class="form-check-input" name="not_verified" value="not_verified">
            <span class="form-check-label">Not Verified</span>
        </label>
    </div>
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
<x-lebify-modal modal-id="create-modal" size="lg" submit-form-id="createForm" title="Create"></x-lebify-modal>
<x-lebify-modal modal-id="edit-modal" size="lg" submit-form-id="editForm" title="Edit"></x-lebify-modal>
<x-lebify-modal modal-id="show-modal" size="lg" :show-submit-button="false" title="Show"></x-lebify-modal>

<!---------------------------
    Scripts
---------------------------->
@push('scripts')
    <script src="{{ asset('js/dashboard/user.js') }}" type="module" defer></script>
@endpush

VIEW;

        $this->updateViewFile('index.blade.php', $indexContent, 'dashboard/pages/user');

        $createContent = <<<VIEW
<form id="create-user-form" form-id="createForm" http-request route="{{ route('dashboard.users.store') }}"
    identifier="single-form-post-handler" feedback close-modal success-toast on-success="RDT">

    <div class="mb-3">
        <label for="name" class="form-label">name</label>
        <input type="text" feedback-id="name-feedback" class="form-control form-control-solid" name="name" id="name">
        <div id="name-feedback" class="invalid-feedback"></div>
    </div>
    <div class="mb-3">
        <label for="email" class="form-label">email</label>
        <input type="text" feedback-id="email-feedback" class="form-control form-control-solid" name="email" id="email">
        <div id="email-feedback" class="invalid-feedback"></div>
    </div>
</form>
VIEW;

        $this->updateViewFile('create.blade.php', $createContent, 'dashboard/pages/user/modal');

        $editContent = <<<VIEW
        <form id="edit-user-form" form-id="editForm" http-request route="{{ route('dashboard.users.update') }}"
    identifier="single-form-post-handler" feedback close-modal success-toast on-success="RDT">
    <input type="hidden" name="id" id="id" value="{{ \$user->id }}">

    <div class="mb-3">
        <label for="name" class="form-label">name</label>
        <input type="text" value="{{ \$user->name }}" feedback-id="name-feedback" class="form-control form-control-solid"
            name="name" id="name">
        <div id="name-feedback" class="invalid-feedback"></div>
    </div>
    <div class="mb-3">
        <label for="email" class="form-label">email</label>
        <input type="text" value="{{ \$user->email }}" feedback-id="email-feedback" class="form-control form-control-solid"
            name="email" id="email">
        <div id="email-feedback" class="invalid-feedback"></div>
    </div>
</form>

VIEW;

        $this->updateViewFile('edit.blade.php', $editContent, 'dashboard/pages/user/modal');

        $showContent = <<<VIEW
        <div class="d-flex flex-column">
    <div class="mb-3">
        <label class="form-label fw-bold">Name</label>
        <p class="text-gray-800">{{ \$user->name }}</p>
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold">Email</label>
        <p class="text-gray-800">{{ \$user->email }}</p>
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold">Created At</label>
        <p class="text-gray-800">{{ \$user->created_at->diffForHumans() }}</p>
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold">Status</label>
        <p class="text-gray-800">
            <span class="badge text-white {{ \$user->status == 'active' ? 'bg-success' : 'bg-danger' }}">
                {{ ucfirst(\$user->status) }}
            </span>
        </p>
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold">Email Verified</label>
        <p class="text-gray-800">
            <span class="badge text-white {{ \$user->email_verified_at ? 'bg-success' : 'bg-danger' }}">
                {{ \$user->email_verified_at ? 'Yes' : 'No' }}
            </span>
        </p>
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold">Last Updated</label>
        <p class="text-gray-800">{{ \$user->updated_at->diffForHumans() }}</p>
    </div>
</div>

VIEW;

        $this->updateViewFile('show.blade.php', $showContent, 'dashboard/pages/user/modal');
    }
}
