import { button as buttonConfig } from '../config/app-config.js';
import { errorHandler } from '../utils/classes/error-utils.js';

/**
 * Manages the state and appearance of buttons, particularly during loading states.
 */
export class ButtonManager {
    /**
     * Sets the loading state of a button.
     * @param {HTMLButtonElement} button - The button element to modify.
     * @param {boolean} isLoading - Whether the button should be in a loading state.
     */
    setLoading(button, isLoading) {
        if (!button) {
            errorHandler.logMissingAttributeError(
                'ButtonManager',
                'button element',
                'DOM',
                'in the DOM',
                'The button element is required to manage its loading state'
            );
            return;
        }

        const config = buttonConfig;
        const noLoadingText = button.hasAttribute(config.attributes.noLoadingText);
        const loadingText = noLoadingText ? '' : (button.getAttribute(config.attributes.loadingText) || config.defaults.loadingText);
        const originalHTML = button.getAttribute('original-html') || button.innerHTML;
        const showSpinner = button.getAttribute(config.attributes.spinner) !== config.defaults.showSpinner.toString();
        const spinnerHTML = config.spinner.html;

        if (isLoading) {
            button.setAttribute('original-html', originalHTML);
            if (noLoadingText) {
                button.innerHTML = showSpinner ? spinnerHTML : '';
            } else {
                button.innerHTML = showSpinner ? `${spinnerHTML}${loadingText}` : loadingText;
            }
            button.disabled = true;
        } else {
            button.innerHTML = originalHTML;
            button.disabled = false;
        }
    }
}
