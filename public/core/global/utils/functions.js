/**
 * Dispatches a custom event on a form element
 * @param {HTMLFormElement} form - The form element to dispatch the event on
 * @param {string} eventName - Name of the custom event
 * @param {*} detail - Data to be passed with the event
 */
export function dispatchFormEvent(form, eventName, detail) {
    form.dispatchEvent(new CustomEvent(eventName, { detail }));
}

/**
 * Serializes form data into different formats
 * @param {HTMLFormElement} form - The form to serialize
 * @param {'formdata'|'json'|'urlencoded'} type - Output format type
 * @returns {FormData|Object|string} Serialized form data in requested format
 */
export function serializeFormData(form, type = 'formdata') {
    switch (type) {
        case 'json':
            return Object.fromEntries(new FormData(form));
        case 'urlencoded':
            return new URLSearchParams(new FormData(form)).toString();
        case 'formdata':
        default:
            return new FormData(form);
    }
}

/**
 * Copies text to clipboard and shows a toast notification
 * @param {string} text - Text to copy to clipboard
 * @returns {Promise<void>}
 */
export async function copyTextToClipboard(text, isToasted = true) {
    try {
        await navigator.clipboard.writeText(text);
        if (isToasted) Toast.showSuccessToast('', 'The text has been copied to your clipboard.');
    } catch (err) {
        if (isToasted) Toast.showErrorToast('', 'Failed to copy text: ' + err);
    }
}

/**
 * Gets a query parameter value from the current URL
 * @param {string} name - Name of the query parameter
 * @returns {string|null} Value of the query parameter or null if not found
 */
export function getUrlQueryParameter(name) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(name) || null;
}

/**
 * Clears all inputs in a form and removes validation states
 * @param {string} formSelector - CSS selector for the form
 * @throws {Error} If no form is found with the given selector
 */
export function resetFormInputs(formSelector) {
    const formElement = document.querySelector(formSelector);
    if (!formElement) {
        throw new Error(`No form found with the selector: ${formSelector}`);
    }
    formElement.querySelectorAll('input, textarea').forEach(el => {
        el.value = '';
        el.classList.remove('is-invalid');
    });
    formElement.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
}

/**
 * Toggles button disabled state based on whether form has any filled inputs
 * @param {HTMLFormElement} form - The form to monitor
 * @param {HTMLButtonElement} button - The button to toggle
 */
export function toggleSubmitButtonOnFormInput(form, button) {
    if (!form || !button) {
        console.error('toggleSubmitButtonOnFormInput: form or button is not defined');
        return;
    }

    const formElements = form.querySelectorAll('input, textarea, select');
    let hasValue = false;

    formElements.forEach(element => {
        element.addEventListener('input', () => {
            hasValue = false;

            formElements.forEach(el => {
                if (el.type === 'checkbox') {
                    if (el.checked) {
                        hasValue = true;
                    }
                } else {
                    if (el.value.trim() !== '') {
                        hasValue = true;
                    }
                }
            });

            button.classList.toggle('disabled', !hasValue);
        });
    });

    formElements.forEach(el => {
        if (el.type === 'checkbox') {
            if (el.checked) {
                hasValue = true;
            }
        } else {
            if (el.value.trim() !== '') {
                hasValue = true;
            }
        }
    });
    button.classList.toggle('disabled', !hasValue);
}

/**
 *Checks if the current URL path ends with the given path
* @param {string} path - The path to check
* @returns {boolean} True if the URL path ends with the path, false otherwise
*/
export function checkUrlEnds(path) {
    return window.location.pathname.endsWith(path);
}

/**
* Checks if the current URL path contains the given path
* @param {string} path - The path to check
* @returns {boolean} True if the URL path contains the path, false otherwise
*/
export function checkUrlContains(path) {
    return window.location.pathname.includes(path);
}

/**
* Navigates to the given URL without page reload using History API
* @param {string} url - The URL to navigate to
*/
export function goToUrl(url) {
    window.history.pushState({}, '', url);
}

/**
* Navigates to the given URL with page reload
* @param {string} url - The URL to navigate to
*/
export function goToUrlReload(url) {
    window.location.href = url;
}

/**
* Finds URL part containing the specified text
* @param {string} text - The text to search for in URL
* @returns {string|undefined} The matching part, or undefined if not found
*/
export function findInUrl(text) {
    const urlParts = window.location.pathname.split('/');
    return urlParts.find(part => part.includes(text));
}

/**
* Gets the URL part that follows after the specified text
* @param {string} text - The text to search for in URL
* @returns {string|undefined} The next part, or undefined if not found
*/
export function findNextInUrl(text) {
    const urlParts = window.location.pathname.split('/');
    const index = urlParts.findIndex(part => part.includes(text));
    if (index !== -1 && index < urlParts.length - 1) {
        return urlParts[index + 1];
    }
    return undefined;
}

/**
* Gets the URL part that comes before the specified text
* @param {string} text - The text to search for in URL
* @returns {string|undefined} The previous part, or undefined if not found
*/
export function findPrevInUrl(text) {
    const urlParts = window.location.pathname.split('/');
    const index = urlParts.findIndex(part => part.includes(text));
    if (index !== -1 && index > 0) {
        return urlParts[index - 1];
    }
    return undefined;
}

/**
* Gets all query parameters from the current URL
* @returns {Object} Object containing all query parameters
*/
export function getUrlParams() {
    const params = {};
    const searchParams = new URLSearchParams(window.location.search);
    for (const [key, value] of searchParams) {
        params[key] = value;
    }
    return params;
}

/**
* Updates URL query parameters without page reload
* @param {Object} params - Object containing parameters to update
*/
export function setUrlParams(params) {
    const url = new URL(window.location.href);
    Object.entries(params).forEach(([key, value]) => {
        if (value === null || value === undefined) {
            url.searchParams.delete(key);
        } else {
            url.searchParams.set(key, value);
        }
    });
    window.history.pushState({}, '', url);
}

/**
* Gets the current URL path parts
* @returns {string[]} Array of non-empty URL parts
*/
export function getUrlParts() {
    return window.location.pathname.split('/').filter(part => part);
}

/**
* Checks if current URL matches a pattern
* @param {string|RegExp} pattern - URL pattern to match against
* @returns {boolean} True if URL matches pattern
*/
export function checkUrlMatches(pattern) {
    if (pattern instanceof RegExp) {
        return pattern.test(window.location.href);
    }
    return window.location.href.includes(pattern);

}

/**
 * Initializes Select2 on a given selector with a dropdown parent
 * @param {string} selector - The selector for the element to initialize Select2 on
 * @param {string} watchElement - The selector for the element to watch for changes
 */
export function initSelect2(selector, watchElement) {
    $(selector).select2({
        dropdownParent: $(watchElement),
    });
}
