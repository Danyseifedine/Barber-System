export class TabLoadingManager {
    constructor(config) {
        this.config = config;
    }

    setLoading(group, tabId, isLoading) {
        const tabElement = group.tabList.querySelector(`[${this.config.attributes.tabId}="${tabId}"]`);
        if (isLoading) {
            tabElement.classList.add(this.config.classes.loading);
        } else {
            tabElement.classList.remove(this.config.classes.loading);
        }

        if (group.eventHandlers.onLoading && typeof window[group.eventHandlers.onLoading] === 'function') {
            window[group.eventHandlers.onLoading](isLoading, tabId, group.tabContent);
        } else {
            this.defaultLoading(isLoading, group.tabContent);
        }
    }

    defaultLoading(isLoading, tabContent) {
        if (isLoading) {
            tabContent.innerHTML = '<div class="spinner">Loading...</div>';
        }
    }
}
