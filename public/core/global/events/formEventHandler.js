import { dispatchFormEvent } from '../utils/functions.js';
import { events, responseTypes, validation } from '../config/app-config.js';
import { Toast } from '../notifications/toast.js';
import { l10n } from '../config/app-config.js';

export class FormEventHandler {
    static async handleSuccess(data, form, button) {
        const successHandler = form.getAttribute(events.attributes.success);

        if (successHandler && typeof window[successHandler] === 'function') {
            await window[successHandler](data, form, button);
        }

        dispatchFormEvent(form, 'formSubmitSuccess', data);

        this.handleResponseByType(data, form);
    }

    static async handleError(error, form, button, validationManager) {
        const feedback = form.hasAttribute(validation.attributes.feedback);

        if (feedback && error.response?.status === 422) {
            validationManager.clearFeedback(form);
            validationManager.handleErrors(error.response.data.errors, form);
        }

        if (error.response?.status === 429) {
            console.log(error.response)
            Toast.showErrorToast(' ', l10n.getToastMessage('tooManyRequestsMessage'));
        }

        const errorHandler = form.getAttribute(events.attributes.error);
        if (errorHandler && typeof window[errorHandler] === 'function') {
            await window[errorHandler](error, form, button);
        }

        dispatchFormEvent(form, 'formSubmitError', error);

        this.handleResponseByType(error.response?.data, form);
    }

    static handleResponseByType(response, form) {
        if (!response) return;

        const redirect = form.hasAttribute(responseTypes.redirect);
        const successToast = form.hasAttribute(responseTypes.successToast);
        const errorToast = form.hasAttribute(responseTypes.errorToast);
        const closeModal = form.hasAttribute(responseTypes.closeModal);

        if (response.success) {

            if (successToast && response.toast) {
                Toast.showSuccessToast(' ', response.toast.message);
            }

            if (redirect && response.redirect) {
                window.location.href = response.redirect;
            }

            if (closeModal) {
                const modal = form.closest('.modal');
                if (modal && typeof modal.hide === 'function') {
                    modal.hide();
                } else if (modal && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    const bsModal = bootstrap.Modal.getInstance(modal);
                    if (bsModal) {
                        bsModal.hide();
                    }
                }
            }

        } else {
            if (errorToast && response.message) {
                Toast.showErrorToast(' ', response.message);
            }
        }
    }
}
