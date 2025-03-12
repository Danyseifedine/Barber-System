import { handlers } from '../config/app-config.js';
import { errorHandler } from '../utils/classes/error-utils.js';

export class ModalLoadingManager {
    constructor() {
        this.config = handlers.modalHandler;
    }

    setLoading(modal, isLoading) {
        if (!modal) {
            errorHandler.logMissingAttributeError(
                'ModalLoadingManager',
                'modal element',
                'DOM',
                'in the DOM',
                'The modal element is required to manage its loading state'
            );
            return;
        }

        const contentElement = modal.querySelector('.modal-content');
        if (!contentElement) {
            console.error('Modal content element not found');
            return;
        }

        if (isLoading) {
            this.showLoading(contentElement);
        } else {
            this.hideLoading(contentElement);
        }
    }

    showLoading(contentElement) {
        const loadingHTML = `
            <div class="modal-body d-flex justify-content-center align-items-center" style="min-height: 200px;">
                <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        `;
        contentElement.innerHTML = loadingHTML;
    }

    hideLoading(contentElement) {
        // We don't need to do anything here, as the content will be replaced
    }

    showError(modal, errorMessage) {
        const contentElement = modal.querySelector('.modal-content');
        if (!contentElement) {
            console.error('Modal content element not found');
            return;
        }

        contentElement.innerHTML = `
            <div class="modal-header">
                <h5 class="modal-title text-danger">Error</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger" role="alert">
                    ${errorMessage}
                </div>
            </div>
        `;
    }

    updateContent(modal, html) {
        const contentElement = modal.querySelector('.modal-content');
        if (contentElement) {
            contentElement.innerHTML = html;
        } else {
            console.error('Modal content element not found');
        }
    }
}
