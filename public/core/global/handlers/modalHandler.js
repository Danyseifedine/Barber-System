import { Identifiable } from './base/identifiable.js';
import { errorHandler } from '../utils/classes/error-utils.js';
import { env, handlers, identifiers, events } from '../config/app-config.js';
import { Documentation } from '../doc/documentation.js';
import { ModalLoadingManager } from '../ui/modalLoadingManager.js';
import { ModalEventHandler } from '../events/modalEventHandler.js';

export class ModalHandler extends Identifiable {
    constructor(identifier) {
        super(identifier);
        this.modals = new Map();
        this.cache = new Map();
        this.config = handlers.modalHandler;
        this.loadingManager = new ModalLoadingManager();
        this.init();
    }

    init() {
        this.registerModals();
        this.setupEventListeners();
    }

    registerModals() {
        const modalElements = document.querySelectorAll(`[${this.config.attributes.identifier}="${this.getIdentifier()}"]`);
        modalElements.forEach(modalElement => {
            const modalId = modalElement.id;
            if (modalId) {
                this.modals.set(modalId, modalElement);
                this.setupModalTriggers(modalId);
            } else {
                errorHandler.logMissingAttributeError('ModalHandler', 'id', 'div', 'as an attribute on the modal element', 'Uniquely identifies the modal');
            }
        });
    }

    setupModalTriggers(modalId) {
        const triggers = document.querySelectorAll(`[data-bs-target="#${modalId}"]`);
        triggers.forEach(trigger => {
            trigger.addEventListener('click', (event) => this.handleModalTrigger(event, modalId));
        });
    }

    async handleModalTrigger(event, modalId) {
        event.preventDefault();
        const modal = this.modals.get(modalId);
        if (!modal) return;

        const shouldFetchContent = modal.hasAttribute(this.config.attributes.httpRequest);
        let fetchUrl = modal.getAttribute(this.config.attributes.fetchUrl);

        if (shouldFetchContent && fetchUrl) {
            const paramAttr = modal.getAttribute(this.config.attributes.params);
            const itemId = event.currentTarget.getAttribute(this.config.attributes.itemId);

            if (paramAttr && itemId) {
                fetchUrl += fetchUrl.includes('?') ? '&' : '?';
                fetchUrl += `${paramAttr}=${itemId}`;
            }

            await this.openModalWithContent(modal, fetchUrl);
        } else {
            this.openModal(modal);
        }
    }

    openModal(modal) {
        const bsModal = bootstrap.Modal.getInstance(modal) || new bootstrap.Modal(modal);
        bsModal.show();
    }

    async openModalWithContent(modal, fetchUrl) {
        this.openModal(modal);
        this.loadingManager.setLoading(modal, true);
        await this.handleEvent('loading', true, modal, fetchUrl);

        const shouldCache = modal.getAttribute(this.config.attributes.cache) === 'true';
        const cachedContent = shouldCache ? this.cache.get(fetchUrl) : null;

        if (cachedContent) {
            this.loadingManager.updateContent(modal, cachedContent);
            this.loadingManager.setLoading(modal, false);
            await this.handleEvent('loaded', cachedContent, modal, fetchUrl, { data: { success: true, html: cachedContent } });
        } else {
            try {
                const response = await this.fetchModalContent(fetchUrl);
                if (response.data.success) {
                    const content = response.data.html;
                    this.loadingManager.updateContent(modal, content);
                    if (shouldCache) {
                        this.cache.set(fetchUrl, content);
                    }
                    await this.handleEvent('success', content, modal, fetchUrl, response);
                } else {
                    throw new Error('Server returned success: false');
                }
            } catch (error) {
                console.error('Error fetching modal content:', error);
                this.loadingManager.showError(modal, 'An error occurred while loading content');
                await this.handleEvent('error', error, modal, fetchUrl);
            } finally {
                this.loadingManager.setLoading(modal, false);
                await this.handleEvent('loading', false, modal, fetchUrl);
            }
        }
    }

    async fetchModalContent(url) {
        return await axios.get(url, { cache: false });
    }

    setupEventListeners() {
        document.addEventListener('hidden.bs.modal', this.handleModalHidden.bind(this));
    }

    handleModalHidden(event) {
        const modal = event.target;
        if (modal.getAttribute(this.config.attributes.identifier) === this.getIdentifier()) {
            if (modal.hasAttribute(this.config.attributes.clearForms)) {
                this.clearForms(modal);
            }
            if (modal.hasAttribute(this.config.attributes.httpRequest) &&
                modal.getAttribute(this.config.attributes.cache) !== 'true') {
                this.resetModalContent(modal);
            }
        }
    }

    clearForms(modal) {
        const forms = modal.querySelectorAll('form');
        forms.forEach(form => {
            form.reset();
            this.clearValidationStates(form);
        });
    }

    clearValidationStates(form) {
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        form.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
    }

    resetModalContent(modal) {
        const contentElement = modal.querySelector('.modal-content');
        if (contentElement) {
            contentElement.innerHTML = '';
        }
        this.loadingManager.setLoading(modal, true);
    }

    async handleEvent(eventType, ...args) {
        const modal = args[1]; // modal is always the second argument
        const customHandler = modal.getAttribute(events.attributes[eventType]);

        // Execute default handler
        await ModalEventHandler[`handle${eventType.charAt(0).toUpperCase() + eventType.slice(1)}`](...args);

        // Execute custom handler if exists
        if (customHandler) {
            await ModalEventHandler.executeCustomHandler(customHandler, ...args);
        }
    }

    static documentation() {
        return Documentation.generate(
            'ModalHandler',
            'The ModalHandler class manages modals, including dynamic content fetching, loading states, form clearing, optional content caching, and custom event handling.',
            `// Initialize a ModalHandler
const modalHandler = new ModalHandler('${identifiers.modalHandler}');

// The ModalHandler is now active and will manage all modals with the specified identifier`,
            `<div class="modal fade" tabindex="-1" id="exampleModal"
    ${handlers.modalHandler.attributes.identifier}="${identifiers.modalHandler}"
    ${handlers.modalHandler.attributes.clearForms}
    ${handlers.modalHandler.attributes.httpRequest}
    ${handlers.modalHandler.attributes.fetchUrl}="/api/modal-content"
    ${handlers.modalHandler.attributes.params}="item-id"
    ${handlers.modalHandler.attributes.cache}="true"
    ${handlers.modalHandler.attributes.itemId}="1"
    ${events.attributes.success}="handleModalSuccess"
    ${events.attributes.error}="handleModalError"
    ${events.attributes.loading}="handleModalLoading"
    ${events.attributes.loaded}="handleModalLoaded">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Content will be dynamically loaded here -->
        </div>
    </div>
</div>
`,
            [
                { name: handlers.modalHandler.attributes.identifier, description: 'Identifies the modal handler instance', required: true },
                { name: handlers.modalHandler.attributes.clearForms, description: 'Indicates that forms within this modal should be cleared on close', required: false },
                { name: handlers.modalHandler.attributes.httpRequest, description: 'Indicates that the modal should fetch content', required: false },
                { name: handlers.modalHandler.attributes.fetchUrl, description: 'URL to fetch modal content', required: false },
                { name: handlers.modalHandler.attributes.params, description: 'Specifies the attribute name for additional parameters', required: false },
                { name: handlers.modalHandler.attributes.itemId, description: 'Specifies the attribute name for the item ID on the trigger element', required: false },
                { name: handlers.modalHandler.attributes.cache, description: 'Enables caching of fetched content', required: false },
                { name: events.attributes.success, description: 'Custom success event handler', required: false },
                { name: events.attributes.error, description: 'Custom error event handler', required: false },
                { name: events.attributes.loading, description: 'Custom loading state event handler', required: false },
                { name: events.attributes.loaded, description: 'Custom loaded event handler', required: false },
            ],
            [
                'Automatically registers all modals with the specified identifier on the page.',
                'Shows a loading spinner when the modal is opened (if http-request is set).',
                'Fetches modal content dynamically when the modal is triggered (if http-request and fetch URL are provided).',
                'Supports optional parameters to be added to the fetch URL.',
                'Updates the modal content with the fetched HTML.',
                'Optionally caches fetched content for faster subsequent loads.',
                'Displays error messages in the modal if content fetching fails.',
                'Clears forms within modals when the modal is closed (if specified).',
                'Resets modal content to loading state when closed (if http-request is set and caching is disabled).',
                'Ensures proper removal of modal backdrop and body classes when the modal is closed.',
                'Uses Axios for HTTP requests.',
                'Provides error logging for missing or invalid attributes.',
                'Extends the Identifiable class for consistent component identification.',
                'Supports both default and custom event handlers for success, error, loading, and loaded events.',
                'Passes additional context (url, response) to event handlers for more detailed handling.'
            ],
            `
<script>
function handleModalSuccess(content, modal, url, response) {
    console.log('Custom success handler:', { content, modal, url, response });
}

function handleModalError(error, modal, url) {
    console.error('Custom error handler:', { error, modal, url });
}

function handleModalLoading(isLoading, modal, url) {
    console.log('Custom loading handler:', { isLoading, modal, url });
}

function handleModalLoaded(content, modal, url, response) {
    console.log('Custom loaded handler:', { content, modal, url, response });
}
</script>
            `
        );
    }
}

env.isDevelopment && (window.ModalHandler = ModalHandler);
