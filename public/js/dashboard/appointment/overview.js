/*=============================================================================
 * Appointment Management Module
 *
 * This module handles all appointment-related operations in the dashboard including:
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
import { initSelect2 } from '../../../core/global/utils/functions.js';

/*---------------------------------------------------------------------------
 * Utility Functions
 * @function defaultErrorHandler - Global error handler for consistency
 * @function reloadDataTable - Refreshes the DataTable after operations
 * @function buildApiUrl - Constructs API endpoints for appointment operations
 *--------------------------------------------------------------------------*/
const defaultErrorHandler = (err) => console.error('Error:', err);
const reloadDataTable = () => appointmentTable.reload();
const buildApiUrl = (path) => `${DASHBOARD_URL}/appointments/${path}`;

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
        this.callCustomFunction('_SHOW_', id, buildApiUrl(`${id}/show`));
    },

    edit: function (id) {
        this.callCustomFunction('_EDIT_', id, buildApiUrl(`${id}/edit`), (response) => {
            initSelect2('#user_id', "#edit-modal");

            $("#start_time").flatpickr({
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
            });

            $("#end_time").flatpickr({
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
            });

        });
    },

    status: function (id, status) {
        this.callCustomFunction('_PATCH_', buildApiUrl(`${status}/${id}/status`), (response) => {
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
 * Defines the structure and behavior of the Appointment management table
 *--------------------------------------------------------------------------*/
const tableColumns = [
    {
        "data": "id"
    },
    {
        "data": "user_id",
        "title": "Customer"
    },
    {
        "data": "appointment_date",
        "title": "Appointment Date"
    },
    {
        "data": "start_time",
        "title": "Start Time"
    },
    {
        "data": "end_time",
        "title": "End Time"
    },
    {
        "data": "status",
        "title": "Status"
    },
    {
        "data": "final_price",
        "title": "Final Price",
        "className": "text-center"
    },
    {
        "data": null,
        "className": "text-end"
    }
];

const tableColumnDefinitions = [
    { targets: [0], orderable: false, htmlType: 'selectCheckbox' },
    {
        targets: [6], orderable: false, customRender: (data) => {
            return `<span class="badge bg-primary">${data} $</span>`
        }
    },
    {
        targets: [5], orderable: false, customRender: (data) => {
            return data == 'scheduled'
                ? `<span class="badge bg-primary">${data}</span>`
                : data == 'completed' ? `<span class="badge bg-success">${data}</span>`
                    : `<span class="badge bg-danger">${data}</span>`
        },
    },
    {
        targets: [-1],
        htmlType: 'dropdownActions',
        className: 'text-center',
        orderable: false,
        containerClass: 'bg-danger',
        actionButtons: {
            edit: {
                icon: 'bi bi-pencil',
                text: 'Edit',
                class: 'btn-edit',
                type: 'modal',
                modalTarget: '#edit-modal',
                color: 'primary'
            },
            divider1: { divider: true },
            view: {
                icon: 'bi bi-eye',
                text: 'View Details',
                class: 'btn-show',
                type: 'modal',
                modalTarget: '#show-modal',
                color: 'info'
            },
            divider2: { divider: true },
            delete: {
                icon: 'bi bi-trash',
                text: 'Delete',
                class: 'delete-btn',
                color: 'danger'
            },
            divider3: { divider: true, showIf: (row) => row.status !== 'completed' },
            complete: {
                type: 'callback',
                callback: (row) => {
                    changeStatus('completed', row.id);
                },
                icon: 'bi bi-check-circle',
                class: 'btn-complete',
                text: 'Complete',
                color: 'success',
                showIf: (row) => row.status !== 'completed',
            },
            divider4: { divider: true, showIf: (row) => row.status !== 'cancelled' },
            cancel: {
                icon: 'bi bi-x-circle',
                text: 'Cancel',
                class: 'btn-cancel',
                type: 'callback',
                callback: (row) => {
                    changeStatus('cancelled', row.id);
                },
                color: 'danger',
                showIf: (row) => row.status !== 'cancelled',
            },
            divider5: { divider: true, showIf: (row) => row.status !== 'scheduled' },
            reschedule: {
                icon: 'bi bi-calendar-date',
                text: 'Reschedule',
                class: 'btn-reschedule',
                type: 'callback',
                callback: (row) => {
                    changeStatus('scheduled', row.id);
                },
                color: 'warning',
                showIf: (row) => row.status !== 'scheduled',
            }
        }
    },
];

/*---------------------------------------------------------------------------
 * Bulk Action Handler
 * Processes operations on multiple selected appointments
 * @param {Array} selectedIds - Array of selected appointment IDs
 *--------------------------------------------------------------------------*/
const handleBulkActions = (selectedIds) => {
    // Implementation for bulk actions
    // Example: Delete multiple appointments, change status, etc.
};

/*=============================================================================
 * DataTable Initialization
 * Creates and configures the main appointment management interface
 *============================================================================*/
export const appointmentTable = new $DatatableController('appointmentTable', {
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

// Initialize create appointment modal
createModalLoader({
    triggerSelector: '.create',
    endpoint: buildApiUrl('create'),
    onSuccess: (response) => {
        initSelect2('#user_id', "#create-modal");

        $("#start_time").flatpickr({
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
        });

        $("#end_time").flatpickr({
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
        });

        document.addEventListener('DOMContentLoaded', function () {
            const serviceCards = document.querySelectorAll('.service-card');

            serviceCards.forEach(card => {
                card.addEventListener('click', function (e) {
                    if (e.target.type !== 'checkbox') {
                        const checkbox = this.querySelector('.service-checkbox');
                        checkbox.checked = !checkbox.checked;
                        toggleCardSelection(this, checkbox.checked);
                    }
                });

                const checkbox = card.querySelector('.service-checkbox');
                checkbox.addEventListener('change', function () {
                    toggleCardSelection(card, this.checked);
                });
            });

            function toggleCardSelection(card, isSelected) {
                if (isSelected) {
                    card.classList.add('selected');
                } else {
                    card.classList.remove('selected');
                }
            }
        });
    }
});

// Global access for table reload
window.RDT = reloadDataTable;

// Add event listeners for status buttons

async function changeStatus(status, id) {
    try {
        const response = await HttpRequest.patch(buildApiUrl(`${status}/${id}/status`));
        reloadDataTable();
    } catch (error) {
        defaultErrorHandler(error);
    }
}
