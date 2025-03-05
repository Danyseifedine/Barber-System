export class ModalEventHandler {
    static async handleSuccess(content, modal, url, response) {
        // Default success handling logic can be added here
        // console.log('Success:', { content, modal, url, response });
    }

    static async handleError(error, modal, url) {
        // Default error handling logic can be added here
        // console.error('Error:', { error, modal, url });
    }

    static async handleLoading(isLoading, modal, url) {
        // Default loading handling logic can be added here
        // console.log('Loading:', { isLoading, modal, url });
    }

    static async handleLoaded(content, modal, url, response) {
        // Default loaded handling logic can be added here
        // console.log('Loaded:', { content, modal, url, response });
    }

    static async executeCustomHandler(handlerName, ...args) {
        if (typeof window[handlerName] === 'function') {
            await window[handlerName](...args);
        }
    }
}
