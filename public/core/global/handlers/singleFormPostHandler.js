import { Identifiable } from './base/identifiable.js';
import { serializeFormData } from '../utils/functions.js';
import { FormEventHandler } from '../events/formEventHandler.js';
import { RateLimiter } from '../services/rateLimiter.js';
import { errorHandler } from '../utils/classes/error-utils.js';
import { env, handlers, validation, events } from '../config/app-config.js';
import { ButtonManager } from '../ui/buttonLoadingManager.js';
import { ValidationManager } from '../validation/validationManager.js';
import { Documentation } from '../doc/documentation.js';
import { ValidationHandler } from '../utils/validationHandler.js';
export class SingleFormPostHandler extends Identifiable {


    constructor(identifier) {
        super(identifier);
        new ValidationHandler();
        this.buttonManager = new ButtonManager();
        this.validationManager = new ValidationManager();
        this.init();
    }

    init() {
        document.addEventListener('click', this.handleButtonClick.bind(this));
    }

    async handleButtonClick(event) {
        const button = event.target.closest(`button[type="submit"][${handlers.singleFormPost.attributes.submitFormId}]`);
        if (!button) return;

        event.preventDefault();

        const { form, endpoint } = this.getFormDetails(button);
        if (!form || !endpoint) return;

        if (form.getAttribute(handlers.singleFormPost.attributes.handler) !== this.getIdentifier()) return;

        if (RateLimiter.isLimited(form)) {
            errorHandler.logWarning('Form submission rate limited');
            return;
        }

        await this.submitForm(form, button, endpoint);
    }

    getFormDetails(button) {
        const formId = button.getAttribute(handlers.singleFormPost.attributes.submitFormId);
        if (!formId) {
            errorHandler.logMissingAttributeError(
                'SingleFormPostHandler',
                handlers.singleFormPost.attributes.submitFormId,
                'button',
                'as an attribute on the submit button',
                'Identifies which form this submit button is associated with'
            );
            return {};
        }

        const form = document.querySelector(`form[${handlers.singleFormPost.attributes.formId}="${formId}"]`);
        if (!form) {
            errorHandler.logMissingAttributeError(
                'SingleFormPostHandler',
                handlers.singleFormPost.attributes.formId,
                'form',
                'as an attribute on the form element',
                'Uniquely identifies the form for submission handling'
            );
            return {};
        }

        if (!form.hasAttribute(handlers.singleFormPost.attributes.httpRequest)) {
            errorHandler.logMissingAttributeError(
                'SingleFormPostHandler',
                handlers.singleFormPost.attributes.httpRequest,
                'form',
                'as an attribute on the form element',
                'Indicates that this form should be submitted via AJAX'
            );
            return {};
        }

        const endpoint = form.getAttribute(handlers.singleFormPost.attributes.route);
        if (!endpoint) {
            errorHandler.logMissingAttributeError(
                'SingleFormPostHandler',
                handlers.singleFormPost.attributes.route,
                'form',
                'as an attribute on the form element',
                'Specifies the server endpoint for form submission'
            );
            return {};
        }

        return { form, endpoint };
    }

    async submitForm(form, button, endpoint) {
        const feedback = form.hasAttribute(validation.attributes.feedback);
        const loadingHandler = form.getAttribute(events.attributes.loading);
        const log = form.getAttribute(events.attributes.log);

        this.setLoading(true, form, button, loadingHandler);
        RateLimiter.setLimit(form);

        try {
            const serializationType = form.getAttribute(handlers.singleFormPost.attributes.serializeAs) || 'formdata';
            const data = serializeFormData(form, serializationType);

            const response = await axios.post(endpoint, data);

            if (log) console.log(response.data);

            if (feedback) this.validationManager.clearFeedback(form);

            await FormEventHandler.handleSuccess(response.data, form, button);
        } catch (error) {
            errorHandler.logError('Error submitting form', error);
            await FormEventHandler.handleError(error, form, button, this.validationManager);
        } finally {
            this.setLoading(false, form, button, loadingHandler);
        }
    }

    setLoading(isLoading, form, button, loadingHandler) {
        if (loadingHandler && typeof window[loadingHandler] === 'function') {
            window[loadingHandler](isLoading, form, button);
        } else {
            this.buttonManager.setLoading(button, isLoading);
        }
    }

    static documentation() {
        return Documentation.generate(
            'SingleFormPostHandler',
            'The SingleFormPostHandler class provides a powerful and flexible system for handling form submissions via AJAX, including form validation, rate limiting, and custom event handling.',
            `// Initialize a SingleFormPostHandler
    const formHandler = new SingleFormPostHandler('single-form-post-handler');

    // The SingleFormPostHandler is now active and will manage all forms with the specified identifier`,
            `<form form-id="unique-form-id"
       http-request
       route="/submit-endpoint"
       identifier="single-form-post-handler"
       serialize-as="formdata"
       feedback
       on-success="handleFormSuccess"
       on-error="handleFormError"
       on-loading="handleFormLoading"
       rate-limit="5000">
        <div class="form-group">
            <label for="email">Email address</label>
            <input type="email" class="form-control form-control-solid" id="email" name="email" required
                   feedback-id="email-feedback">
            <div id="email-feedback" class="invalid-feedback"></div>
        </div>
        <!-- More form fields go here -->
        <button type="submit" submit-form-id="unique-form-id">Submit</button>
    </form>`,
            [
                { name: 'form-id', description: 'Unique identifier for the form', required: true },
                { name: 'http-request', description: 'Indicates form should be handled by AJAX', required: true },
                { name: 'route', description: 'Server endpoint for form submission', required: true },
                { name: 'identifier', description: 'Specifies which handler should process form', required: true },
                { name: 'serialize-as', description: 'Specifies form serialization type', required: false, default: 'formdata' },
                { name: 'submit-form-id', description: 'Links submit button to its form', required: true },
                { name: 'feedback', description: 'Enables validation feedback', required: false },
                { name: 'redirect', description: 'Redirects to a specified URL after success', required: false },
                { name: 'success-toast', description: 'Displays a success toast message after success', required: false },
                { name: 'error-toast', description: 'Displays an error toast message after error', required: false },
                { name: 'on-success', description: 'Name of success event handler function', required: false },
                { name: 'on-error', description: 'Name of error event handler function', required: false },
                { name: 'on-loading', description: 'Name of loading event handler function', required: false },
                { name: 'log', description: 'Logs the response', required: false },
                { name: 'rate-limit', description: 'Sets rate limit for form submission (ms)', required: false, default: '0 (none)' },
                { name: 'feedback-id', description: 'ID of the element to display validation feedback for an input', required: false }
            ],
            [
                'Automatically handles form submissions for forms with matching attributes.',
                'Performs AJAX form submission with customizable serialization.',
                'Manages button loading states during submission.',
                'Handles form validation and displays error messages.',
                'Implements rate limiting to prevent rapid successive submissions.',
                'Allows custom event handlers for success, error, and loading states.',
                'Supports redirection and toast notifications based on server response.',
                'Uses event delegation for efficient event handling.',
                'Provides detailed error logging for missing or invalid attributes.',
                'Extends the Identifiable class for consistent component identification.',
                'Integrates with ButtonManager for button state management.',
                'Uses ValidationManager for form validation handling.',
                'Dispatches custom events for form submission success and error.'
            ],
            `// Custom event handlers example
    window.handleFormSuccess = function(data, form, button) {
        console.log('Form submitted successfully', data);
        // Perform any custom actions on successful submission
    };

    window.handleFormError = function(error, form, button) {
        console.error('Form submission error', error);
        // Handle errors, display custom error messages, etc.
    };

    window.handleFormLoading = function(isLoading, form, button) {
        console.log('Form loading state:', isLoading);
        // Customize loading behavior, e.g., show/hide a loading spinner
    };

    // Usage:
    // 1. Define these functions in your global scope
    // 2. Set the 'on-success', 'on-error', and 'on-loading' attributes in your HTML to the function names
    // 3. The SingleFormPostHandler will automatically call these functions during the form submission lifecycle

    // Event listeners for custom events
    document.addEventListener('formSubmitSuccess', function(event) {
        console.log('Form submitted successfully', event.detail);
        // Perform any global actions on successful form submission
    });

    document.addEventListener('formSubmitError', function(event) {
        console.error('Form submission error', event.detail);
        // Handle errors globally, e.g., show a global error message
    });`
        );
    }
}


env.isDevelopment && (window.SingleFormPostHandler = SingleFormPostHandler);



