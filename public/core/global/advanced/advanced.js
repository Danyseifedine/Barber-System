import { toggleSubmitButtonOnFormInput } from '../utils/functions.js';
import { HttpRequest } from '../services/httpRequest.js';
import { dataTableConfig, l10n } from '../config/app-config.js';

/**
 * DynamicContentManager - A class for managing dynamic content loading with filtering, caching and pagination
 *
 * @class
 * @description Handles dynamic content loading with support for filtering, caching, pagination and customizable loading states
 *
 * @param {Object} options - Configuration options
 * @param {(string|Element)} options.contentSection - Target element where content will be loaded (required)
 * @param {string} options.route - API endpoint route for fetching data (required)
 * @param {(string|Element)} [options.filterForm] - Form element containing filter inputs
 * @param {(string|Element)} [options.searchBtn] - Search/submit button element
 * @param {(string|Element)} [options.clearFilterBtn] - Clear filters button element
 * @param {Object} [options.cacheOptions] - Caching configuration
 * @param {boolean} [options.cacheOptions.enabled=false] - Enable/disable caching
 * @param {number} [options.cacheOptions.maxAge=600000] - Cache entry max age in ms (default 10 mins)
 * @param {number} [options.cacheOptions.maxEntries=10] - Maximum number of cache entries
 * @param {Object} [options.callbacks] - Callback functions
 * @param {Function} [options.callbacks.onSuccess] - Called after successful data fetch
 * @param {Function} [options.callbacks.onLoading] - Called when loading starts
 * @param {Function} [options.callbacks.onError] - Called when an error occurs
 * @param {Function} [options.callbacks.onCacheHit] - Called when data is retrieved from cache
 * @param {Object} [options.config] - Additional configuration
 * @param {boolean} [options.config.toggleSubmitButtonOnFormInput=false] - Toggle button state based on form validity
 * @param {string} [options.config.loadingSpinner] - Custom loading spinner HTML
 * @param {number} [options.config.debounceDelay=300] - Debounce delay for fetch requests in ms
 * @param {boolean} [options.config.autoAttachPagination=true] - Auto attach pagination event listeners
 * @param {Object} [options.axios] - Custom axios instance
 * @param {boolean} [options.config.updateURL=true] - Update URL with query parameters
 * @param {string} [options.config.queryParam='page'] - Query parameter name for page number
 *
 * @example
 * const manager = new DynamicContentManager({
 *   contentSection: '#content',
 *   route: '/api/data',
 *   filterForm: '#filterForm', // optional
 *   searchBtn: '#searchButton', // optional
 *   clearFilterBtn: '#clearButton', // optional
 *   toggleSubmitButtonOnFormInput: true, // optional (default: false, prevents button from being disabled when form is empty)
 *   cacheOptions: {
 *     enabled: true,
 *     maxAge: 300000,
 *     maxEntries: 5
 *   },
 *   callbacks: {
 *     onSuccess: (data, filterForm, searchBtn, clearFilterBtn, contentSection) => console.log('Data loaded:', data),
 *     onError: (error, filterForm, searchBtn, clearFilterBtn, contentSection) => console.error('Error:', error)
 *   },
 *   config: {
 *     toggleSubmitButtonOnFormInput: true,
 *     debounceDelay: 500
 *   }
 * });
 */
export class DynamicContentManager {
    constructor(options = {}) {
        // Required options
        this.contentSection = this._validateElement(options.contentSection, 'Content section');
        this.route = this._validateRoute(options.route);

        // Initialize cache manager
        this.cacheManager = new CacheManager({
            maxEntries: options.cacheOptions?.maxEntries || 10,
            maxAge: options.cacheOptions?.maxAge || 10 * 60 * 1000
        });

        // Cache enabled flag
        this.cacheEnabled = options.cacheOptions?.enabled || false;

        // Filter form options
        this.filterForm = options.filterForm ? this._validateElement(options.filterForm, 'Filter form') : null;
        this.searchBtn = options.searchBtn ? this._validateElement(options.searchBtn, 'Search button') : null;
        this.clearFilterBtn = options.clearFilterBtn ? this._validateElement(options.clearFilterBtn, 'Clear filter button') : null;

        // Callbacks
        this.callbacks = {
            onSuccess: options.callbacks?.onSuccess || (() => { }),
            onLoading: options.callbacks?.onLoading || (() => { }),
            onError: options.callbacks?.onError || (() => { }),
            onCacheHit: options.callbacks?.onCacheHit || (() => { })
        };

        // Configuration options
        this.config = {
            toggleSubmitButtonOnFormInput: options.config?.toggleSubmitButtonOnFormInput || false,
            loadingSpinner: options.config?.loadingSpinner || this._defaultLoadingSpinner(),
            debounceDelay: options.config?.debounceDelay || 300,
            autoAttachPagination: options.config?.autoAttachPagination !== false,
            updateURL: options.config?.updateURL !== false,
            queryParam: options.config?.queryParam || 'page'
        };

        // Get initial page from URL
        this.initialPage = this._getPageFromURL();

        // Debounce mechanism
        this.fetchDataDebounced = this._debounce(this.fetchData.bind(this), this.config.debounceDelay);

        // Axios instance
        this.axios = options.axios || window.axios;
        if (!this.axios) {
            throw new Error('Axios is required. Please provide axios in options or ensure it is globally available.');
        }

        // Initialize
        this.init();
    }

    _validateElement(element, name) {
        if (typeof element === 'string') {
            element = document.querySelector(element);
        }
        if (!element || !(element instanceof Element)) {
            throw new Error(`${name} element is invalid or not found`);
        }
        return element;
    }

    _validateRoute(route) {
        if (!route || typeof route !== 'string') {
            throw new Error('A valid API route is required');
        }
        return route;
    }

    _defaultLoadingSpinner() {
        return `
            <div class="d-flex justify-content-center align-items-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        `;
    }

    _debounce(func, delay) {
        let timeoutId;
        return function (...args) {
            const context = this;
            clearTimeout(timeoutId);
            timeoutId = setTimeout(() => func.apply(context, args), delay);
        };
    }

    _getPageFromURL() {
        const urlParams = new URLSearchParams(window.location.search);
        return parseInt(urlParams.get(this.config.queryParam)) || 1;
    }

    _updateURL(page, filters = {}) {
        if (!this.config.updateURL) return;

        const url = new URL(window.location.href);

        // Update page parameter
        if (page === 1) {
            url.searchParams.delete(this.config.queryParam);
        } else {
            url.searchParams.set(this.config.queryParam, page);
        }

        // Update filter parameters
        Object.entries(filters).forEach(([key, value]) => {
            if (value) {
                url.searchParams.set(key, value);
            } else {
                url.searchParams.delete(key);
            }
        });

        window.history.pushState({}, '', url);
    }

    init() {
        this.setupEventListeners();
        this.loadInitialData();

        if (this.filterForm && this.clearFilterBtn) {
            this.toggleClearButton();
        }

        // Handle browser back/forward buttons
        window.addEventListener('popstate', () => {
            const page = this._getPageFromURL();
            this.fetchData(page, false);
        });
    }

    setupEventListeners() {
        if (this.filterForm) {
            // Form submit event
            this.filterForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                await this.fetchData(1, true); // Reset to page 1 when filtering
            });

            // Search button click
            if (this.searchBtn) {
                this.searchBtn.addEventListener('click', async () => {
                    await this.fetchData(1, true);
                });
            }

            // Input change events for clear button toggle
            if (this.clearFilterBtn) {
                this.filterForm.querySelectorAll('input, select').forEach(element => {
                    element.addEventListener('change', () => {
                        this.toggleClearButton();
                    });

                    // Add input event listener for real-time filtering if needed
                    if (this.config.realTimeFiltering) {
                        element.addEventListener('input', () => {
                            this.fetchDataDebounced(1, true);
                        });
                    }
                });
            }
        }

        // Clear filters button
        if (this.clearFilterBtn && this.filterForm) {
            this.clearFilterBtn.addEventListener('click', async () => {
                this.filterForm.reset();
                this.filterForm.querySelectorAll('input, select').forEach(element => {
                    element.dispatchEvent(new Event('change', { bubbles: true }));
                });

                if (this.config.toggleSubmitButtonOnFormInput) {
                    toggleSubmitButtonOnFormInput(this.filterForm, this.searchBtn);
                }

                await this.fetchData(1, true);
                this.toggleClearButton();
            });
        }
    }

    toggleClearButton() {
        if (!this.clearFilterBtn || !this.filterForm) return;

        const formData = new FormData(this.filterForm);
        const hasValue = Array.from(formData.values()).some(value => value);

        this.clearFilterBtn.style.display = hasValue ? 'inline-block' : 'none';
    }

    showLoadingSection() {
        // If there's a custom loading callback, use it instead of default loading
        if (this.callbacks.onLoading !== (() => { })) {
            this.callbacks.onLoading(this.contentSection);
            return; // Exit early to prevent default loading spinner
        }

        // Default loading behavior only runs if no custom callback is provided
        this.contentSection.innerHTML = this.config.loadingSpinner;
    }

    async fetchData(page = 1, updateURL = true) {
        this.showLoadingSection();

        let params = new URLSearchParams();
        params.append('page', page);

        // Collect filter parameters
        let filters = {};
        if (this.filterForm) {
            const formData = new FormData(this.filterForm);
            for (let [key, value] of formData.entries()) {
                if (value) {
                    params.append(key, value);
                    filters[key] = value;
                }
            }
        }

        // Update URL if needed
        if (updateURL) {
            this._updateURL(page, filters);
        }

        // Check cache
        if (this.cacheEnabled) {
            const cacheKey = params.toString();
            const cachedData = this.cacheManager.get(cacheKey);

            if (cachedData) {
                this.contentSection.innerHTML = cachedData.html;
                this.callbacks.onCacheHit(cachedData);

                if (this.config.autoAttachPagination) {
                    this.attachPaginationListeners();
                }

                return;
            }
        }

        try {
            if (this.searchBtn) {
                this.searchBtn.disabled = true;
            }

            const response = await this.axios.get(`${this.route}?${params.toString()}`);

            if (response.status === 200) {
                this.contentSection.innerHTML = response.data.html;

                if (this.cacheEnabled) {
                    this.cacheManager.set(params.toString(), response.data);
                }

                if (this.config.autoAttachPagination) {
                    this.attachPaginationListeners();
                }

                this.callbacks.onSuccess(response.data, this.filterForm, this.searchBtn, this.clearFilterBtn, this.contentSection);
            }

        } catch (error) {
            console.error('Error fetching data:', error);
            this.contentSection.innerHTML = `
                <div class="alert alert-danger">
                    Error loading content: ${error.message}
                </div>
            `;
            this.callbacks.onError(error, this.filterForm, this.searchBtn, this.clearFilterBtn, this.contentSection);
        } finally {
            if (this.searchBtn) {
                this.searchBtn.disabled = false;
            }
        }
    }

    attachPaginationListeners() {
        const paginationLinks = document.querySelectorAll('.pagination .page-link');
        paginationLinks.forEach(link => {
            link.addEventListener('click', async (e) => {
                e.preventDefault();
                if (!link.closest('.page-item').classList.contains('disabled')) {
                    const page = link.getAttribute('data-page');
                    await this.fetchData(page, true);
                }
            });
        });
    }

    async loadInitialData() {
        await this.fetchData(this.initialPage, true);
    }

    clearCache() {
        this.cacheManager.clear();
    }
}

export class ModalLoader {
    static defaultLoadingSpinner = `
        <div class="d-flex justify-content-center align-items-center" style="height: 200px">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    `;

    constructor(config) {
        this.triggerSelector = config.triggerSelector;
        this.modalBodySelector = config.modalBodySelector;
        this.loadingHtml = config.loadingHtml || ModalLoader.defaultLoadingSpinner;
        this.endpoint = config.endpoint;
        this.onSuccess = config.onSuccess;
        this.onError = config.onError;
        this.autoInit = config.autoInit !== false; // Default to true if not specified

        this.modalBody = document.querySelector(this.modalBodySelector);

        // Initialize
        if (this.autoInit) {
            this.init();
        }
    }

    async init() {
        if (!this.modalBody) {
            console.error('Modal body element not found');
            return;
        }

        if (this.triggerSelector) {
            this.trigger = document.querySelector(this.triggerSelector);
            if (this.trigger) {
                this.trigger.addEventListener('click', () => this.handleModalOpen());
            }
        } else {
            // If no trigger is specified, load content immediately
            await this.handleModalOpen();
        }
    }

    async handleModalOpen() {
        try {
            this.showLoading();
            const response = await this.fetchContent();
            this.updateModalContent(response);
            if (this.onSuccess) this.onSuccess(response);
        } catch (error) {
            console.error('Error loading modal content:', error);
            if (this.onError) this.onError(error);
        }
    }

    showLoading() {
        if (this.modalBody) {
            this.modalBody.innerHTML = this.loadingHtml;
        }
    }

    async fetchContent() {
        return await HttpRequest.get(this.endpoint);
    }

    updateModalContent(response) {
        if (response && response.html && this.modalBody) {
            this.modalBody.innerHTML = response.html;
        }
    }

    // New method to manually load modal content
    async load() {
        await this.handleModalOpen();
    }
}

/**
 * SimpleWatcher - A lightweight class for observing DOM element additions and removals
 *
 * @class
 * @description Provides a simple way to watch for DOM elements being added or removed
 *
 * @param {Object} config - Configuration options
 * @param {string} config.targetSelector - CSS selector for the target element to observe
 * @param {string} [config.watchFor=''] - CSS selector for specific elements to watch for within target
 * @param {Function} [config.onElementFound] - Callback when matching elements are added
 * @param {Function} [config.onElementRemoved] - Callback when matching elements are removed
 *
 * @example
 * const watcher = new SimpleWatcher({
 *   targetSelector: '.content-section',
 *   watchFor: '.dynamic-element',
 *   onElementFound: () => console.log('Element added'),
 *   onElementRemoved: () => console.log('Element removed')
 * });
 *
 * // Control methods
 * watcher.disconnect(); // Stop watching
 * watcher.reconnect(); // Restart watching
 */
export class SimpleWatcher {
    constructor(config) {
        this.targetSelector = config.targetSelector;
        this.watchFor = config.watchFor || '';
        this.onElementFound = config.onElementFound || (() => { });
        this.onElementRemoved = config.onElementRemoved || (() => { });
        this.observer = null;

        this.initialize();
    }

    initialize() {
        const target = document.querySelector(this.targetSelector);

        if (!target) {
            console.warn(`Target element with selector "${this.targetSelector}" not found`);
            return;
        }

        this.observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                // Handle added nodes
                if (mutation.addedNodes.length) {
                    const hasMatchingElements = [...mutation.addedNodes].some(node =>
                        node.querySelector && (
                            this.watchFor ?
                                node.querySelector(this.watchFor) :
                                true
                        )
                    );

                    if (hasMatchingElements) {
                        this.onElementFound();
                    }
                }

                // Handle removed nodes
                if (mutation.removedNodes.length) {
                    const hasMatchingElements = [...mutation.removedNodes].some(node =>
                        node.querySelector && (
                            this.watchFor ?
                                node.querySelector(this.watchFor) :
                                true
                        )
                    );

                    if (hasMatchingElements) {
                        this.onElementRemoved();
                    }
                }
            });
        });

        this.observer.observe(target, {
            childList: true,
            subtree: true
        });
    }

    disconnect() {
        if (this.observer) {
            this.observer.disconnect();
            this.observer = null;
        }
    }

    reconnect() {
        if (!this.observer) {
            this.initialize();
        }
        return this;
    }
}

/**
 * LoadingBar - A reusable class for creating and managing loading bars
 *
 * @class LoadingBar
 * @exports LoadingBar
 *
 * @property {Object} options - Configuration options for the loading bar
 * @property {string} options.height - Height of the loading bar (default: '3px')
 * @property {string[]} options.colors - Array of colors for gradient background (default: ['#FFA500', '#FF8C00'])
 * @property {number} options.maxWidth - Maximum width percentage the bar will animate to before complete (default: 90)
 * @property {number} options.animationSpeed - Speed of the width animation in ms (default: 200)
 * @property {string} options.position - Position of bar, either 'top' or 'bottom' (default: 'top')
 * @property {HTMLElement} element - The loading bar DOM element
 * @property {number} interval - Reference to the animation interval
 */
export class LoadingBar {
    constructor(options = {}) {
        this.options = {
            height: options.height || '3px',
            colors: options.colors || ['#FFA500', '#FF8C00'],
            maxWidth: options.maxWidth || 90,
            animationSpeed: options.animationSpeed || 200,
            position: options.position || 'top',
            ...options
        };

        this.element = null;
        this.interval = null;
        this.create();
    }

    create() {
        this.element = document.createElement('div');

        const position = this.options.position === 'bottom' ? 'bottom: 0;' : 'top: 0;';
        const gradient = `linear-gradient(to right, ${this.options.colors.join(', ')})`;

        this.element.style.cssText = `
            position: fixed;
            ${position}
            left: 0;
            height: ${this.options.height};
            width: 0%;
            background: ${gradient};
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 9999;
            box-shadow: 0 0 10px rgba(255, 165, 0, 0.5);
            border-radius: 0 4px 4px 0;
            opacity: 0;
            transform: translateY(-100%);
        `;

        document.body.appendChild(this.element);

        // Trigger reflow for smooth entrance animation
        this.element.offsetHeight;
        this.element.style.opacity = '1';
        this.element.style.transform = 'translateY(0)';

        return this;
    }

    start() {
        let width = 0;
        let acceleration = 1;

        this.interval = setInterval(() => {
            if (width < this.options.maxWidth) {
                // Dynamic acceleration for more natural progress
                acceleration = Math.max(0.1, acceleration * 0.98);
                width += Math.random() * 8 * acceleration;

                // Add slight oscillation for more dynamic effect
                const oscillation = Math.sin(Date.now() / 500) * 0.5;
                const finalWidth = Math.min(this.options.maxWidth, width + oscillation);

                this.element.style.width = `${finalWidth}%`;
                this.element.style.boxShadow = `0 0 ${10 + oscillation * 5}px rgba(255, 165, 0, 0.5)`;
            }
        }, this.options.animationSpeed / 2);
        return this;
    }

    complete() {
        if (!this.element) return;

        this.element.style.transition = 'all 0.5s cubic-bezier(0.4, 0, 0.2, 1)';
        this.element.style.width = '100%';
        this.element.style.boxShadow = '0 0 20px rgba(255, 165, 0, 0.8)';

        setTimeout(() => {
            this.element.style.opacity = '0';
            this.element.style.transform = 'translateY(-100%)';
            setTimeout(() => this.remove(), 500);
        }, 300);
    }

    error() {
        if (!this.element) return;

        this.element.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
        this.element.style.background = 'linear-gradient(to right, #ff4444, #cc0000)';
        this.element.style.boxShadow = '0 0 15px rgba(255, 0, 0, 0.6)';

        // Shake animation
        const shake = [
            { transform: 'translateX(-8px)' },
            { transform: 'translateX(8px)' },
            { transform: 'translateX(-4px)' },
            { transform: 'translateX(4px)' },
            { transform: 'translateX(0)' }
        ];

        this.element.animate(shake, {
            duration: 500,
            iterations: 1
        });

        setTimeout(() => {
            this.element.style.opacity = '0';
            this.element.style.transform = 'translateY(-100%)';
            setTimeout(() => this.remove(), 500);
        }, 1000);
    }

    remove() {
        if (this.interval) {
            clearInterval(this.interval);
        }
        if (this.element) {
            this.element.remove();
            this.element = null;
        }
    }
}

/**
 * DatatableController is a class that handles the initialization of the DataTable library.
 *
 * @param {string} tableId - The ID of the table to be handled
 * @param {Object} [options] - The options for the DataTable
 * @param {Object} [options.customFunctions] - Custom functions to be added to the DataTable
 * @param {Object} [options.eventListeners] - Event listeners to be added to the DataTable
 * @param {Object} [options.ajax] - Ajax options for the DataTable
 * @param {Object} [options.select] - Select options for the DataTable
 * @param {Object} [options.lengthMenu] - Length menu options for the DataTable
 * @param {Object} [options.order] - Order options for the DataTable
 * @param {Object} [options.searchSelector] - Search selector for the DataTable
 * @param {Object} [options.filterSelector] - Filter selector for the DataTable
 * @param {Object} [options.stateSave] - State save options for the DataTable
 * @param {Object} [options.onDraw] - Function to be called when the DataTable is drawn
 *
 * @example
 * const usersDataTable = new $DatatableController('kt_datatable_example_1', {
 *
 *  lengthMenu: [[5, 10, 20, 50, -1], [5, 10, 20, 50, "All"]],
 *  toggleToolbar: true,
 *  initColumnVisibility: true,
 *
 *  selectedAction: (selectedIds) => {
 *
 *      console.log('Performing action on ids:', selectedIds);
 *
 *  },
 *
 *   ajax: {
 *       url: `${__API_CFG__.BASE_URL}/dashboard/users/data`,
 *       data: (d) => ({
 *           ...d,
 *           name_with_4_letter: document.querySelector('input[name="name_with_4_letter"]').checked,
 *           name_with_5_letter: document.querySelector('input[name="name_with_5_letter"]').checked
 *       })
 *   },
 *
 *    columns: [
 *        { data: 'id' },
 *        { data: 'name' },
 *        { data: 'email' },
 *        { data: 'created_at' },
 *       { data: 'status' },
 *       { data: null },
 *    ],
 *
 *    columnDefs: $DatatableController.generateColumnDefs([
 *        { targets: [0], htmlType: 'selectCheckbox' },
 *        { targets: [1], htmlType: 'badge', badgeClass: 'badge-light-danger' },
 *        {
 *            targets: [4], htmlType: 'toggle',
 *            checkWhen: (data, type, row) => {
 *                return data === 'in';
 *           },
 *            uncheckWhen: (data, type, row) => {
 *                return data === 'pending';
 *            }
 *        },
 *        { targets: [-1], htmlType: 'actions', className: 'text-center', actionButtons: { edit: true, delete: true, view: true } },
 *    ]),
 *
 *    // Custom functions
 *    customFunctions: {
 *
 *        delete: async function (endpoint, onSuccess, onError) {
 *            try {
 *                const result = await SweetAlert.deleteAction();
 *                if (result) {
 *                    const response = await HttpRequest.del(endpoint);
 *                    onSuccess(response);
 *                }
 *            } catch (error) {
 *                onError(error);
 *            }
 *        },
 *
 *        show: async function (id, endpoint, onSuccess, onError) {
 *            console.log("Show user", id);
 *        },
 *
 *        edit: async function (id, endpoint, onSuccess, onError) {
 *            console.log("Edit user", id);
 *        },
 *
 *        updateStatus: async function (id, newStatus, onSuccess, onError) {
 *            try {
 *               const response = await HttpRequest.put(`${__API_CFG__.BASE_URL}/dashboard/users/update-status/${id}`, { status: newStatus });
 *                onSuccess(response);
 *            } catch (error) {
 *                onError(error);
 *            }
 *        },
 *    },
 *
 *    eventListeners: [
 *        {
 *            event: 'click',
 *            selector: '.delete-btn',
 *            handler: function (id, event) {
 *                this.callCustomFunction(
 *                    'delete',
 *                    `${__API_CFG__.BASE_URL}/dashboard/users/delete/${id}`,
 *                    (res) => {
 *                        if (res.risk) {
 *                            SweetAlert.error();
 *                        } else {
 *                            SweetAlert.deleteSuccess();
 *                            this.reload();
 *                        }
 *                    },
 *                    (err) => { console.error('Error deleting user', err); }
 *                );
 *            }
 *        },
 *        {
 *            event: 'click',
 *            selector: '.status-toggle',
 *            handler: function (id, event) {
 *                const toggle = event.target;
 *                const newStatus = toggle.checked ? 'in' : 'pending';
 *                this.callCustomFunction('updateStatus', id, newStatus,
 *                    (res) => {
 *                        Toast.showSuccessToast('', res.message);
 *                        toggle.dataset.currentStatus = newStatus;
 *                    },
 *                    (err) => {
 *                        console.error('Error updating status', err);
 *                        SweetAlert.error('Failed to update status');
 *                        toggle.checked = !toggle.checked;
 *                    }
 *                );
 *            }
 *        },
 *        {
 *            event: 'click',
 *           selector: '.btn-show',
 *           handler: function (id, event) {
 *               this.callCustomFunction('show', id);
 *            }
 *        },
 *        {
 *            event: 'click',
 *            selector: '.btn-edit',
 *            handler: function (id, event) {
 *               this.callCustomFunction('edit', id);
 *            }
 *        }
 *    ],
 * });
 *
 *
 * function addUser() {
 *    FunctionUtility.closeModalWithButton('kt_modal_stacked_2', '.close-modal', () => {
 *        FunctionUtility.clearForm('#add-user-form');
 *    });
 *
 *    const addUserConfig = {
 *        formSelector: '#add-user-form',
 *        externalButtonSelector: '#add-user-button',
 *        endpoint: `${__API_CFG__.BASE_URL}/dashboard/users/create`,
 *        feedback: true,
 *        onSuccess: (res) => {
 *            Toast.showNotificationToast('', res.message)
 *            FunctionUtility.closeModal('kt_modal_stacked_2', () => {
 *                FunctionUtility.clearForm('#add-user-form');
 *            });
 *            usersDataTable.reload();
 *        },
 *        onError: (err) => { console.error('Error adding user', err); },
 *    };
 *
 * const form = new $SingleFormPostController(addUserConfig);
 * form.init();
 *
 * addUser();
 *
 */
export class $DatatableController {
    constructor(tableId, options = {}) {
        this.tableId = tableId;
        this.options = this.mergeOptions(options);
        this.dt = null;
        this.customFunctions = new Map();
        this.eventListeners = new Map();
        this.init();
    }

    mergeOptions(options) {
        const defaultOptions = {
            searchDelay: dataTableConfig.SEARCH_DELAY,
            processing: dataTableConfig.PROCESSING,
            serverSide: dataTableConfig.SERVER_SIDE,
            order: [[3, 'desc']],
            lengthMenu: [[dataTableConfig.LENGTH_MENU], [dataTableConfig.LENGTH_MENU_TEXT]],
            stateSave: dataTableConfig.STATE_SAVE,
            select: {
                style: 'multi',
                selector: 'td:first-child input[type="checkbox"]',
                className: 'row-selected'
            },
            ajax: {
                error: (xhr) => console.error('AJAX Error:', xhr)
            },

            // search cfg
            search: dataTableConfig.ENABLE_SEARCH,
            searchSelector: '[data-table-filter="search"]',

            // filter cfg
            filter: dataTableConfig.ENABLE_FILTER,
            filterBoxSelector: '.filter-toolbar',
            filterMenuSelector: '#filter-menu',
            filterSelector: '[data-table-filter="filter"]',
            resetFilterSelector: '[data-table-reset="filter"]',
            resetFilter: dataTableConfig.ENABLE_RESET_FILTER,

            // column cfg
            columnVisibility: dataTableConfig.ENABLE_COLUMN_VISIBILITY,
            columnVisibilitySelector: '.column-visibility-container',

            // create cfg
            createButtonSelector: '.create',

            //
            toggleToolbar: dataTableConfig.ENABLE_TOGGLE_TOOLBAR,
            selectedCountSelector: '[data-table-toggle-select-count="selected_count"]',
            selectedActionSelector: '[data-table-toggle-action-btn="selected_action"]',
            toolbarBaseSelector: '[data-table-toggle-base="base"]',
            toolbarSelectedSelector: '[data-table-toggle-selected="selected"]',

            // Custom action
            selectedAction: null,
        };
        return { ...defaultOptions, ...options };
    }

    init() {
        this.initDatatable();
        this.setupCustomFunctions();
        this.setupEventListeners();
        this.attachDefaultListeners();
        this.setupSelectAllCheckbox();
        this.attachResetListener();
    }

    initDatatable() {
        this.dt = $(`#${this.tableId}`).DataTable({
            ...this.options,
            language: {
                "sEmptyTable": l10n.getRandomTranslation('sEmptyTable'),
                "sInfo": l10n.getRandomTranslation('sInfo'),
                "sInfoEmpty": l10n.getRandomTranslation('sInfoEmpty'),
                "sInfoFiltered": l10n.getRandomTranslation('sInfoFiltered'),
                "sLoadingRecords": l10n.getRandomTranslation('sLoadingRecords'),
                // "sProcessing": l10n.getRandomTranslation('sProcessing'),
                "sSearch": l10n.getRandomTranslation('sSearch'),
                "sZeroRecords": l10n.getRandomTranslation('sZeroRecords'),
                "oPaginate": {
                    "sFirst": l10n.getRandomTranslation('sFirst'),
                    "sLast": l10n.getRandomTranslation('sLast'),
                    "sNext": l10n.getRandomTranslation('sNext'),
                    "sPrevious": l10n.getRandomTranslation('sPrevious')
                },
                "oAria": {
                    "sSortAscending": l10n.getRandomTranslation('sSortAscending'),
                    "sSortDescending": l10n.getRandomTranslation('sSortDescending')
                }
            }
        });

        // Initialize KTMenu after each draw
        this.dt.on('draw', () => {
            // Initialize KTMenu for new dropdowns
            KTMenu.init(); // Initialize all new menus
            KTMenu.createInstances(); // Create instances for new menus

            if (typeof this.options.onDraw === 'function') {
                this.options.onDraw.call(this);
            }

            if (l10n.currentLocale == "ar") {
                const pagText = document.getElementById('dt-length-1');
                pagText.style.marginLeft = '10px';
            }
        });
    }

    resetCheckboxes() {
        let filterMenu = document.querySelector(this.options.filterBoxSelector);
        const inputs = filterMenu.querySelectorAll('input');
        inputs.forEach(input => {
            input.value = '';
            if (input.type === 'checkbox') {
                input.checked = false;
            }
        });
    }

    attachResetListener() {
        if (!this.options.resetFilter) return;

        const resetButton = document.querySelector(this.options.resetFilterSelector);
        if (resetButton) {
            resetButton.addEventListener('click', () => {
                let filterMenu = document.querySelector(this.options.filterBoxSelector);
                const inputs = filterMenu.querySelectorAll('input, select');
                inputs.forEach(input => {
                    if (input.type === 'checkbox') {
                        input.checked = false;
                    } else if (input.tagName === 'SELECT') {
                        input.value = '';
                        if ($(input).data('select2')) {
                            $(input).val(null).trigger('change');
                        }
                    } else {
                        input.value = '';
                    }
                });
                this.reload();
            });
        }
    }

    initToggleToolbar() {
        if (!this.options.toggleToolbar) return;

        const container = document.querySelector(`#${this.tableId}`);
        const actionButton = document.querySelector(this.options.selectedActionSelector);

        container.addEventListener('change', (e) => {
            if (e.target.type === 'checkbox' && e.target.classList.contains('row-select-checkbox')) {
                setTimeout(() => this.toggleToolbars(), 50);
            }
        });

        if (actionButton && this.options.selectedAction) {
            actionButton.addEventListener('click', () => {
                const selectedIds = this.getSelectedIds();
                this.options.selectedAction(selectedIds, () => this.reload());
            });
        }
    }

    getSelectedIds() {
        const selectedCheckboxes = document.querySelectorAll(`#${this.tableId} tbody input.row-select-checkbox:checked`);
        return Array.from(selectedCheckboxes).map(checkbox => checkbox.value);
    }

    toggleToolbars() {
        if (!this.options.toggleToolbar) return;

        const container = document.querySelector(`#${this.tableId}`);
        const toolbarBase = document.querySelector(this.options.toolbarBaseSelector);
        const toolbarSelected = document.querySelector(this.options.toolbarSelectedSelector);
        const selectedCount = document.querySelector(this.options.selectedCountSelector);
        const allCheckboxes = container.querySelectorAll('tbody .row-select-checkbox');
        const filterToolbar = document.querySelector(this.options.filterBoxSelector);
        const createButton = document.querySelector(this.options.createButtonSelector);

        let checkedState = false;
        let count = 0;

        allCheckboxes.forEach(c => {
            if (c.checked) {
                checkedState = true;
                count++;
            }
        });

        if (toolbarSelected && toolbarBase && selectedCount) {
            if (checkedState) {
                selectedCount.innerHTML = `${count}`;
                toolbarBase.classList.add('d-none');
                toolbarSelected.classList.remove('d-none');
                createButton.classList.add('d-none');
                filterToolbar.classList.add('d-none');
            } else {
                toolbarBase.classList.remove('d-none');
                toolbarSelected.classList.add('d-none');
                createButton.classList.remove('d-none');
                filterToolbar.classList.remove('d-none');
            }
        } else {
            console.error('One or more toolbar elements not found');
        }
    }

    initColumnVisibility() {
        if (!this.options.columnVisibility) return;

        const container = document.querySelector(`#${this.tableId}_wrapper`);
        if (!container) return;

        const menuBody = document.getElementById('column-toggles');
        if (!menuBody) return;

        menuBody.innerHTML = '';

        this.dt.columns().every(function (index) {
            const column = this;
            const title = column.header().textContent.trim();

            const toggleContainer = document.createElement('div');
            toggleContainer.className = 'form-check form-switch form-check-custom form-check-solid mb-3';

            const checkbox = document.createElement('input');
            checkbox.className = 'form-check-input';
            checkbox.type = 'checkbox';
            checkbox.checked = column.visible();
            checkbox.id = `column_toggle_${index}`;

            const label = document.createElement('label');
            label.className = 'form-check-label';
            label.htmlFor = `column_toggle_${index}`;
            label.textContent = title;

            checkbox.addEventListener('change', function () {
                column.visible(this.checked);
            });

            toggleContainer.appendChild(checkbox);
            toggleContainer.appendChild(label);
            menuBody.appendChild(toggleContainer);
        });

        // Add the button container to the DataTable wrapper if it's not already added
        const buttonContainer = document.querySelector('.column-visibility-container');
        const tableControlsContainer = container.querySelector('.dataTables_wrapper .row:first-child .col-sm-6:last-child');
        if (tableControlsContainer && !tableControlsContainer.contains(buttonContainer)) {
            tableControlsContainer.appendChild(buttonContainer);
        }
        KTMenu.createInstances();
    }

    attachDefaultListeners() {
        if (this.options.search) this.attachSearchListener();
        if (this.options.filter) this.attachFilterListener();
        if (this.options.toggleToolbar) this.initToggleToolbar();
        if (this.options.resetFilter) this.attachResetListener();
        if (this.options.columnVisibility) this.initColumnVisibility();
    }

    attachFilterListener() {
        const filterElement = document.querySelector(this.options.filterSelector);
        if (filterElement) {
            filterElement.addEventListener('click', () => this.reload());
        }
    }

    attachSearchListener() {
        const searchElement = document.querySelector(this.options.searchSelector);
        if (searchElement) {
            searchElement.addEventListener('keyup', (e) => {
                this.dt.search(e.target.value).draw();
            });
        }
    }

    setupCustomFunctions() {
        if (this.options.customFunctions) {
            for (const [name, func] of Object.entries(this.options.customFunctions)) {
                this.addCustomFunction(name, func);
            }
        }
    }

    addCustomFunction(name, func) {
        this.customFunctions.set(name, func.bind(this));
    }

    setupEventListeners() {
        if (this.options.eventListeners) {
            for (const listener of this.options.eventListeners) {
                this.addEventListener(listener.event, listener.selector, listener.handler);
            }
        }
    }

    addEventListener(event, selector, handler) {
        const wrappedHandler = (e) => {
            const id = e.currentTarget.getAttribute('data-id');
            handler.call(this, id, e);
        };
        $(`#${this.tableId}`).on(event, selector, wrappedHandler);

        if (!this.eventListeners.has(event)) {
            this.eventListeners.set(event, new Map());
        }
        this.eventListeners.get(event).set(selector, wrappedHandler);
    }

    removeEventListener(event, selector) {
        if (this.eventListeners.has(event) && this.eventListeners.get(event).has(selector)) {
            $(`#${this.tableId}`).off(event, selector, this.eventListeners.get(event).get(selector));
            this.eventListeners.get(event).delete(selector);
        }
    }

    setupSelectAllCheckbox() {
        const tableId = this.tableId;
        const selectAllCheckbox = document.querySelector(`#${tableId} .select-all-checkbox`);

        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('click', (e) => {
                const isChecked = e.target.checked;
                const rowCheckboxes = document.querySelectorAll(`#${tableId} .row-select-checkbox`);

                rowCheckboxes.forEach(checkbox => {
                    checkbox.checked = isChecked;
                });

                this.toggleToolbars();
            });

            // Update "select all" checkbox state when individual checkboxes change
            document.querySelector(`#${tableId} tbody`).addEventListener('change', (e) => {
                if (e.target.classList.contains('row-select-checkbox')) {
                    const allCheckboxes = document.querySelectorAll(`#${tableId} .row-select-checkbox`);
                    const checkedCheckboxes = document.querySelectorAll(`#${tableId} .row-select-checkbox:checked`);
                    selectAllCheckbox.checked = allCheckboxes.length === checkedCheckboxes.length;

                    this.toggleToolbars();
                }
            });
        }
    }

    reload() {
        this.dt.ajax.reload(null, false);
    }

    callCustomFunction(functionName, ...args) {
        if (this.customFunctions.has(functionName)) {
            return this.customFunctions.get(functionName)(...args);
        } else {
            console.error(`Custom function ${functionName} not found`);
        }
    }

    destroy() {
        this.dt.destroy();
        this.eventListeners.forEach((listeners, event) => {
            listeners.forEach((_, selector) => {
                this.removeEventListener(event, selector);
            });
        });
        this.customFunctions.clear();
        this.eventListeners.clear();
    }

    getDataTable() {
        return this.dt;
    }

    static generateColumnDefs(columnConfigs) {
        return columnConfigs.map(config => {
            const {
                htmlType, targets, orderable = dataTableConfig.ORDERABLE,
                className = '', customRender, checkWhen,
                uncheckWhen, hrefFunction, dataClassName = '',
                actionButtons = dataTableConfig.ACTION_BUTTONS,
                badgeClass = ''
            } = config;

            let renderFunction;

            switch (htmlType) {

                case 'link':
                    renderFunction = function (data, type, row) {
                        const href = typeof hrefFunction === 'function' ? hrefFunction(data, type, row) : data;
                        return `<a href="${href}" target="_blank" class="${dataClassName}">${data}</a>`;
                    };
                    break;

                case 'number':
                    renderFunction = function (data) {
                        return `<span class="${dataClassName}">${Number(data).toLocaleString()}</span>`;
                    };
                    break;

                case 'badge':
                    renderFunction = function (data) {
                        return `<span class="badge ${badgeClass} ${dataClassName}">${data}</span>`;
                    };
                    break;

                case 'icon':
                    renderFunction = function (data) {
                        return `<i class="${data} ${dataClassName}"></i>`;
                    };
                    break;

                case 'image':
                    renderFunction = function (data) {
                        return `<div style="width: 50px; height: 50px; background-image: url('${data}'); background-size: cover; background-position: center; background-repeat: no-repeat; border-radius: 4px;"></div>`;
                    };
                    break;

                case 'toggle':
                    renderFunction = function (data, type, row, meta) {
                        // Default check/uncheck conditions if not provided
                        const defaultCheckWhen = (data) => {
                            return data == true || data == "1" || data == "show" || data == 1 ||
                                data == "true" || data == "active" || data == "on" ||
                                data == "Active" || data == "Show" || data == "available" ||
                                data == "Available";
                        };

                        const defaultUncheckWhen = (data) => {
                            return data == false || data == "0" || data == "hide" || data == 0 ||
                                data == "false" || data == "inactive" || data == "off" ||
                                data == "Inactive" || data == "Hide" || data == "unavailable" ||
                                data == "Unavailable";
                        };

                        // Use provided functions or defaults
                        const checkFunction = typeof checkWhen === 'function' ? checkWhen : defaultCheckWhen;
                        const uncheckFunction = typeof uncheckWhen === 'function' ? uncheckWhen : defaultUncheckWhen;

                        const isChecked = checkFunction(data, type, row);
                        const isUnchecked = uncheckFunction(data, type, row);

                        if (isChecked && isUnchecked) {
                            console.warn("Both checkWhen and uncheckWhen are defined. Only checkWhen will be considered.");
                        }

                        // Generate unique ID using column name or index
                        const columnName = meta.settings.aoColumns[meta.col].data || meta.col;
                        const uniqueId = `${columnName}_${row.id}`;

                        return `
            <div class="form-check form-switch">
                <input class="form-check-input ${dataClassName}" type="checkbox"
                    id="${uniqueId}"
                    ${isChecked || (data === 'active' && !isUnchecked) ? 'checked' : ''}
                    data-id="${row.id}">
            </div>
        `;
                    };
                    break;

                case 'selectCheckbox':
                    renderFunction = function (data, type, row) {
                        return `
                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                <input class="form-check-input row-select-checkbox" type="checkbox" value="${row.id}" />
                            </div>
                        `;
                    };
                    break;

                case 'actions':
                    renderFunction = function (data) {
                        const generateButton = (action, config) => {
                            // If config is null/undefined or false, don't render the button
                            if (!config) return '';

                            // If config is true, use default modal configuration
                            const buttonConfig = config === true ? { type: 'modal' } : config;
                            const type = buttonConfig.type || 'modal'; // Default to modal
                            const baseClasses = 'btn datatable-btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1';

                            // Button configurations
                            const buttonConfigs = {
                                edit: {
                                    icon: 'bi bi-pencil fs-5',
                                    color: '#007bff',
                                    title: 'Edit',
                                    class: 'btn-edit data-table-action-edit',
                                    modalTarget: '#edit-modal'
                                },
                                view: {
                                    icon: 'bi bi-eye fs-5',
                                    color: '#28a745',
                                    title: 'View',
                                    class: 'btn-show mx-2 data-table-action-show',
                                    modalTarget: '#show-modal'
                                },
                                delete: {
                                    icon: 'bi bi-trash3-fill fs-5',
                                    color: '#dc3545',
                                    title: 'Delete',
                                    class: 'delete-btn mx-2 data-table-action-delete'
                                }
                            };

                            const btnConfig = buttonConfigs[action];
                            if (!btnConfig) return '';

                            // Attributes based on type
                            let additionalAttrs = '';
                            if (type === 'modal') {
                                const modalTarget = buttonConfig.modalTarget || btnConfig.modalTarget;
                                additionalAttrs = `data-bs-toggle="modal" data-bs-target="${modalTarget}"`;
                            } else if (type === 'redirect') {
                                additionalAttrs = `href="${buttonConfig.url || '#'}"`;
                            }

                            // Element type based on redirect
                            const element = type === 'redirect' ? 'a' : 'button';

                            return `
                                    <${element}
                                        data-id="${data.id}"
                                        ${additionalAttrs}
                                        type="button"
                                        class="${baseClasses} ${btnConfig.class}"
                                        data-action-type="${type || 'none'}"
                                        data-bs-toggle="tooltip"
                                        data-bs-placement="top"
                                        title="${btnConfig.title}">
                                        <i class="${btnConfig.icon}" style="color: ${btnConfig.color};"></i>
                                    </${element}>
                                `;
                        };

                        return `
                                <div class="btn-group" role="group">
                                    ${generateButton('edit', actionButtons.edit)}
                                    ${generateButton('view', actionButtons.view)}
                                    ${generateButton('delete', actionButtons.delete)}
                                </div>`;
                    };
                    break;

                case 'dropdownActions':
                    renderFunction = function (data, type, row) {
                        const generateDropdownItem = (action, config) => {
                            if (!config) return '';

                            if (config.divider) {
                                if (config.showIf && typeof config.showIf === 'function' && !config.showIf(row)) {
                                    return ''; // Don't show divider if showIf returns false
                                }
                                return '<div class="separator my-2"></div>';
                            }

                            if (config.showIf && typeof config.showIf === 'function' && !config.showIf(row)) {
                                return ''; // Don't render this item if showIf returns false
                            }

                            const {
                                icon = 'bi bi-gear',
                                text = action.charAt(0).toUpperCase() + action.slice(1),
                                class: customClass = '',
                                menuItemClass = '',
                                type = null,
                                modalTarget,
                                url,
                                callback,
                                color = 'primary'
                            } = config;

                            let additionalAttrs = '';
                            let finalClass = customClass; // Create mutable class variable

                            if (type) {
                                if (type !== 'modal' && type !== 'redirect' && type !== 'callback') {
                                    console.error(`Invalid type "${type}" for action "${action}". Only "modal", "redirect", "callback" or null are allowed.`);
                                    return '';
                                }

                                if (type === 'modal') {
                                    if (!modalTarget) {
                                        console.error(`modalTarget is required for modal action "${action}"`);
                                        return '';
                                    }
                                    additionalAttrs = `data-bs-toggle="modal" data-bs-target="${modalTarget}"`;
                                } else if (type === 'redirect') {
                                    if (!url) {
                                        console.error(`url is required for redirect action "${action}"`);
                                        return '';
                                    }
                                    const finalUrl = typeof url === 'function' ? url(row) : url;
                                    additionalAttrs = `href="${finalUrl}"`;
                                } else if (type === 'callback') {
                                    if (!callback || typeof callback !== 'function') {
                                        console.error(`callback function is required for callback action "${action}"`);
                                        return '';
                                    }
                                    const callbackId = `callback_${action}_${Math.random().toString(36).substr(2, 9)}`;
                                    window[callbackId] = {
                                        callback,
                                        rowData: row
                                    };
                                    additionalAttrs = `data-callback-id="${callbackId}"`;
                                    finalClass += ' callback-action'; // Use mutable variable
                                }
                            }

                            return `
                                <div class="menu-item px-3 ${menuItemClass}">
                                    <a class="menu-link px-3 ${finalClass}"
                                       data-id="${data.id}"
                                       ${additionalAttrs}
                                       style="cursor: pointer"
                                       ${type ? `data-action-type="${type}"` : ''}>
                                        <span class="menu-icon me-3">
                                            <i class="${icon} fs-6 text-${color}"></i>
                                        </span>
                                        <span class="menu-title">${text}</span>
                                    </a>
                                </div>
                            `;
                        };

                        const dropdownId = `dropdown_${data.id}_${Math.random().toString(36).substr(2, 9)}`;

                        return `
                            <div class="d-flex justify-content-end">
                                <div class="dropdown ${actionButtons.containerClass || ''}" id="${dropdownId}">
                                    <button type="button"
                                            class="btn btn-sm btn-icon btn-light btn-active-light-primary ${actionButtons.buttonClass || ''}"
                                            data-kt-menu-trigger="click"
                                            data-kt-menu-placement="bottom-end">
                                        <i class="bi bi-three-dots fs-2"></i>
                                    </button>
                                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-semibold w-200px py-3 ${actionButtons.menuClass || ''}"
                                         data-kt-menu="true">
                                        ${Object.entries(actionButtons).map(([action, config]) =>
                            generateDropdownItem(action, config)
                        ).join('')}
                                    </div>
                                </div>
                            </div>
                        `;
                    };
                    break;

                default:
                    renderFunction = customRender || function (data) {
                        return `<span class="${dataClassName}">${data}</span>`;
                    };
                    break;
            }

            // Add event listener for callback actions using event delegation
            document.addEventListener('click', function (e) {
                if (e.target.closest('.callback-action')) {
                    const link = e.target.closest('.callback-action');
                    const callbackId = link.getAttribute('data-callback-id');
                    if (callbackId && window[callbackId]) {
                        const { callback, rowData } = window[callbackId];
                        callback(rowData);
                        // Clean up
                        delete window[callbackId];
                    }
                }
            }, { capture: true });

            return {
                targets: targets,
                orderable: orderable,
                className: className,
                render: renderFunction
            };
        });
    }
}

