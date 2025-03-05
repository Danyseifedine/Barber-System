import { Translator } from '../translation/translation.js';
/**
 * Application Configuration
 *
 * This file contains global configuration settings for the application.
 * It defines constants and default values used across different components.
 */

const currEnv = 'development';

// Initialize Translator
export const l10n = new Translator();

// Get the current URL
let url = window.location.href;

// Get the base URL
let baseUrl = url.split('/')[0] + '//' + url.split('/')[2];

// Base URL for API requests, dynamically includes the current locale
export const BASE_URL = `${baseUrl}/${l10n.currentLocale}`;

// Base local URL for development environment
export const LOCAL_URL = `${baseUrl}`;

// Default language for the application
export const APP_LANG = l10n.currentLocale;

export const DASHBOARD_URL = `${LOCAL_URL}/dashboard`;

/**
 * Environment Settings
 * Defines the current environment and provides boolean flags for easy checks.
 */
export const env = {
    type: currEnv,
    isDevelopment: currEnv === 'development', // New configuration to enable/disable dev tools
    isProduction: currEnv === 'production',   // New configuration to enable/disable dev tools
    enableDevTools: true,                     // New configuration to enable/disable dev tools
}


/**
 * Form Handling Configuration
 * Defines attributes and settings for form submission handling.
 */
export const handlers = {

    // Single Form Post Handler
    singleFormPost: {
        attributes: {
            formId: 'form-id',              // Unique identifier for the form
            submitFormId: 'submit-form-id', // Links submit button to its form
            httpRequest: 'http-request',    // Indicates AJAX submission
            route: 'route',                 // Specifies form submission endpoint
            handler: 'identifier',          // Specifies the form processing handler
            serializeAs: 'serialize-as',    // Defines form serialization method
        }
    },

    // Tab Handler
    tabHandler: {
        attributes: {
            handler: 'identifier',          // Specifies the form processing handler
            tabGroupId: 'tab-group-id',     // Specifies the tab group identifier
            tabId: 'tab-id',                // Specifies the tab identifier
            contentId: 'tab-content-id',    // Specifies the tab content identifier
            route: 'tab-route',             // Specifies the tab route
            initial: 'tab-initial',         // Specifies the initial tab
            tabUrl: 'tab-url',              // Specifies whether to update URL with tab ID
            cache: 'cache-tab',             // New attribute for caching
        },
        classes: {
            active: 'active',                // Specifies the active tab class
            loading: 'loading'               // Specifies the loading tab class
        },
        selectors: {
            tabGroup: '[identifier="tab-handler"]', // Specifies the tab group selector
            tabContent: '[tab-content-id]',         // Specifies the tab content selector
            tab: '[tab-id]',                        // Specifies the tab selector
            initialTab: '[tab-initial="true"]',     // Specifies the initial tab selector
        }
    },

    // Counter Handler
    counterHandler: {
        attributes: {
            id: 'counter-id',                   // Unique identifier for the counter
            init: 'counter-init',               // Initial counter value
            step: 'counter-step',               // Increment/decrement step
            min: 'counter-min',                 // Minimum allowed value
            max: 'counter-max',                 // Maximum allowed value
            display: 'counter-display',         // Element to display counter value
            increment: 'counter-inc',           // Increment button
            decrement: 'counter-dec',           // Decrement button
            onIncrease: 'on-increase',          // Custom increase function (button, currentValue, minValue, maxValue, updateFunction)
            onDecrease: 'on-decrease'           // Custom decrease function (button, currentValue, minValue, maxValue, updateFunction)
        },
        defaults: {
            init: 0,                            // Default initial value
            step: 1,                            // Default step value
            min: null,                          // Default minimum (no limit)
            max: null                           // Default maximum (no limit)
        }
    },

    // Modal Handler
    modalHandler: {
        attributes: {
            identifier: 'identifier',                // Identifies the modal handler instance
            clearForms: 'clear-forms',               // Indicates that forms within this modal should be cleared on close or successful submission
            fetchUrl: 'route',                       // URL to fetch modal content
            httpRequest: 'http-request',             // Indicates if the modal should fetch content
            params: 'params',                        // Specifies the attribute name for additional parameters
            itemId: 'item-id',                       // Specifies the attribute name for the item ID
            cache: 'modal-cache'                     // New attribute for caching
        },
    },
}


/**
 * Response Types
 * Defines attributes for response types.
 */
export const responseTypes = {
    successToast: 'success-toast', // Success toast
    errorToast: 'error-toast',     // Error toast
    redirect: 'redirect',          // Redirect to a new page
    closeModal: 'close-modal',      // Close the modal
}


/**
 * Button Configuration
 * Defines settings for various button types.
 */
export const button = {
    attributes: {
        loadingText: 'loading-text',    // Custom loading text
        spinner: 'spinner',             // Enables/disables spinner
        noLoadingText: 'no-loading-text', // Disables loading text
    },
    defaults: {
        loadingText: 'Loading...',      // Default loading text
        showSpinner: false,             // Default spinner visibility
    },
    spinner: {
        html: '<span class="spinner-border mx-2 spinner-border-sm" role="status" aria-hidden="true"></span>'
    }
}


/**
 * Toast Configuration
 * Defines settings for toast notifications.
 */
export const toast = {
    options: {
        position: 'topRight',
        timeout: 5000,
        progressBar: true,
        progressBarColor: '#1b5e38',
        closeOnClick: true,
        pauseOnHover: true,
        resetOnHover: false,
        transitionIn: 'fadeInLeft',
        transitionOut: 'fadeOutRight',
    }
}


/**
 * Validation Configuration
 * Defines settings for validation feedback.
 */
export const validation = {
    attributes: {
        feedback: 'feedback',               // Enables validation feedback
        inputFeedbackId: 'feedback-id',     // Links input to its feedback element
    }
}


/**
 * Event Handling Configuration
 * Defines attributes for custom event handlers.
 */
export const events = {
    attributes: {
        success: 'on-success',              // Success event handler (data, form, button)
        error: 'on-error',                  // Error event handler (data, form, button)
        loading: 'on-loading',              // Loading state event handler (isLoading, form, button)
        loaded: 'on-loaded',                // Loaded event handler (tabId, tabContent)
        log: 'log',                        // Logs the response
    }
}


/**
 * Rate Limiter Configuration
 * Defines attributes for rate limiting and submission tracking.
 */
export const rateLimiter = {
    attributes: {
        rateLimit: 'rate-limit',        // Specifies rate limiting
        lastSubmission: 'last-submission', // Tracks last submission time
    }
}

/**
 * Component Identifiers
 * Defines unique identifiers for various components in the application.
 */
export const identifiers = {
    singleFormPost: 'single-form-post-handler', // Single form post handler
    counterHandler: 'counter-handler',          // Counter handler
    tabHandler: 'tab-handler',                  // Tab handler
    modalHandler: 'modal-handler',              // Modal handler
};



/**
 * @constant {Object} __SWEET_ALERT_CFG__ - SweetAlert configuration settings.
 * @property {boolean} TIMER_PROGRESS_BAR - Default show progress bar for the timer.
 * @property {number} TIMER - Default timer for the SweetAlert.
 */
export const sweetAlertConfig = {
    TIMER_PROGRESS_BAR: false,                           // Default show progress bar for the timer
    TIMER: 5000,                                        // Default timer for the SweetAlert
}

export const httpRequestConfig = {
    CSRF_TOKEN_SELECTOR: 'meta[name="csrf-token"]',       // Selector for the CSRF token meta tag
    CSRF_TOKEN_HEADER: 'X-CSRF-TOKEN',                    // Header for the CSRF token
    CSRF_TOKEN_ERROR: 'CSRF token not found',             // Error message for the CSRF token not found
    REQUEST_TIMEOUT: 20000,                               // Default timeout (in milliseconds) for HTTP requests
}

/**
 * @constant {Object} __DATA_TABLE_CFG__ - DataTable configuration settings.
 * @property {number} SEARCH_DELAY - Delay (in milliseconds) for the search input.
 * @property {boolean} PROCESSING - Enable or disable the processing indicator.
 * @property {boolean} SERVER_SIDE - Enable or disable server-side processing.
 * @property {Array<number>} LENGTH_MENU - Array of page length options.
 * @property {Array<string|number>} LENGTH_MENU_TEXT - Array of text for page length options.
 * @property {boolean} STATE_SAVE - Enable or disable state saving.
 * @property {boolean} ENABLE_SEARCH - Enable or disable the search functionality.
 * @property {boolean} ENABLE_FILTER - Enable or disable the filter functionality.
 * @property {boolean} ENABLE_RESET_FILTER - Enable or disable the reset filter functionality.
 * @property {boolean} ENABLE_COLUMN_VISIBILITY - Enable or disable column visibility control.
 * @property {boolean} ENABLE_TOGGLE_TOOLBAR - Enable or disable the toggle toolbar.
 * @property {boolean} ORDERABLE - Enable or disable ordering of columns.
 * @property {Object} ACTION_BUTTONS - Configuration for action buttons.
 * @property {boolean} ACTION_BUTTONS.edit - Enable or disable the edit button.
 * @property {boolean} ACTION_BUTTONS.delete - Enable or disable the delete button.
 * @property {boolean} ACTION_BUTTONS.view - Enable or disable the view button.
 */
export const dataTableConfig = {
    SEARCH_DELAY: 1500,                                  // Delay for the search input
    PROCESSING: true,                                    // Enable processing indicator
    SERVER_SIDE: true,                                   // Enable server-side processing
    LENGTH_MENU: [10, 20, 30, 40, 50, -1],               // Page length options
    LENGTH_MENU_TEXT: [10, 20, 30, 40, 50, 'All'],       // Text for page length options
    STATE_SAVE: false,                                   // Disable state saving

    ENABLE_SEARCH: true,                                 // Enable search functionality
    ENABLE_FILTER: true,                                 // Enable filter functionality
    ENABLE_RESET_FILTER: true,                           // Enable reset filter functionality
    ENABLE_COLUMN_VISIBILITY: true,                      // Enable column visibility control
    ENABLE_TOGGLE_TOOLBAR: true,                         // Enable toggle toolbar
    ORDERABLE: true,                                    // Disable ordering of columns
    ACTION_BUTTONS: {
        edit: true,                                      // Enable edit button
        delete: true,                                    // Enable delete button
        view: true,                                      // Enable view button
    }
}


/**
 * @constant {Object} validationHandlerConfig - Validation handler configuration settings.
 */
export const validationHandlerConfig = {
    IDENTIFIER: 'validation-handler',

    ATTRIBUTES: {
        form: {
            validator: 'v'
        },

        input: {
            // Validation attributes
            required: 'd-required',
            minLength: 'd-minLength',
            maxLength: 'd-maxLength',
            email: 'd-email',
            numeric: 'd-numeric',
            min: 'd-min',
            max: 'd-max',
            pattern: 'd-pattern',
            match: 'd-match',
            url: 'd-url',
            tel: 'd-tel',
            date: 'd-date',
            dateMin: 'd-dateMin',
            dateMax: 'd-dateMax',
            dateRelatesTo: 'd-dateRelatesTo',
            dateRelationType: 'd-dateRelationType',
            ip: 'd-ip',
            color: 'd-color',

            // File validation attributes (removed image dimensions)
            fileSize: 'd-fileSize',           // in MB
            fileTypes: 'd-fileTypes',         // comma-separated extensions
            maxFiles: 'd-maxFiles',           // maximum number of files
            totalSize: 'd-totalSize',         // total size in MB

            // New text validation attributes
            unicode: 'd-unicode',
            profanity: 'd-profanity',
            richText: 'd-richText',
            json: 'd-json',
            xml: 'd-xml',
            maxWords: 'd-maxWords',
            noConsecutive: 'd-noConsecutive',
            domain: 'd-domain',
            base64: 'd-base64',

            // Only keep the basic validations
            decimal: 'd-decimal',  // Decimal places control
            scientific: 'd-scientific',        // Scientific notation
            percentage: 'd-percentage',        // Percentage values
            roman: 'd-roman',                 // Roman numerals

            // New date validation attributes
            dateRange: 'd-dateRange',
            dateRangeType: 'd-dateRangeType',
            dateAge: 'd-dateAge',
            dateDuration: 'd-dateDuration',
            dateDurationType: 'd-dateDurationType',
            dateRelative: 'd-dateRelative',
        },

        // UI related attributes
        ui: {
            feedbackId: 'feedback-id',
            labelName: 'l-name',
        },

        // Control attributes
        control: {
            controls: 'controls',
            controlledBy: 'controlled-by',
            controlType: 'control-type',
            controlCondition: 'd-controlCondition', // New: empty, not-empty, equals, not-equals
            controlValue: 'd-controlValue'         // New: value to compare against
        },

        errorMessages: {
            required: 'err-required',
            minLength: 'err-minLength',
            maxLength: 'err-maxLength',
            email: 'err-email',
            numeric: 'err-numeric',
            min: 'err-min',
            max: 'err-max',
            pattern: 'err-pattern',
            match: 'err-match',
            url: 'err-url',
            tel: 'err-tel',
            date: 'err-date',
            dateMin: 'err-dateMin',
            dateMax: 'err-dateMax',

            ip: 'err-ip',
            color: 'err-color',
            fileSize: 'err-fileSize',
            fileTypes: 'err-fileTypes',
            maxFiles: 'err-maxFiles',
            totalSize: 'err-totalSize',
            unicode: 'err-unicode',
            profanity: 'err-profanity',
            richText: 'err-richText',
            json: 'err-json',
            xml: 'err-xml',
            maxWords: 'err-maxWords',
            noConsecutive: 'err-noConsecutive',
            domain: 'err-domain',
            base64: 'err-base64',
            decimal: 'err-decimal',
            scientific: 'err-scientific',
            currency: 'err-currency',
            percentage: 'err-percentage',
            roman: 'err-roman',
            dateRange: 'err-dateRange',
            dateAge: 'err-dateAge',
            dateDuration: 'err-dateDuration',
            dateRelative: 'err-dateRelative',
        }
    },

    PATTERNS: {
        email: /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/,
        url: /^(https?:\/\/)?([\da-z.-]+)\.([a-z.]{2,6})([/\w .-]*)*\/?$/,
        tel: /^[+]?[(]?[0-9]{3}[)]?[-\s.]?[0-9]{3}[-\s.]?[0-9]{4,6}$/,
        numeric: /^[0-9]+$/,
        ip: /^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/,
        color: /^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/,
        date: /^\d{4}-\d{2}-\d{2}$/,
        unicode: /^[\p{L}\p{N}\p{P}\p{Z}]+$/u,
        domain: /^(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,}$/,
        base64: /^(?:[A-Za-z0-9+/]{4})*(?:[A-Za-z0-9+/]{2}==|[A-Za-z0-9+/]{3}=)?$/,
        consecutiveChars: /(.)\1{2,}/,
        consecutiveSpaces: /\s{2,}/,
        htmlTags: /<[^>]*>/g,
        scientific: /^[+-]?\d*\.?\d+e[+-]?\d+$/i,
        roman: /^M{0,4}(CM|CD|D?C{0,3})(XC|XL|L?X{0,3})(IX|IV|V?I{0,3})$/i,
        percentage: /^(100|[1-9][0-9]?|0)$/,
        decimal: (places) => new RegExp(`^-?\\d*\\.\\d{${places}}$`),
    },

    MESSAGES: {
        required: (fieldName) => `${fieldName} is required`,
        minLength: (fieldName, min) => `${fieldName} must be at least ${min} characters`,
        maxLength: (fieldName, max) => `${fieldName} must not exceed ${max} characters`,
        email: (fieldName) => `${fieldName} must be a valid email address`,
        numeric: (fieldName) => `${fieldName} must contain only numbers`,
        min: (fieldName, min) => `${fieldName} must be at least ${min}`,
        max: (fieldName, max) => `${fieldName} must not exceed ${max}`,
        pattern: (fieldName) => `${fieldName} format is invalid`,
        url: (fieldName) => `${fieldName} must be a valid URL`,
        tel: (fieldName) => `${fieldName} must be a valid phone number`,
        match: (fieldName, matchFieldId) => {
            const matchField = document.getElementById(matchFieldId);
            const matchFieldName = matchField?.getAttribute('l-name') || matchFieldId;
            return `${fieldName} must match ${matchFieldName}`;
        },
        date: (fieldName) => `${fieldName} must be a valid date`,
        dateMin: (fieldName, min) => `${fieldName} cannot be before ${min}`,
        dateMax: (fieldName, max) => `${fieldName} cannot be after ${max}`,
        dateBefore: (fieldName, relatedField) => `${fieldName} must be before ${relatedField}`,
        dateAfter: (fieldName, relatedField) => `${fieldName} must be after ${relatedField}`,
        ip: (fieldName) => `${fieldName} must be a valid IP address`,
        color: (fieldName) => `${fieldName} must be a valid color code`,
        fileSize: (fieldName, size) => `${fieldName} must not exceed ${size}MB`,
        fileTypes: (fieldName, types) => `${fieldName} must be one of these types: ${types}`,
        maxFiles: (fieldName, max) => `You can only upload up to ${max} files`,
        totalSize: (fieldName, size) => `Total upload size must not exceed ${size}MB`,
        unicode: (fieldName) => `${fieldName} contains invalid Unicode characters`,
        profanity: (fieldName) => `${fieldName} contains restricted words`,
        richText: (fieldName) => `${fieldName} contains invalid HTML tags`,
        json: (fieldName) => `${fieldName} must be valid JSON format`,
        xml: (fieldName) => `${fieldName} must be valid XML format`,
        maxWords: (fieldName, max) => `${fieldName} must not exceed ${max} words`,
        noConsecutive: (fieldName) => `${fieldName} contains consecutive repeated characters`,
        domain: (fieldName) => `${fieldName} must be a valid domain name`,
        base64: (fieldName) => `${fieldName} must be a valid Base64 string`,
        decimal: (fieldName, min) => `${fieldName} must have exactly ${min} decimal places`,
        scientific: (fieldName) =>
            `${fieldName} must be in scientific notation (e.g., 1.23e-4)`,
        percentage: (fieldName) =>
            `${fieldName} must be a number between 0 and 100`,
        roman: (fieldName) =>
            `${fieldName} must be a valid Roman numeral`,
        dateRange: (fieldName, range, type) => {
            const direction = type === 'future' ? 'next' : 'last';
            return `${fieldName} must be within the ${direction} ${range} days`;
        },
        dateAge: (fieldName, age) =>
            `${fieldName} indicates an age less than ${age} years`,
        dateDuration: (fieldName, duration, type) =>
            `${fieldName} duration must be within ${duration} ${type}`,
        dateRelative: (fieldName, period) =>
            `${fieldName} must be within ${period.replace('-', ' ')}`,
    },

    CLASSES: {
        invalid: 'is-invalid',
        validationMessage: 'invalid-feedback',
        visible: 'show-feedback',
        hidden: 'd-none',
        fadeIn: 'fade-in',
        fadeOut: 'fade-out'
    },

    // Add file validation constants
    FILE_VALIDATION: {
        DEFAULT_MAX_SIZE: 5, // 5MB
        DEFAULT_TOTAL_SIZE: 20, // 20MB
        DEFAULT_MAX_FILES: 5,
        ALLOWED_IMAGE_TYPES: ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
        ALLOWED_DOCUMENT_TYPES: [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ]
    },

    TEXT_VALIDATION: {

        PROFANITY: {
            WORD_LIST: [
                'test',
            ],

            LEETSPEAK_MAP: {
                'a': ['4', '@', 'α', 'а'],
                'b': ['8', '6', 'β', 'б'],
                'e': ['3', '€', 'ε', 'е'],
                'i': ['1', '!', 'ι', 'і'],
                'l': ['1', '|', 'і', 'л'],
                'o': ['0', 'ο', 'о'],
                's': ['5', '$', 'ѕ'],
                't': ['7', '+', 'τ'],
                'x': ['×', 'х'],
                'y': ['¥', 'у']
            },

            SEPARATORS: [
                ' ', '.', ',', '-', '_', '*',
                '|', '/', '\\', '+', '=',
                '(', ')', '[', ']', '{', '}',
                '!', '?', '"', "'", ';', ':'
            ],
            MIN_SIMILARITY: 0.8
        },
        ALLOWED_HTML_TAGS: ['<p>', '<br>', '<strong>', '<em>', '<ul>', '<li>', '<ol>'],
        MAX_CONSECUTIVE_CHARS: 3
    },

    // New numeric validation configuration
    NUMBER_VALIDATION: {
        MAX_SAFE_DECIMAL_PLACES: 20
    },

    // Add date validation constants
    DATE_VALIDATION: {
        RELATIVE_PERIODS: ['next-week', 'next-month', 'next-quarter', 'next-year'],
        DURATION_TYPES: ['days', 'weeks', 'months'],
        MIN_AGE: 18,
        DEFAULT_RANGE: 30
    }
};

/**
 * Updates the configuration object with new settings.
 * @param {Object} newConfig - The new configuration settings.
 * @example
 * // Update the configuration to change the debounce delay and throttle limit
 * updateConfig({ debounceDelay: 300, throttleLimit: 600 });
 */
export function updateConfig(newConfig) {
    Object.assign(config, newConfig);
}
