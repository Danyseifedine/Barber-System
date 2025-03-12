export class TabEventHandler {
    static async handleSuccess(content, tabId, tabContent, onSuccess) {
        if (onSuccess && typeof window[onSuccess] === 'function') {
            window[onSuccess](content, tabId, tabContent);
        }
    }

    static async handleError(error, tabId, tabContent, onError) {
        if (onError && typeof window[onError] === 'function') {
            window[onError](error, tabId, tabContent);
        }
    }

    static async handleLoaded(tabId, tabContent, onLoaded) {
        if (onLoaded && typeof window[onLoaded] === 'function') {
            window[onLoaded](tabId, tabContent);
        }
    }
}
