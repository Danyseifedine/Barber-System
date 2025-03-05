import { Identifiable } from './base/identifiable.js';
import { errorHandler } from '../utils/classes/error-utils.js';
import { handlers, identifiers, events } from '../config/app-config.js';
import { env } from '../config/app-config.js';
import { TabEventHandler } from '../events/tabEventHandler.js';
import { TabLoadingManager } from '../ui/tabLoadingManager.js';
import { Documentation } from '../doc/documentation.js';

export class TabHandler extends Identifiable {
    constructor(identifier = identifiers.tabHandler) {
        super(identifier);
        this.tabGroups = new Map();
        this.config = handlers.tabHandler;
        this.loadingManager = new TabLoadingManager(this.config);
        this.init();
    }

    init() {
        const tabLists = document.querySelectorAll(this.config.selectors.tabGroup);
        if (!tabLists.length) {
            errorHandler.logError('No tab lists found');
            return;
        }
        tabLists.forEach(this.initTabGroup.bind(this));
    }

    initTabGroup(tabList) {
        const tabGroupId = tabList.getAttribute(this.config.attributes.tabGroupId);
        if (!tabGroupId) {
            errorHandler.logMissingAttributeError(
                'TabHandler',
                this.config.attributes.tabGroupId,
                'ul',
                'as an attribute on the tab list element',
                'Uniquely identifies the tab group'
            );
            return;
        }

        if (this.tabGroups.has(tabGroupId)) {
            errorHandler.logError(`Duplicate tab group ID found: ${tabGroupId}. Each tab group must have a unique ID.`);
            return;
        }

        const tabContent = document.querySelector(`[${this.config.attributes.contentId}="${tabGroupId}"]`);
        if (!tabContent) {
            errorHandler.logMissingAttributeError(
                'TabHandler',
                this.config.attributes.contentId,
                'div',
                'as an attribute on the tab content container',
                'Links the content area to the tab group'
            );
            return;
        }

        const route = tabList.getAttribute(this.config.attributes.route);
        if (!route) {
            errorHandler.logMissingAttributeError(
                'TabHandler',
                this.config.attributes.route,
                'ul',
                'as an attribute on the tab list element',
                'Specifies the server endpoint for tab content'
            );
            return;
        }

        const eventHandlers = {
            onLoading: tabList.getAttribute(events.attributes.loading),
            onSuccess: tabList.getAttribute(events.attributes.success),
            onError: tabList.getAttribute(events.attributes.error),
            onLoaded: tabList.getAttribute(events.attributes.loaded)
        };

        const updateUrl = tabList.getAttribute(this.config.attributes.tabUrl) === 'true';

        this.tabGroups.set(tabGroupId, {
            tabList,
            tabContent,
            route,
            tabs: new Map(),
            activeTab: null,
            eventHandlers,
            updateUrl
        });

        tabList.addEventListener('click', this.handleTabClick.bind(this, tabGroupId));
        this.loadInitialTab(tabGroupId);
    }

    handleTabClick(tabGroupId, event) {
        const tabElement = event.target.closest(this.config.selectors.tab);
        if (!tabElement) return;

        const tabId = tabElement.getAttribute(this.config.attributes.tabId);
        this.activateTab(tabGroupId, tabId);
    }

    async activateTab(tabGroupId, tabId) {
        const group = this.tabGroups.get(tabGroupId);
        if (!group || group.activeTab === tabId) return;

        this.loadingManager.setLoading(group, tabId, true);

        try {
            await this.loadTabContent(tabGroupId, tabId);
            this.updateActiveTab(group, tabId);
            if (group.updateUrl) {
                this.updateUrl(tabGroupId, tabId);
            }
        } finally {
            this.loadingManager.setLoading(group, tabId, false);
            await TabEventHandler.handleLoaded(tabId, group.tabContent, group.eventHandlers.onLoaded);
        }
    }

    updateActiveTab(group, tabId) {
        group.tabList.querySelectorAll(this.config.selectors.tab)
            .forEach(tab => tab.classList.remove(this.config.classes.active));

        const activeTabElement = group.tabList.querySelector(`[${this.config.attributes.tabId}="${tabId}"]`);
        if (activeTabElement) {
            activeTabElement.classList.add(this.config.classes.active);
        }

        group.activeTab = tabId;
    }

    updateUrl(tabGroupId, tabId) {
        const url = new URL(window.location);
        url.searchParams.set(tabGroupId, tabId);
        window.history.pushState({}, '', url);
    }

    async loadTabContent(tabGroupId, tabId) {
        const group = this.tabGroups.get(tabGroupId);
        if (!group) return;

        const tabElement = group.tabList.querySelector(`[${this.config.attributes.tabId}="${tabId}"]`);
        const shouldCache = tabElement.getAttribute(this.config.attributes.cache) !== 'false';
        const log = tabElement.getAttribute(events.attributes.log);

        if (shouldCache && group.tabs.has(tabId)) {
            group.tabContent.innerHTML = group.tabs.get(tabId).content;
            return;
        }

        try {
            const response = await axios.get(group.route, { params: { tabId } });
            const content = response.data.html;

            if (log) console.log(response.data);

            if (shouldCache) {
                group.tabs.set(tabId, { content });
            }
            group.tabContent.innerHTML = content;

            await TabEventHandler.handleSuccess(content, tabId, group.tabContent, group.eventHandlers.onSuccess);
        } catch (error) {
            errorHandler.logError(`Error loading content for tab ID ${tabId} in group ${tabGroupId}:`, error);
            group.tabContent.innerHTML = '<p>Error loading content. Please try again.</p>';

            await TabEventHandler.handleError(error, tabId, group.tabContent, group.eventHandlers.onError);
        }
    }

    loadInitialTab(tabGroupId) {
        const group = this.tabGroups.get(tabGroupId);
        if (!group) return;

        const url = new URL(window.location);
        const tabIdFromUrl = url.searchParams.get(tabGroupId);

        if (tabIdFromUrl && group.updateUrl) {
            const tabElement = group.tabList.querySelector(`[${this.config.attributes.tabId}="${tabIdFromUrl}"]`);
            if (tabElement) {
                this.activateTab(tabGroupId, tabIdFromUrl);
                return;
            }
        }

        const initialTab = group.tabList.querySelector(this.config.selectors.initialTab);
        if (initialTab) {
            const tabId = initialTab.getAttribute(this.config.attributes.tabId);
            this.activateTab(tabGroupId, tabId);
        }
    }

    static documentation() {
        return Documentation.generate(
            'TabHandler',
            'The TabHandler class provides a powerful and flexible system for managing tab-based content with dynamic loading, caching, and custom event handling.',
            `// Initialize a TabHandler
const tabHandler = new TabHandler('tab-handler');

// The TabHandler is now active and will manage all tab groups with the specified identifier`,
            `<ul identifier="tab-handler"
    tab-group-id="unique-group-id"
    tab-route="/tab-content-endpoint"
    tab-list
    tab-url="true"
    on-loading="handleTabLoading"
    on-success="handleTabSuccess"
    on-error="handleTabError"
    on-loaded="handleTabLoaded">
    <li tab-id="tab1" tab-initial="true">Tab 1</li>
    <li tab-id="tab2">Tab 2</li>
    <li tab-id="tab3">Tab 3</li>
</ul>
<div tab-content-id="unique-group-id"></div>`,
            [
                { name: 'identifier', description: 'Identifies the tab handler instance', required: true },
                { name: 'tab-group-id', description: 'Unique identifier for the tab group', required: true },
                { name: 'tab-route', description: 'Server endpoint for tab content', required: true },
                { name: 'tab-list', description: 'Indicates this is a tab list', required: true },
                { name: 'tab-id', description: 'Unique identifier for each tab', required: true },
                { name: 'tab-initial', description: 'Indicates the initially active tab', required: false, default: 'false' },
                { name: 'tab-content-id', description: 'Links content area to tab group', required: true },
                { name: 'tab-url', description: 'Whether to update URL with tab ID', required: false, default: 'false' },
                { name: 'cache-tab', description: 'Whether to cache the tab content', required: false, default: 'true' },
                { name: 'on-loading', description: 'Name of loading event handler function', required: false },
                { name: 'on-success', description: 'Name of success event handler function', required: false },
                { name: 'on-error', description: 'Name of error event handler function', required: false },
                { name: 'on-loaded', description: 'Name of loaded event handler function', required: false },
                { name: 'log', description: 'Logs the response', required: false }
            ], [
            'Automatically handles tab switching and content loading for multiple tab groups.',
            'Performs AJAX content loading for tabs with caching support.',
            'Manages loading states during content fetching.',
            'Allows custom event handlers for loading, success, error, and loaded states.',
            'Uses event delegation for efficient event handling.',
            'Provides detailed error logging for missing or invalid attributes.',
            'Ensures each tab group has a unique ID and logs an error if duplicates are found.',
            'Can update the URL with the current tab ID for better navigation and bookmarking.',
            'Supports responsive design and can be easily styled with CSS.',
            'Extends the Identifiable class for consistent component identification.'
        ],
            `// Custom event handlers example
window.handleTabLoading = function(isLoading, tabId, tabContent) {
    console.log('Tab loading state:', isLoading, 'for tab:', tabId);
    // Add your custom loading logic here
};

window.handleTabSuccess = function(content, tabId, tabContent) {
    console.log('Tab content loaded successfully for tab:', tabId);
    // Process the loaded content if needed
};

window.handleTabError = function(error, tabId, tabContent) {
    console.error('Error loading tab content for tab:', tabId, error);
    // Handle the error, maybe display a user-friendly message
};

window.handleTabLoaded = function(tabId, tabContent) {
    console.log('Tab fully loaded:', tabId);
    // Perform any post-loading operations
};
// Usage:
// 1. Define these functions in your global scope
// 2. Set the 'on-loading', 'on-success', 'on-error', and 'on-loaded' attributes in your HTML to the function names
// 3. The TabHandler will automatically call these functions during the tab lifecycle`
        );
    }
}

env.isDevelopment && (window.TabHandler = TabHandler);
