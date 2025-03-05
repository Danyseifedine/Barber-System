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
