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
