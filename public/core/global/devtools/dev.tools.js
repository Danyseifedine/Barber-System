import { UTILITY_FUNCTIONS, CLASSIC_CLASSES } from './utilityMap.js'
export class DevTools {
    static componentMap;
    static initializedComponents;
    static devToolsContainer;
    static init(componentMap, initializedComponents) {
        this.componentMap = componentMap;
        this.initializedComponents = initializedComponents;
        this.createDevToolsUI();
        this.setupDevTools();
    }
    static createDevToolsUI() {
        // Create floating action button (FAB)
        const fabHTML = `
            <button id="devToolsFAB" class="dev-tools-fab">
                <svg class="dev-tools-fab-icon" viewBox="0 0 24 24" width="24" height="24">
                    <path d="M9.17 6.17a3.001 3.001 0 0 1 5.66 0M7 12a5 5 0 0 1 10 0M4.83 17.83a9.001 9.001 0 0 1 14.34 0M12 12v.01"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
        `;

        // Create modal
        const modalHTML = `
            <div id="devToolsModal" class="dev-tools-modal">
                <div class="dev-tools-modal-content">
                    <div class="dev-tools-header">
                        <div class="dev-tools-branding">
                            <div class="dev-tools-logo">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
                                </svg>
                            </div>
                            <h2>DevTools</h2>
                        </div>
                        <button id="devToolsClose" class="dev-tools-close">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M18 6L6 18M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <div class="dev-tools-navigation">
                        <div class="dev-tools-tabs">
                            <button class="dev-tools-tab active" data-tab="components">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M4 7h16M4 12h16M4 17h16"/>
                                </svg>
                                <span>Attribute Components</span>
                            </button>
                            <button class="dev-tools-tab" data-tab="documentation">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                                <span>Attribute Components Documentation</span>
                            </button>
                            <button class="dev-tools-tab" data-tab="datatable">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M3 3h18v18H3V3zm0 6h18M9 3v18"/>
                                </svg>
                                <span>DataTable</span>
                            </button>
                            <button class="dev-tools-tab" data-tab="utils">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                                </svg>
                                <span>Utils</span>
                            </button>
                            <button class="dev-tools-tab" data-tab="classic">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2zm0 18a8 8 0 1 1 8-8 8 8 0 0 1-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z"/>
                                </svg>
                                <span>Classic</span>
                            </button>
                        </div>
                    </div>
                    <div class="dev-tools-content">
                        <div class="dev-tools-tab-content active" id="componentsTab">
                            <div class="components-grid">
                                <div class="components-section">
                                    <h3>Available Components</h3>
                                    <div class="components-list" id="devToolAvailableComponents"></div>
                                </div>
                                <div class="components-section">
                                    <h3>Initialized Components</h3>
                                    <div class="components-list" id="devToolInitializedComponents"></div>
                                </div>
                            </div>
                        </div>
                        <div class="dev-tools-tab-content" id="documentationTab">
                            <div class="documentation-container">
                                <div class="documentation-header">
                                    <div class="documentation-select-container">
                                        <select id="devToolDocSelector" class="documentation-select">
                                            <option value="">Select a component...</option>
                                        </select>
                                    </div>
                                </div>
                                <div id="devToolDocumentation" class="documentation-content">
                                    <div class="documentation-placeholder">
                                        <p>Select a component to view its documentation</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="dev-tools-tab-content" id="utilsTab">
                            <div class="utils-container">
                                <div class="utils-header">
                                <div class="utils-search">
                                        <input type="text" id="utilsSearchInput" placeholder="Search utilities...">
                                        <svg class="search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                        </svg>
                                </div>
                                <div class="category-badges" id="categoryBadges"></div>
                                </div>
                                <div class="utils-content">
                                <div class="utils-grid" id="utilsGrid"></div>
                                <div class="util-detail" id="utilDetail"></div>
                                </div>
                            </div>
                        </div>
                        <div class="dev-tools-tab-content" id="classicTab">
                            <div class="classic-container">
                                <div class="classic-header">
                                    <div class="documentation-select-container">
                                        <select id="classicSelector" class="documentation-select">
                                            <option value="">Select a classic class...</option>
                                            ${CLASSIC_CLASSES.map(classItem => `
                                                <option value="${classItem.name}">${classItem.name}</option>
                                            `).join('')}
                                        </select>
                                    </div>
                                </div>
                                <div id="classicContent" class="classic-content"></div>
                            </div>
                        </div>
                        <div class="dev-tools-tab-content" id="datatableTab">
                            <div class="datatable-container">
                                <div class="datatable-header">
                                    <div class="documentation-select-container">
                                        <select id="datatableSelector" class="documentation-select">
                                            <option value="">Select a section...</option>
                                            <option value="basic">Basic Usage</option>
                                            <option value="columnDefs">Column Definitions</option>
                                            <option value="actions">Action Buttons</option>
                                            <option value="events">Event Handling</option>
                                            <option value="customization">Customization</option>
                                            <option value="htmlStructure">HTML Structure</option>
                                            <option value="bestPractice">Best Practice Implementation</option>
                                        </select>
                                    </div>
                                </div>
                                <div id="datatableContent" class="datatable-content"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Add elements to the page
        const container = document.createElement('div');
        container.innerHTML = fabHTML + modalHTML;
        document.body.appendChild(container);

        this.addStyles();
        this.setupEventListeners();
        this.updateDevToolsContent();
    }
    static addStyles() {
        const styles = document.createElement('style');
        styles.textContent = `
            /* Floating Action Button */
            .dev-tools-fab {
                position: fixed;
                bottom: 24px;
                left: 24px;
                width: 56px;
                height: 56px;
                border-radius: 50%;
                background: #3730a3;
                border: none;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                z-index: 9998;
            }

            .dev-tools-fab:hover {
                transform: scale(1.1);
                background: #312e81;
            }

            .dev-tools-fab-icon {
                color: white;
                transition: transform 0.3s ease;
            }

            .dev-tools-fab:hover .dev-tools-fab-icon {
                transform: rotate(90deg);
            }

            /* Modal Styling */
            .dev-tools-modal {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                backdrop-filter: blur(4px);
                z-index: 9999;
                opacity: 0;
                transition: opacity 0.3s ease;
            }

            .dev-tools-modal.active {
                opacity: 1;
            }

            .dev-tools-modal-content {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%) scale(0.9);
                width: 90%;
                max-width: 1200px;
                height: 90vh;
                background: #ffffff;
                border-radius: 16px;
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
                display: flex;
                flex-direction: column;
                overflow: hidden;
                transition: transform 0.3s ease;
            }

            .dev-tools-modal.active .dev-tools-modal-content {
                transform: translate(-50%, -50%) scale(1);
            }

            .dev-tools-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 20px 24px;
                background: #f8fafc;
                border-bottom: 1px solid #e2e8f0;
            }

            .dev-tools-branding {
                display: flex;
                align-items: center;
                gap: 12px;
            }

            .dev-tools-logo {
                color: #3730a3;
            }

            .dev-tools-header h2 {
                margin: 0;
                font-size: 20px;
                font-weight: 600;
                color: #1e293b;
            }

            .dev-tools-close {
                background: transparent;
                border: none;
                color: #64748b;
                cursor: pointer;
                padding: 8px;
                border-radius: 8px;
                transition: all 0.2s ease;
            }

            .dev-tools-close:hover {
                background: #f1f5f9;
                color: #ef4444;
            }

            .dev-tools-navigation {
                background: #f8fafc;
                border-bottom: 1px solid #e2e8f0;
            }

            .dev-tools-tabs {
                display: flex;
                gap: 8px;
                padding: 16px 24px;
            }

            .dev-tools-tab {
                display: flex;
                align-items: center;
                gap: 8px;
                padding: 8px 16px;
                border: none;
                background: transparent;
                color: #64748b;
                border-radius: 8px;
                cursor: pointer;
                transition: all 0.2s ease;
                font-weight: 500;
            }

            .dev-tools-tab:hover {
                background: #f1f5f9;
                color: #1e293b;
            }

            .dev-tools-tab.active {
                background: #3730a3;
                color: white;
            }

            .dev-tools-tab.active svg {
                stroke: white;
            }

            .dev-tools-content {
                flex: 1;
                overflow: hidden;
                display: flex;
            }

            .dev-tools-tab-content {
                display: none;
                width: 100%;
                height: 100%;
                overflow-y: auto;
                padding: 20px;
            }

            .dev-tools-tab-content.active {
                display: block;
            }

            /* Components Tab Specific */
            .components-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 20px;
                height: 100%;
                overflow-y: auto;
                padding-bottom: 20px;
            }

            .components-section {
                background: #f8fafc;
                border: 1px solid #e2e8f0;
                border-radius: 8px;
                padding: 20px;
            }

            .components-section h3 {
                margin: 0 0 16px 0;
                font-size: 18px;
                font-weight: 600;
                color: #1e293b;
            }

            .components-list {
                display: flex;
                flex-direction: column;
                gap: 8px;
            }

            .component-item {
                display: flex;
                align-items: center;
                gap: 8px;
                padding: 8px 12px;
                background: #ffffff;
                border: 1px solid #e2e8f0;
                border-radius: 4px;
                cursor: pointer;
                transition: all 0.2s ease;
            }

            .component-item:hover {
                background: #f1f5f9;
                border-color: #3730a3;
            }

            .component-item.active {
                background: #3730a3;
                color: white;
            }

            .component-item.active svg {
                stroke: white;
            }

            .component-name {
                font-weight: 500;
                color: #1e293b;
            }

            .component-status {
                font-size: 12px;
                font-weight: 500;
                padding: 2px 6px;
                border-radius: 4px;
                color: #64748b;
                background: #f1f5f9;
            }

            .component-status.available {
                color: #059669;
                background: #ecfdf5;
            }

            .component-status.initialized {
                color: #3730a3;
                background: #f8fafc;
            }

            /* Documentation Tab Specific */
            .documentation-container {
                height: 100%;
                overflow-y: auto;
                padding-bottom: 20px;
            }

            .documentation-header {
                margin-bottom: 24px;
            }

            .documentation-select-container {
                position: relative;
                margin-bottom: 24px;
            }

            .documentation-select {
                width: 100%;
                padding: 12px 16px;
                padding-right: 40px;
                font-size: 14px;
                color: #1e293b;
                background: #f8fafc;
                border: 1px solid #e2e8f0;
                border-radius: 8px;
                appearance: none;
                cursor: pointer;
                transition: all 0.2s ease;
            }

            .documentation-select:hover {
                background: #f1f5f9;
                border-color: #cbd5e1;
            }

            .documentation-select:focus {
                outline: none;
                border-color: #3730a3;
                box-shadow: 0 0 0 3px rgba(55, 48, 163, 0.1);
                background: #ffffff;
            }

            .documentation-select-container::after {
                content: "";
                position: absolute;
                top: 50%;
                right: 16px;
                transform: translateY(-50%);
                width: 10px;
                height: 10px;
                background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
                background-size: contain;
                background-repeat: no-repeat;
                pointer-events: none;
            }

            .documentation-select option {
                padding: 8px;
                font-size: 14px;
            }

            .documentation-content {
                padding: 20px;
                background: #f8fafc;
                border: 1px solid #e2e8f0;
                border-radius: 8px;
            }

            .documentation-placeholder {
                text-align: center;
                color: #64748b;
                font-size: 14px;
                padding: 20px;
            }

            .documentation-tabs {
                display: flex;
                gap: 2px;
                padding: 16px 24px;
                background: #f8fafc;
                border-bottom: 1px solid #e2e8f0;
                overflow-x: auto;
            }

            .documentation-tab {
                padding: 8px 16px;
                border: none;
                background: transparent;
                color: #64748b;
                border-radius: 6px;
                cursor: pointer;
                font-size: 14px;
                font-weight: 500;
                white-space: nowrap;
                transition: all 0.2s ease;
            }

            .documentation-tab:hover {
                color: #3730a3;
                background: #f1f5f9;
            }

            .documentation-tab.active {
                background: #3730a3;
                color: white;
            }

            .documentation-sections {
                padding: 24px;
            }

            .documentation-section {
                display: none;
            }

            .documentation-section.active {
                display: block;
            }

            /* Attributes Table Styling */
            .attributes-table {
                overflow-x: auto;
            }

            .attributes-table table {
                width: 100%;
                border-collapse: collapse;
                font-size: 14px;
            }

            .attributes-table th,
            .attributes-table td {
                padding: 12px;
                text-align: left;
                border-bottom: 1px solid #e2e8f0;
            }

            .attributes-table th {
                background: #f8fafc;
                font-weight: 600;
                color: #1e293b;
            }

            .attributes-table td code {
                background: #f1f5f9;
                padding: 2px 6px;
                border-radius: 4px;
                font-family: monospace;
                font-size: 12px;
                color: #3730a3;
            }

            .attributes-table .badge {
                display: inline-block;
                padding: 2px 8px;
                border-radius: 4px;
                font-size: 12px;
                font-weight: 500;
            }

            .attributes-table .badge.required {
                background: #fee2e2;
                color: #991b1b;
            }

            .attributes-table .badge.optional {
                background: #e0e7ff;
                color: #3730a3;
            }

            /* Features List Styling */
            .features-list {
                display: flex;
                flex-direction: column;
                gap: 12px;
            }

            .feature-item {
                display: flex;
                align-items: flex-start;
                gap: 8px;
                padding: 12px;
                background: #f8fafc;
                border-radius: 8px;
                border: 1px solid #e2e8f0;
            }

            .feature-bullet {
                color: #3730a3;
                font-size: 18px;
                line-height: 1;
            }

            .feature-text {
                color: #475569;
                font-size: 14px;
                line-height: 1.5;
            }

            /* Code Block Styling */
            .documentation-section pre {
                margin: 0;
                background: #1e1e1e;
                border-radius: 8px;
                position: relative;
            }

            .documentation-section pre code {
                display: block;
                padding: 20px;
                color: #d4d4d4;
                font-family: 'Fira Code', 'Consolas', monospace;
                font-size: 14px;
                line-height: 1.5;
                overflow-x: auto;
                tab-size: 4;
            }

            /* Add a copy button */
            .documentation-section pre::after {
                content: "Copy";
                position: absolute;
                top: 12px;
                right: 12px;
                padding: 6px 12px;
                font-size: 12px;
                color: #94a3b8;
                background: rgba(255, 255, 255, 0.1);
                border-radius: 4px;
                cursor: pointer;
                opacity: 0;
                transition: all 0.2s ease;
            }

            .documentation-section pre:hover::after {
                opacity: 1;
            }

            .documentation-section pre.copied::after {
                content: "Copied!";
                background: #059669;
                color: white;
            }

            /* HTML syntax highlighting */
            .documentation-section pre code.language-html {
                color: #d4d4d4;
            }

            .documentation-section pre code.language-html .tag {
                color: #569cd6;
            }

            .documentation-section pre code.language-html .attr-name {
                color: #9cdcfe;
            }

            .documentation-section pre code.language-html .attr-value {
                color: #ce9178;
            }

            /* Utils Tab Specific */
            .utils-container {
                height: 100%;
                overflow-y: auto;
                padding-bottom: 20px;
            }

            /* Utils Search Styling */
            .utils-search {
                position: relative;
                margin-bottom: 24px;
            }

            .utils-search input {
                width: 100%;
                padding: 12px 16px;
                padding-left: 42px;
                background: #f8fafc;
                border: 1px solid #e2e8f0;
                border-radius: 8px;
                font-size: 14px;
                color: #1e293b;
                transition: all 0.2s ease;
            }

            .utils-search input:focus {
                outline: none;
                border-color: #3730a3;
                box-shadow: 0 0 0 3px rgba(55, 48, 163, 0.1);
                background: #ffffff;
            }

            .utils-search .search-icon {
                position: absolute;
                left: 14px;
                top: 50%;
                transform: translateY(-50%);
                color: #94a3b8;
                pointer-events: none;
            }

            /* Category Badges */
            .category-badges {
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
                margin-bottom: 24px;
            }

            .category-badge {
                display: flex;
                align-items: center;
                gap: 8px;
                padding: 6px 12px;
                background: #f1f5f9;
                border: 1px solid #e2e8f0;
                border-radius: 20px;
                cursor: pointer;
                transition: all 0.2s ease;
            }

            .category-badge:hover {
                background: #e2e8f0;
                border-color: #cbd5e1;
            }

            .category-badge.active {
                background: #3730a3;
                border-color: #312e81;
                color: white;
            }

            .badge-name {
                font-size: 13px;
                font-weight: 500;
            }

            .badge-count {
                display: flex;
                align-items: center;
                justify-content: center;
                min-width: 20px;
                height: 20px;
                padding: 0 6px;
                background: rgba(255, 255, 255, 0.2);
                border-radius: 10px;
                font-size: 12px;
                font-weight: 600;
            }

            .category-badge:not(.active) .badge-count {
                background: #e2e8f0;
                color: #64748b;
            }

            /* Utils Grid */
            .utils-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
                gap: 16px;
                margin-top: 20px;
            }

            .util-card {
                background: #ffffff;
                border: 1px solid #e2e8f0;
                border-radius: 8px;
                padding: 16px;
                transition: all 0.2s ease;
                cursor: pointer;
            }

            .util-card:hover {
                border-color: #3730a3;
                transform: translateY(-2px);
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            }

            .util-card.active {
                border-color: #3730a3;
                background: #f8fafc;
            }

            .util-name {
                font-weight: 600;
                color: #1e293b;
                margin-bottom: 8px;
            }

            .util-description {
                font-size: 13px;
                color: #64748b;
                line-height: 1.5;
            }

            /* Utils Detail Panel */
            .util-detail {
                margin-top: 20px;
                padding: 20px;
                background: #f8fafc;
                border: 1px solid #e2e8f0;
                border-radius: 8px;
                display: none;
            }

            .util-detail.active {
                display: block;
            }

            .util-detail h3 {
                margin: 0 0 16px 0;
                color: #1e293b;
                font-size: 18px;
            }

            .util-detail pre {
                margin: 16px 0;
                background: #1e1e1e;
                padding: 16px;
                border-radius: 8px;
                overflow-x: auto;
            }

            .util-detail code {
                color: #e4e4e4;
                font-family: 'JetBrains Mono', monospace;
                font-size: 13px;
            }

            /* Scrollbar Styling */
            .dev-tools-tab-content::-webkit-scrollbar,
            .components-grid::-webkit-scrollbar,
            .documentation-container::-webkit-scrollbar,
            .utils-container::-webkit-scrollbar {
                width: 8px;
            }

            .dev-tools-tab-content::-webkit-scrollbar-track,
            .components-grid::-webkit-scrollbar-track,
            .documentation-container::-webkit-scrollbar-track,
            .utils-container::-webkit-scrollbar-track {
                background: #f1f5f9;
            }

            .dev-tools-tab-content::-webkit-scrollbar-thumb,
            .components-grid::-webkit-scrollbar-thumb,
            .documentation-container::-webkit-scrollbar-thumb,
            .utils-container::-webkit-scrollbar-thumb {
                background: #cbd5e1;
                border-radius: 4px;
            }

            .dev-tools-tab-content::-webkit-scrollbar-thumb:hover,
            .components-grid::-webkit-scrollbar-thumb:hover,
            .documentation-container::-webkit-scrollbar-thumb:hover,
            .utils-container::-webkit-scrollbar-thumb:hover {
                background: #94a3b8;
            }

            .lebify-devTool-util-card {
                background: #ffffff;
                border: 1px solid #e2e8f0;
                border-radius: 12px;
                padding: 20px;
                transition: all 0.2s ease;
                cursor: pointer;
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                height: 100%;
                min-height: 160px;
            }

            .lebify-devTool-util-card:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
                border-color: #3730a3;
            }

            .lebify-devTool-util-content {
                flex-grow: 1;
                margin-bottom: 16px;
            }

            .lebify-devTool-util-name {
                font-size: 16px;
                font-weight: 600;
                color: #1e293b;
                margin-bottom: 8px;
            }

            .lebify-devTool-util-description {
                color: #64748b;
                font-size: 14px;
                line-height: 1.5;
            }

            .lebify-devTool-util-category {
                display: flex;
                align-items: center;
                gap: 6px;
                padding: 6px 12px;
                background: #f8fafc;
                border-radius: 6px;
                color: #3730a3;
                font-size: 13px;
                font-weight: 500;
                margin-top: auto;
                width: fit-content;
            }

            /* Copy Button Styles */
            .lebify-devTool-copy-btn {
                display: flex;
                align-items: center;
                gap: 6px;
                padding: 6px 12px;
                background: white;
                border: 1px solid #e2e8f0;
                border-radius: 6px;
                color: #64748b;
                font-size: 12px;
                font-weight: 500;
                cursor: pointer;
                transition: all 0.2s ease;
            }

            .lebify-devTool-copy-btn:hover {
                background: #f8fafc;
                border-color: #cbd5e1;
                color: #3730a3;
            }

            .lebify-devTool-copy-btn.copied {
                background: #dcfce7;
                border-color: #bbf7d0;
                color: #059669;
            }

            .lebify-devTool-copy-btn.copied .lebify-devTool-copy-text {
                content: 'Copied!';
            }

            .lebify-devTool-copy-btn.copied .lebify-devTool-copy-text::before {
                content: 'Copied!';
            }

            .lebify-devTool-copy-btn svg {
                stroke-width: 1.5;
            }

            /* Modal Styles */
            .lebify-devTool-util-modal {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                z-index: 10000;
            }

            .lebify-devTool-util-modal.active {
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .lebify-devTool-util-modal-backdrop {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                backdrop-filter: blur(4px);
                opacity: 0;
                transition: opacity 0.3s ease;
            }

            .lebify-devTool-util-modal.show .lebify-devTool-util-modal-backdrop {
                opacity: 1;
            }

            .lebify-devTool-util-modal-content {
                position: relative;
                width: 90%;
                max-width: 800px;
                max-height: 90vh;
                background: #ffffff;
                border-radius: 16px;
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
                overflow: hidden;
                opacity: 0;
                transform: scale(0.95);
                transition: all 0.3s ease;
            }

            .lebify-devTool-util-modal.show .lebify-devTool-util-modal-content {
                opacity: 1;
                transform: scale(1);
            }

            .lebify-devTool-util-modal-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 20px 24px;
                background: #f8fafc;
                border-bottom: 1px solid #e2e8f0;
            }

            .lebify-devTool-util-modal-title {
                display: flex;
                align-items: center;
                gap: 12px;
            }

            .lebify-devTool-util-modal-title h3 {
                margin: 0;
                font-size: 20px;
                color: #1e293b;
            }

            .lebify-devTool-util-modal-category {
                padding: 4px 12px;
                background: #f1f5f9;
                border-radius: 20px;
                color: #3730a3;
                font-size: 13px;
                font-weight: 500;
            }

            .lebify-devTool-util-modal-close {
                background: none;
                border: none;
                color: #64748b;
                padding: 8px;
                border-radius: 8px;
                cursor: pointer;
                transition: all 0.2s ease;
            }

            .lebify-devTool-util-modal-close:hover {
                background: #f1f5f9;
                color: #ef4444;
            }

            .lebify-devTool-util-modal-body {
                padding: 24px;
                overflow-y: auto;
                max-height: calc(90vh - 81px);
            }

            .lebify-devTool-util-modal-description {
                color: #475569;
                font-size: 15px;
                line-height: 1.6;
                margin-bottom: 24px;
            }

            /* Code Block Styles */
            .lebify-devTool-code-block {
                margin-bottom: 24px;
                border-radius: 12px;
                overflow: hidden;
                border: 1px solid #e2e8f0;
            }

            .lebify-devTool-code-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 12px 16px;
                background: #f8fafc;
                border-bottom: 1px solid #e2e8f0;
            }

            .lebify-devTool-code-header span {
                color: #64748b;
                font-size: 13px;
                font-weight: 500;
            }

            .lebify-devTool-copy-btn {
                display: flex;
                align-items: center;
                gap: 6px;
                padding: 6px 12px;
                background: white;
                border: 1px solid #e2e8f0;
                border-radius: 6px;
                color: #64748b;
                font-size: 12px;
                font-weight: 500;
                cursor: pointer;
                transition: all 0.2s ease;
            }

            .lebify-devTool-copy-btn:hover {
                background: #f8fafc;
                border-color: #cbd5e1;
                color: #3730a3;
            }

            .lebify-devTool-copy-btn svg {
                stroke-width: 1.5;
            }

            .lebify-devTool-code-block pre {
                margin: 0;
                padding: 20px;
                background: #1e1e1e;
                overflow-x: auto;
            }

            .lebify-devTool-code-block code {
                font-family: 'JetBrains Mono', 'Fira Code', monospace;
                font-size: 13px;
                line-height: 1.6;
                color: #d4d4d4;
            }

            /* Parameters Grid */
            .lebify-devTool-params-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
                gap: 16px;
                margin-top: 16px;
            }

            .lebify-devTool-param-card {
                background: #f8fafc;
                border: 1px solid #e2e8f0;
                border-radius: 8px;
                padding: 16px;
            }

            .lebify-devTool-param-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                margin-bottom: 8px;
            }

            .lebify-devTool-param-name {
                font-family: 'JetBrains Mono', monospace;
                color: #3730a3;
                font-size: 14px;
                font-weight: 600;
            }

            .lebify-devTool-param-type {
                padding: 2px 8px;
                background: white;
                border: 1px solid #e2e8f0;
                border-radius: 12px;
                color: #64748b;
                font-size: 12px;
                font-weight: 500;
            }

            .lebify-devTool-param-description {
                color: #475569;
                font-size: 13px;
                line-height: 1.5;
            }

            .documentation-section pre code {
                font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', 'Consolas', monospace;
                line-height: 1.5;
                tab-size: 4;
            }

            .documentation-section pre code .tag {
                color: #569CD6;
            }

            .documentation-section pre code .attr-name {
                color: #9CDCFE;
            }

            .documentation-section pre code .attr-value {
                color: #CE9178;
            }

            .documentation-section pre code .comment {
                color: #6A9955;
                font-style: italic;
            }

            /* Classic Tab Specific */
            .classic-container {
                height: 100%;
                overflow-y: auto;
                padding: 20px;
            }

            .classic-header {
                margin-bottom: 24px;
            }

            .classic-search {
                position: relative;
                margin-bottom: 24px;
            }

            .classic-search input {
                width: 100%;
                padding: 12px 16px;
                padding-left: 42px;
                background: #f8fafc;
                border: 1px solid #e2e8f0;
                border-radius: 8px;
                font-size: 14px;
                color: #1e293b;
                transition: all 0.2s ease;
            }

            .classic-search input:focus {
                outline: none;
                border-color: #3730a3;
                box-shadow: 0 0 0 3px rgba(55, 48, 163, 0.1);
                background: #ffffff;
            }

            .classic-search .search-icon {
                position: absolute;
                left: 14px;
                top: 50%;
                transform: translateY(-50%);
                color: #94a3b8;
                pointer-events: none;
            }

            .classic-grid {
                display: grid;
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .classic-class-card {
                background: #ffffff;
                border: 1px solid #e2e8f0;
                border-radius: 12px;
                overflow: hidden;
            }

            .classic-class-header {
                padding: 20px;
                background: #f8fafc;
                border-bottom: 1px solid #e2e8f0;
            }

            .classic-class-name {
                font-size: 18px;
                font-weight: 600;
                color: #1e293b;
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .classic-class-name svg {
                color: #3730a3;
            }

            .classic-class-content {
                padding: 20px;
            }

            .classic-class-example {
                margin-bottom: 24px;
            }

            .classic-class-example pre {
                margin: 0;
                padding: 16px;
                background: #1e1e1e;
                border-radius: 8px;
                overflow-x: auto;
            }

            .classic-class-example code {
                color: #e4e4e4;
                font-family: 'JetBrains Mono', monospace;
                font-size: 13px;
                line-height: 1.6;
            }

            .classic-params-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
                gap: 16px;
            }

            .classic-param-card {
                background: #f8fafc;
                border: 1px solid #e2e8f0;
                border-radius: 8px;
                padding: 16px;
            }

            .classic-param-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                margin-bottom: 8px;
            }

            .classic-param-name {
                font-family: 'JetBrains Mono', monospace;
                color: #3730a3;
                font-size: 14px;
                font-weight: 600;
            }

            .classic-param-required {
                padding: 2px 8px;
                background: #fee2e2;
                color: #991b1b;
                border-radius: 12px;
                font-size: 12px;
                font-weight: 500;
            }

            .classic-param-optional {
                padding: 2px 8px;
                background: #e0e7ff;
                color: #3730a3;
                border-radius: 12px;
                font-size: 12px;
                font-weight: 500;
            }

            .classic-param-type {
                font-family: 'JetBrains Mono', monospace;
                color: #64748b;
                font-size: 12px;
                margin-bottom: 8px;
            }

            .classic-param-description {
                color: #475569;
                font-size: 13px;
                line-height: 1.5;
            }

            .classic-example-block {
                margin-bottom: 24px;
            }

            .classic-example-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                margin-bottom: 12px;
            }

            .classic-example-title {
                font-size: 16px;
                font-weight: 600;
                color: #1e293b;
            }

            .classic-example pre {
                margin: 0;
                padding: 16px;
                background: #1e1e1e;
                border-radius: 8px;
                overflow-x: auto;
            }

            .classic-example code {
                font-family: 'JetBrains Mono', monospace;
                font-size: 13px;
                line-height: 1.5;
                color: #e4e4e4;
            }

            .classic-placeholder {
                text-align: center;
                color: #64748b;
                padding: 48px 24px;
                background: #f8fafc;
                border-radius: 12px;
                border: 1px solid #e2e8f0;
            }

            .classic-placeholder svg {
                width: 48px;
                height: 48px;
                color: #94a3b8;
                margin-bottom: 16px;
            }

            .classic-placeholder-text {
                font-size: 15px;
                font-weight: 500;
            }

            /* DataTable Tab Specific */
            .datatable-container {
                height: 100%;
                overflow-y: auto;
                padding: 20px;
            }

            .datatable-header {
                margin-bottom: 24px;
            }

            .datatable-content {
                background: #ffffff;
                border: 1px solid #e2e8f0;
                border-radius: 12px;
                overflow: hidden;
            }

            .datatable-section {
                padding: 24px;
            }

            .datatable-section:not(:last-child) {
                border-bottom: 1px solid #e2e8f0;
            }

            .datatable-section-title {
                font-size: 18px;
                font-weight: 600;
                color: #1e293b;
                margin-bottom: 16px;
            }

            .datatable-section-description {
                color: #475569;
                font-size: 15px;
                line-height: 1.6;
                margin-bottom: 20px;
            }

            .datatable-code-block {
                margin: 20px 0;
                background: #1e1e1e;
                border-radius: 8px;
                overflow: hidden;
            }

            .datatable-code-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 12px 16px;
                background: #2d2d2d;
                color: #e4e4e4;
            }

            .datatable-code-content {
                padding: 16px;
                overflow-x: auto;
            }

            .datatable-code-content code {
                font-family: 'JetBrains Mono', monospace;
                font-size: 14px;
                line-height: 1.5;
                color: #e4e4e4;
            }
        `;
        document.head.appendChild(styles);
    }
    static setupEventListeners() {
        const fab = document.getElementById('devToolsFAB');
        const modal = document.getElementById('devToolsModal');
        const closeBtn = document.getElementById('devToolsClose');
        const tabs = document.querySelectorAll('.dev-tools-tab');

        // Open modal
        fab.addEventListener('click', () => {
            modal.style.display = 'block';
            setTimeout(() => modal.classList.add('active'), 10);
        });

        // Close modal
        closeBtn.addEventListener('click', () => {
            modal.classList.remove('active');
            setTimeout(() => modal.style.display = 'none', 300);
        });

        // Close on outside click
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.classList.remove('active');
                setTimeout(() => modal.style.display = 'none', 300);
            }
        });

        // Tab switching
        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                tabs.forEach(t => t.classList.remove('active'));
                tab.classList.add('active');

                const tabContents = document.querySelectorAll('.dev-tools-tab-content');
                tabContents.forEach(content => content.classList.remove('active'));

                const targetTab = document.getElementById(`${tab.dataset.tab}Tab`);
                targetTab.classList.add('active');
            });
        });
    }

    static updateDevToolsContent() {
        // Components Tab
        const componentsTab = document.getElementById('componentsTab');
        if (componentsTab) {
            const availableComponents = componentsTab.querySelector('#devToolAvailableComponents');
            const initializedComponents = componentsTab.querySelector('#devToolInitializedComponents');

            if (availableComponents) {
                availableComponents.innerHTML = Object.keys(this.componentMap)
                    .map(id => `
                        <div class="component-item">
                            <span class="component-name">${id}</span>
                            <span class="component-status available">Available</span>
                        </div>
                    `).join('');
            }

            if (initializedComponents) {
                initializedComponents.innerHTML = Array.from(this.initializedComponents)
                    .map(id => `
                        <div class="component-item">
                            <span class="component-name">${id}</span>
                            <span class="component-status initialized">Initialized</span>
                        </div>
                    `).join('');
            }
        }

        // Documentation Tab
        const documentationTab = document.getElementById('documentationTab');
        if (documentationTab) {
            const docSelector = documentationTab.querySelector('#devToolDocSelector');
            if (docSelector) {
                docSelector.innerHTML = `
                    <option value="">Select a component...</option>
                    ${Object.keys(this.componentMap)
                        .map(id => `<option value="${id}">${id}</option>`)
                        .join('')}
                `;

                // Add documentation selector event listener
                docSelector.addEventListener('change', async (e) => {
                    const documentation = document.getElementById('devToolDocumentation');
                    const selectedComponent = e.target.value;

                    if (selectedComponent && documentation) {
                        const docData = this.getDocumentation(selectedComponent);

                        documentation.innerHTML = `
                            <div class="documentation-content">
                                <div class="documentation-header">
                                    <div>
                                        <h4>${docData.title}</h4>
                                        <p class="documentation-description">${docData.description}</p>
                                    </div>
                                </div>
                                <div class="documentation-tabs">
                                    <button class="documentation-tab active" data-section="usage">Usage</button>
                                    <button class="documentation-tab" data-section="html">HTML Structure</button>
                                    <button class="documentation-tab" data-section="attributes">Attributes</button>
                                    <button class="documentation-tab" data-section="functionality">Functionality</button>
                                    <button class="documentation-tab" data-section="functions">Functions</button>
                                </div>
                                <div class="documentation-sections">
                                    <div class="documentation-section active" id="usage">
                                        <pre><code class="language-javascript">${this.escapeHtml(docData.example)}</code></pre>
                                    </div>
                                    <div class="documentation-section" id="html">
                                        <pre><code class="language-html">${this.escapeHtml(docData.html)}</code></pre>
                                    </div>
                                    <div class="documentation-section" id="attributes">
                                        ${this.formatAttributes(docData.attributes) || 'No attributes documentation available.'}
                                    </div>
                                    <div class="documentation-section" id="functionality">
                                        ${this.formatFeatures(docData.features) || 'No functionality documentation available.'}
                                    </div>
                                    <div class="documentation-section" id="functions">
                                        <pre><code class="language-javascript">${this.escapeHtml(docData.functions)}</code></pre>
                                    </div>
                                </div>
                            </div>
                        `;

                        // Add tab switching functionality
                        const docTabs = documentation.querySelectorAll('.documentation-tab');
                        docTabs.forEach(tab => {
                            tab.addEventListener('click', () => {
                                docTabs.forEach(t => t.classList.remove('active'));
                                documentation.querySelectorAll('.documentation-section').forEach(s => s.classList.remove('active'));

                                tab.classList.add('active');
                                const sectionId = tab.dataset.section;
                                const section = documentation.querySelector(`#${sectionId}`);
                                if (section) section.classList.add('active');
                            });
                        });

                        // Initialize syntax highlighting if using Prism.js
                        if (window.Prism) {
                            Prism.highlightAllUnder(documentation);
                        }

                        // Add copy functionality for code blocks
                        this.setupCodeBlockCopy(documentation);
                    } else if (documentation) {
                        documentation.innerHTML = `
                            <div class="documentation-placeholder">
                                <p>Select a component to view its documentation</p>
                            </div>
                        `;
                    }
                });
            }
        }

        // Utils Tab
        const utilsTab = document.getElementById('utilsTab');
        if (utilsTab) {
            this.setupUtils();
        }

        // Classic Tab
        const classicTab = document.getElementById('classicTab');
        if (classicTab) {
            classicTab.innerHTML = `
                <div class="classic-container">
                    <div class="classic-header">
                        <div class="documentation-select-container">
                            <select id="classicSelector" class="documentation-select">
                                <option value="">Select a classic class...</option>
                                ${CLASSIC_CLASSES.map(classItem => `
                                    <option value="${classItem.name}">${classItem.name}</option>
                                `).join('')}
                            </select>
                        </div>
                    </div>
                    <div id="classicContent" class="classic-content"></div>
                </div>
            `;

            // Add styles for Classic tab
            const styles = document.createElement('style');
            styles.textContent = `
                .classic-container {
                    height: 100%;
                    overflow-y: auto;
                    padding: 20px;
                }

                .classic-content {
                    margin-top: 24px;
                }

                .classic-documentation {
                    background: #ffffff;
                    border: 1px solid #e2e8f0;
                    border-radius: 12px;
                    overflow: hidden;
                }

                .classic-tabs {
                    display: flex;
                    gap: 2px;
                    padding: 16px 24px;
                    background: #f8fafc;
                    border-bottom: 1px solid #e2e8f0;
                }

                .classic-tab {
                    padding: 8px 16px;
                    border: none;
                    background: transparent;
                    color: #64748b;
                    border-radius: 6px;
                    cursor: pointer;
                    font-size: 14px;
                    font-weight: 500;
                    transition: all 0.2s ease;
                }

                .classic-tab:hover {
                    color: #3730a3;
                    background: #f1f5f9;
                }

                .classic-tab.active {
                    background: #3730a3;
                    color: white;
                }

                .classic-section {
                    display: none;
                    padding: 24px;
                }

                .classic-section.active {
                    display: block;
                }

                .classic-params-grid {
                    display: grid;
                    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
                    gap: 16px;
                    margin-bottom: 24px;
                }

                .classic-param-card {
                    background: #f8fafc;
                    border: 1px solid #e2e8f0;
                    border-radius: 8px;
                    padding: 16px;
                }

                .classic-param-header {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    margin-bottom: 8px;
                }

                .classic-param-name {
                    font-family: 'JetBrains Mono', monospace;
                    color: #3730a3;
                    font-size: 14px;
                    font-weight: 600;
                }

                .classic-param-required {
                    padding: 2px 8px;
                    background: #fee2e2;
                    color: #991b1b;
                    border-radius: 12px;
                    font-size: 12px;
                    font-weight: 500;
                }

                .classic-param-optional {
                    padding: 2px 8px;
                    background: #e0e7ff;
                    color: #3730a3;
                    border-radius: 12px;
                    font-size: 12px;
                    font-weight: 500;
                }

                .classic-param-type {
                    font-family: 'JetBrains Mono', monospace;
                    color: #64748b;
                    font-size: 12px;
                    margin-bottom: 8px;
                }

                .classic-param-description {
                    color: #475569;
                    font-size: 13px;
                    line-height: 1.5;
                }

                .classic-example-block {
                    margin-bottom: 24px;
                }

                .classic-example-header {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    margin-bottom: 12px;
                }

                .classic-example-title {
                    font-size: 16px;
                    font-weight: 600;
                    color: #1e293b;
                }

                .classic-example pre {
                    margin: 0;
                    padding: 16px;
                    background: #1e1e1e;
                    border-radius: 8px;
                    overflow-x: auto;
                }

                .classic-example code {
                    font-family: 'JetBrains Mono', monospace;
                    font-size: 13px;
                    line-height: 1.5;
                    color: #e4e4e4;
                }

                .classic-placeholder {
                    text-align: center;
                    color: #64748b;
                    padding: 48px 24px;
                    background: #f8fafc;
                    border-radius: 12px;
                    border: 1px solid #e2e8f0;
                }

                .classic-placeholder svg {
                    width: 48px;
                    height: 48px;
                    color: #94a3b8;
                    margin-bottom: 16px;
                }

                .classic-placeholder-text {
                    font-size: 15px;
                    font-weight: 500;
                }
            `;
            document.head.appendChild(styles);

            // Handle class selection
            const classicSelector = document.getElementById('classicSelector');
            const classicContent = document.getElementById('classicContent');

            const renderClassDocumentation = (classItem) => {
                classicContent.innerHTML = `
                    <div class="classic-documentation">
                        <div class="classic-tabs">
                            <button class="classic-tab active" data-tab="parameters">Parameters</button>
                            <button class="classic-tab" data-tab="javascript">JavaScript Example</button>
                            ${this.escapeHtml(classItem.BladeExample) ? `
                                <button class="classic-tab" data-tab="html">HTML Example</button>
                            ` : ''}
                        </div>

                        <div class="classic-section active" data-section="parameters">
                            <div class="classic-params-grid">
                                ${Object.entries(classItem.params).map(([paramName, param]) => `
                                    <div class="classic-param-card">
                                        <div class="classic-param-header">
                                            <span class="classic-param-name">${paramName}</span>
                                            <span class="classic-param-${param.required ? 'required' : 'optional'}">
                                                ${param.required ? 'Required' : 'Optional'}
                                            </span>
                                        </div>
                                        <div class="classic-param-type">${param.type}</div>
                                        <div class="classic-param-description">${param.description}</div>
                                    </div>
                                `).join('')}
                            </div>
                        </div>

                        <div class="classic-section" data-section="javascript">
                            <div class="classic-example-block">
                                <div class="classic-example-header">
                                    <span class="classic-example-title">JavaScript Implementation</span>
                                    <button class="lebify-devTool-copy-btn" onclick="this.classList.add('copied'); setTimeout(() => this.classList.remove('copied'), 2000); navigator.clipboard.writeText(this.parentElement.nextElementSibling.textContent)">
                                        <svg viewBox="0 0 24 24" width="16" height="16">
                                            <path d="M8 4v12a2 2 0 002 2h8a2 2 0 002-2V7.242a2 2 0 00-.602-1.43L16.083 2.57A2 2 0 0014.685 2H10a2 2 0 00-2 2z" stroke="currentColor" fill="none"/>
                                            <path d="M16 18v2a2 2 0 01-2 2H6a2 2 0 01-2-2V9a2 2 0 012-2h2" stroke="currentColor" fill="none"/>
                                        </svg>
                                        <span class="lebify-devTool-copy-text">Copy</span>
                                    </button>
                                </div>
                                <pre><code class="language-javascript">${classItem.JavaScriptExample}</code></pre>
                            </div>
                        </div>

                        ${classItem.BladeExample ? `
                            <div class="classic-section" data-section="html">
                                <div class="classic-example-block">
                                    <div class="classic-example-header">
                                        <span class="classic-example-title">HTML Structure</span>
                                        <button class="lebify-devTool-copy-btn" onclick="this.classList.add('copied'); setTimeout(() => this.classList.remove('copied'), 2000); navigator.clipboard.writeText(this.parentElement.nextElementSibling.textContent)">
                                            <svg viewBox="0 0 24 24" width="16" height="16">
                                                <path d="M8 4v12a2 2 0 002 2h8a2 2 0 002-2V7.242a2 2 0 00-.602-1.43L16.083 2.57A2 2 0 0014.685 2H10a2 2 0 00-2 2z" stroke="currentColor" fill="none"/>
                                                <path d="M16 18v2a2 2 0 01-2 2H6a2 2 0 01-2-2V9a2 2 0 012-2h2" stroke="currentColor" fill="none"/>
                                            </svg>
                                            <span class="lebify-devTool-copy-text">Copy</span>
                                        </button>
                                    </div>
                                    <pre><code class="language-html">${this.escapeHtml(classItem.BladeExample)}</code></pre>
                                </div>
                            </div>
                        ` : ''}
                    </div>
                `;

                // Add tab switching functionality
                const tabs = classicContent.querySelectorAll('.classic-tab');
                tabs.forEach(tab => {
                    tab.addEventListener('click', () => {
                        tabs.forEach(t => t.classList.remove('active'));
                        tab.classList.add('active');

                        const sections = classicContent.querySelectorAll('.classic-section');
                        sections.forEach(section => section.classList.remove('active'));

                        const targetSection = classicContent.querySelector(`[data-section="${tab.dataset.tab}"]`);
                        if (targetSection) targetSection.classList.add('active');
                    });
                });

                // Initialize syntax highlighting if using Prism.js
                if (window.Prism) {
                    Prism.highlightAllUnder(classicContent);
                }
            };

            // Show placeholder when no class is selected
            const showPlaceholder = () => {
                classicContent.innerHTML = `
                    <div class="classic-placeholder">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                        </svg>
                        <div class="classic-placeholder-text">
                            Select a classic class to view its documentation
                        </div>
                    </div>
                `;
            };

            // Initialize with placeholder
            showPlaceholder();

            // Handle selection changes
            classicSelector.addEventListener('change', (e) => {
                const selectedClass = CLASSIC_CLASSES.find(c => c.name === e.target.value);
                if (selectedClass) {
                    renderClassDocumentation(selectedClass);
                } else {
                    showPlaceholder();
                }
            });
        }

        // DataTable Tab
        const datatableTab = document.getElementById('datatableTab');
        if (datatableTab) {
            this.setupDatatableTab();
        }
    }

    static setupUtils() {
        const searchInput = document.getElementById('utilsSearchInput');
        const utilsGrid = document.getElementById('utilsGrid');
        const utilDetail = document.getElementById('utilDetail');
        const categoryBadges = document.getElementById('categoryBadges');

        // Get categories and their counts
        const categories = UTILITY_FUNCTIONS.reduce((acc, util) => {
            acc[util.category] = (acc[util.category] || 0) + 1;
            return acc;
        }, {});

        // Render category badges
        categoryBadges.innerHTML = `
            <div class="category-badge active" data-category="all">
                <span class="badge-name">All</span>
                <span class="badge-count">${UTILITY_FUNCTIONS.length}</span>
            </div>
            ${Object.entries(categories).map(([category, count]) => `
                <div class="category-badge" data-category="${category}">
                    <span class="badge-name">${category}</span>
                    <span class="badge-count">${count}</span>
                </div>
            `).join('')}
        `;

        // Add category filter functionality
        let activeCategory = 'all';
        let searchTerm = '';

        const filterUtils = () => {
            const filtered = UTILITY_FUNCTIONS.filter(util => {
                const matchesCategory = activeCategory === 'all' || util.category === activeCategory;
                const matchesSearch = util.name.toLowerCase().includes(searchTerm) ||
                    util.description.toLowerCase().includes(searchTerm);
                return matchesCategory && matchesSearch;
            });
            renderUtils(filtered);
        };

        categoryBadges.querySelectorAll('.category-badge').forEach(badge => {
            badge.addEventListener('click', () => {
                categoryBadges.querySelectorAll('.category-badge').forEach(b => b.classList.remove('active'));
                badge.classList.add('active');
                activeCategory = badge.dataset.category;
                filterUtils();
            });
        });

        // Update search functionality
        searchInput.addEventListener('input', (e) => {
            searchTerm = e.target.value.toLowerCase();
            filterUtils();
            utilDetail.classList.remove('active');
        });

        // Render utils grid
        const renderUtils = (utils) => {
            utilsGrid.innerHTML = utils.map(util => `
                <div class="lebify-devTool-util-card" data-util="${util.name}">
                    <div class="lebify-devTool-util-content">
                        <div class="lebify-devTool-util-name">${util.name}</div>
                        <div class="lebify-devTool-util-description">${util.description}</div>
                    </div>
                    <div class="lebify-devTool-util-category">
                        <svg class="lebify-devTool-category-icon" viewBox="0 0 24 24" width="16" height="16">
                            <path d="M4 9h16M4 15h16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                        ${util.category}
                    </div>
                </div>
            `).join('');

            // Create a cool modal for utility details
            const modal = document.createElement('div');
            modal.className = 'lebify-devTool-util-modal';
            modal.innerHTML = `
                <div class="lebify-devTool-util-modal-backdrop"></div>
                <div class="lebify-devTool-util-modal-content">
                    <div class="lebify-devTool-util-modal-header">
                        <div class="lebify-devTool-util-modal-title">
                            <h3></h3>
                            <span class="lebify-devTool-util-modal-category"></span>
                        </div>
                        <button class="lebify-devTool-util-modal-close">
                            <svg viewBox="0 0 24 24" width="24" height="24">
                                <path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                        </button>
                    </div>
                    <div class="lebify-devTool-util-modal-body"></div>
                </div>
            `;
            document.body.appendChild(modal);

            // Add click handlers to cards
            utilsGrid.querySelectorAll('.lebify-devTool-util-card').forEach(card => {
                card.addEventListener('click', () => {
                    const util = UTILITY_FUNCTIONS.find(u => u.name === card.dataset.util);
                    if (util) {
                        const modalTitle = modal.querySelector('.lebify-devTool-util-modal-title h3');
                        const modalCategory = modal.querySelector('.lebify-devTool-util-modal-category');
                        const modalBody = modal.querySelector('.lebify-devTool-util-modal-body');

                        modalTitle.textContent = util.name;
                        modalCategory.textContent = util.category;
                        modalBody.innerHTML = `
                            <div class="lebify-devTool-util-modal-description">
                                ${util.description}
                            </div>

                            <div class="lebify-devTool-util-modal-section">
                                <div class="lebify-devTool-code-block">
                                    <div class="lebify-devTool-code-header">
                                        <span>Usage</span>
                                        <button class="lebify-devTool-copy-btn" onclick="this.classList.add('copied'); setTimeout(() => this.classList.remove('copied'), 2000); navigator.clipboard.writeText(this.parentElement.nextElementSibling.textContent)">
                                            <svg viewBox="0 0 24 24" width="16" height="16">
                                                <path d="M8 4v12a2 2 0 002 2h8a2 2 0 002-2V7.242a2 2 0 00-.602-1.43L16.083 2.57A2 2 0 0014.685 2H10a2 2 0 00-2 2z" stroke="currentColor" fill="none"/>
                                                <path d="M16 18v2a2 2 0 01-2 2H6a2 2 0 01-2-2V9a2 2 0 012-2h2" stroke="currentColor" fill="none"/>
                                            </svg>
                                            <span class="lebify-devTool-copy-text">Copy</span>
                                        </button>
                                    </div>
                                    <pre><code class="language-javascript">${util.usage}</code></pre>
                                </div>
                            </div>

                            <div class="lebify-devTool-util-modal-section">
                                <div class="lebify-devTool-code-block">
                                    <div class="lebify-devTool-code-header">
                                        <span>Example</span>
                                        <button class="lebify-devTool-copy-btn" onclick="this.classList.add('copied'); setTimeout(() => this.classList.remove('copied'), 2000); navigator.clipboard.writeText(this.parentElement.nextElementSibling.textContent)">
                                            <svg viewBox="0 0 24 24" width="16" height="16">
                                                <path d="M8 4v12a2 2 0 002 2h8a2 2 0 002-2V7.242a2 2 0 00-.602-1.43L16.083 2.57A2 2 0 0014.685 2H10a2 2 0 00-2 2z" stroke="currentColor" fill="none"/>
                                                <path d="M16 18v2a2 2 0 01-2 2H6a2 2 0 01-2-2V9a2 2 0 012-2h2" stroke="currentColor" fill="none"/>
                                            </svg>
                                            <span class="lebify-devTool-copy-text">Copy</span>
                                        </button>
                                    </div>
                                    <pre><code class="language-javascript">${util.example}</code></pre>
                                </div>
                            </div>

                            ${util.params ? `
                                <div class="lebify-devTool-util-modal-section">
                                    <h4>Parameters</h4>
                                    <div class="lebify-devTool-params-grid">
                                        ${util.params.map(param => `
                                            <div class="lebify-devTool-param-card">
                                                <div class="lebify-devTool-param-header">
                                                    <span class="lebify-devTool-param-name">${param.name}</span>
                                                    <span class="lebify-devTool-param-type">${param.type}</span>
                                                </div>
                                                <div class="lebify-devTool-param-description">${param.description}</div>
                                            </div>
                                        `).join('')}
                                    </div>
                                </div>
                            ` : ''}
                        `;

                        modal.classList.add('active');
                        setTimeout(() => modal.classList.add('show'), 10);
                    }
                });
            });

            // Close modal handlers
            const closeBtn = modal.querySelector('.lebify-devTool-util-modal-close');
            const closeModal = () => {
                modal.classList.remove('show');
                setTimeout(() => modal.classList.remove('active'), 300);
            };

            closeBtn.addEventListener('click', closeModal);
            modal.addEventListener('click', (e) => {
                if (e.target === modal || e.target.classList.contains('lebify-devTool-util-modal-backdrop')) {
                    closeModal();
                }
            });
        };

        // Initial render
        renderUtils(UTILITY_FUNCTIONS);
    }

    static setupDevTools() {
        window.devTools = {
            getDocumentation: this.getDocumentation.bind(this),
            listAvailableComponents: this.listAvailableComponents.bind(this),
            listUsedIdentifiers: this.listUsedIdentifiers.bind(this),
            showInitializedComponents: this.showInitializedComponents.bind(this)
        };
        window.devToolsHelp = this.devToolsHelp.bind(this);
        console.log("🛠️ Dev Tools ready! Use devToolsHelp() to see available functions.");
    }

    static getDocumentation(identifier) {
        const Component = this.componentMap[identifier];
        if (Component && typeof Component.documentation === 'function') {
            const docData = Component.documentation();
            console.log('Raw Documentation Data:', docData);

            // If it's HTML format (contains HTML tags)
            if (typeof docData === 'string' && docData.includes('<div class="devToolDocumentation">')) {
                // Create a temporary element to parse the HTML
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = docData;

                // Extract data from the HTML
                return {
                    title: tempDiv.querySelector('.componentTitle')?.textContent || identifier,
                    description: tempDiv.querySelector('.componentDescription')?.textContent || '',
                    example: tempDiv.querySelector('#usageTab code')?.textContent || '',
                    html: tempDiv.querySelector('#structureTab code')?.textContent || '',
                    attributes: this.parseAttributesFromHTML(tempDiv),
                    features: this.parseFeaturesFromHTML(tempDiv),
                    functions: tempDiv.querySelector('#functionsTab code')?.textContent || ''
                };
            }

            // If it's array format
            if (Array.isArray(docData)) {
                return {
                    title: docData[0],
                    description: docData[1],
                    example: docData[2],
                    html: docData[3],
                    attributes: docData[4],
                    features: docData[5],
                    functions: docData[6]
                };
            }

            return docData;
        }

        return {
            title: identifier,
            description: 'No documentation found.',
            example: '',
            html: '',
            attributes: [],
            features: [],
            functions: ''
        };
    }

    static getUsedIdentifiers() {
        const usedIdentifiers = new Set();
        Object.keys(this.componentMap).forEach(identifier => {
            if (document.querySelector(`[identifier="${identifier}"]`)) {
                usedIdentifiers.add(identifier);
            }
        });
        return usedIdentifiers;
    }

    static listAvailableComponents() {
        console.group('📦 Available Components');
        Object.keys(this.componentMap).forEach(identifier => {
            console.log(`▸ ${identifier}`);
        });
        console.groupEnd();
    }

    static listUsedIdentifiers() {
        console.group('🔍 Used Identifiers');
        this.getUsedIdentifiers().forEach(identifier => {
            console.log(`▸ ${identifier}`);
        });
        console.groupEnd();
    }

    static showInitializedComponents() {
        console.group('✨ Initialized Components');
        this.initializedComponents.forEach(identifier => {
            console.log(`▸ ${identifier}`);
        });
        console.groupEnd();
    }

    static devToolsHelp() {
        console.group('🛠️ Dev Tools Help');
        console.log('Available commands:');
        console.log('▸ devTools.getDocumentation(identifier) - View component documentation');
        console.log('▸ devTools.listAvailableComponents() - List all component identifiers');
        console.log('▸ devTools.listUsedIdentifiers() - List identifiers used on page');
        console.log('▸ devTools.showInitializedComponents() - Show initialized components');
        console.log('\nExample: devTools.getDocumentation("my-component")');
        console.groupEnd();
    }

    static formatAttributes(attributes) {
        if (!Array.isArray(attributes)) {
            console.warn('Invalid attributes data:', attributes);
            return 'No attributes documentation available.';
        }

        if (attributes.length === 0) {
            return 'No attributes documented.';
        }

        return `
            <div class="attributes-table">
                <table>
                    <thead>
                        <tr>
                            <th>Attribute</th>
                            <th>Description</th>
                            <th>Required</th>
                            <th>Default</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${attributes.map(attr => {
            if (!attr || typeof attr !== 'object') {
                console.warn('Invalid attribute data:', attr);
                return '';
            }
            return `
                            <tr>
                                <td><code>${attr.name || 'Unnamed'}</code></td>
                                <td>${attr.description || 'No description'}</td>
                                <td>
                                    <span class="badge ${attr.required ? 'required' : 'optional'}">
                                        ${attr.required ? 'Required' : 'Optional'}
                                    </span>
                                </td>
                                <td>${attr.default ? `<code>${attr.default}</code>` : '-'}</td>
                            </tr>
                        `;
        }).join('')}
                    </tbody>
                </table>
            </div>
        `;
    }

    static formatFeatures(features) {
        if (!Array.isArray(features)) {
            console.warn('Invalid features data:', features);
            return 'No features documentation available.';
        }

        if (features.length === 0) {
            return 'No features documented.';
        }

        return `
            <div class="features-list">
                ${features.map(feature => {
            if (typeof feature !== 'string') {
                console.warn('Invalid feature data:', feature);
                return '';
            }
            return `
                    <div class="feature-item">
                        <span class="feature-bullet">•</span>
                        <span class="feature-text">${feature}</span>
                    </div>
                `;
        }).join('')}
            </div>
        `;
    }

    static escapeHtml(unsafe) {
        if (!unsafe) return '';
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    static parseAttributesFromHTML(element) {
        const rows = element.querySelectorAll('#attributesTab table tbody tr');
        if (!rows.length) return [];

        return Array.from(rows).map(row => {
            const cells = row.querySelectorAll('td');
            return {
                name: cells[0]?.querySelector('code')?.textContent || '',
                description: cells[1]?.textContent || '',
                required: cells[2]?.textContent.includes('✅'),
                default: cells[3]?.querySelector('code')?.textContent || cells[3]?.textContent || ''
            };
        });
    }

    static parseFeaturesFromHTML(element) {
        const functionalityTab = element.querySelector('#functionalityTab');
        if (!functionalityTab) return [];

        // Look for list items or paragraphs that contain features
        const features = Array.from(functionalityTab.querySelectorAll('li, p'))
            .map(el => el.textContent.trim())
            .filter(text => text.length > 0);

        return features;
    }

    static highlightHTML(code) {
        if (!code) return '';

        // First decode any existing HTML entities
        const decodedCode = code
            .replace(/&quot;/g, '"')
            .replace(/&lt;/g, '<')
            .replace(/&gt;/g, '>')
            .replace(/&amp;/g, '&');

        // Then apply highlighting with proper encoding
        return decodedCode
            // Escape HTML entities
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            // Highlight tags
            .replace(/&lt;(\/?[a-zA-Z0-9-]+)(\s|&gt;)/g, '<span class="tag">&lt;$1</span>$2')
            .replace(/&gt;/g, '<span class="tag">&gt;</span>')
            // Highlight attributes
            .replace(/\s([a-zA-Z0-9-]+)=/g, ' <span class="attr-name">$1</span>=')
            // Highlight attribute values
            .replace(/="([^"]*?)"/g, '="<span class="attr-value">$1</span>"')
            // Fix comment highlighting
            .replace(/&lt;!--(.+?)--&gt;/g, '<span class="comment">&lt;!--$1--&gt;</span>');
    }

    static setupCodeBlockCopy(documentation) {
        const codeBlocks = documentation.querySelectorAll('pre');
        codeBlocks.forEach(block => {
            block.addEventListener('click', async (e) => {
                if (e.target === block || e.target.closest('pre') === block) {
                    const code = block.querySelector('code').textContent;
                    try {
                        await navigator.clipboard.writeText(code);
                        block.classList.add('copied');
                        setTimeout(() => block.classList.remove('copied'), 2000);
                    } catch (err) {
                        console.error('Failed to copy code:', err);
                    }
                }
            });
        });
    }

    static setupClassicTab() {
        const classicGrid = document.getElementById('classicGrid');
        const searchInput = document.getElementById('classicSearchInput');

        const renderClassicClasses = (classes) => {
            classicGrid.innerHTML = classes.map(classItem => `
                <div class="classic-class-card">
                    <div class="classic-class-header">
                        <div class="classic-class-name">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
                            </svg>
                            ${classItem.name}
                        </div>
                    </div>
                    <div class="classic-class-content">
                        <div class="classic-class-example">
                            <pre><code>${this.escapeHtml(classItem.example)}</code></pre>
                        </div>
                        <div class="classic-params-grid">
                            ${Object.entries(classItem.params).map(([paramName, param]) => `
                                <div class="classic-param-card">
                                    <div class="classic-param-header">
                                        <span class="classic-param-name">${paramName}</span>
                                        <span class="classic-param-${param.required ? 'required' : 'optional'}">
                                            ${param.required ? 'Required' : 'Optional'}
                                        </span>
                                    </div>
                                    <div class="classic-param-type">${param.type}</div>
                                    <div class="classic-param-description">${param.description}</div>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                </div>
            `).join('');

            // Add syntax highlighting if Prism.js is available
            if (window.Prism) {
                Prism.highlightAllUnder(classicGrid);
            }
        };

        // Initial render
        renderClassicClasses(CLASSIC_CLASSES);

        // Search functionality
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                const searchTerm = e.target.value.toLowerCase();
                const filteredClasses = CLASSIC_CLASSES.filter(classItem =>
                    classItem.name.toLowerCase().includes(searchTerm)
                );
                renderClassicClasses(filteredClasses);
            });
        }
    }

    static setupDatatableTab() {
        const datatableSelector = document.getElementById('datatableSelector');
        const content = document.getElementById('datatableContent');

        const datatableContent = {
            basic: {
                title: 'Basic Usage',
                description: 'Initialize a DataTable with essential features. The DataTable controller provides a powerful wrapper around jQuery DataTables with additional functionality.',
                code: `
const usersDataTable = new $DatatableController('kt_datatable_example_1', {
    // Basic configuration
    lengthMenu: [[5, 10, 20, 50, -1], [5, 10, 20, 50, "All"]],
    order: [[3, 'desc']],
    processing: true,
    serverSide: true,

    // Enable toolbar features
    toggleToolbar: true,
    initColumnVisibility: true,

    // Ajax configuration
    ajax: {
        url: \`\${__API_CFG__.BASE_URL}/api/users\`,
        data: (d) => ({
            ...d,
            status: document.querySelector('select[name="status"]').value,
            role: document.querySelector('select[name="role"]').value
        })
    },

    // Column definitions
    columns: [
        { data: 'id' },
        { data: 'name' },
        { data: 'email' },
        { data: 'created_at' },
        { data: 'status' },
        { data: null }, // For actions column
    ]
});`
            },

            columnDefs: {
                title: 'Column Definitions',
                description: 'Configure how columns are rendered and behave. The DataTable provides several pre-built column types and allows custom rendering.',
                code: `
columnDefs: $DatatableController.generateColumnDefs([
    // Checkbox column for row selection
    {
        targets: [0],
        htmlType: 'selectCheckbox',
        orderable: false
    },

    // Badge styling for status
    {
        targets: [1],
        htmlType: 'badge',
        badgeClass: 'badge-light-primary',
        dataClassName: 'fw-bold'
    },

    // Link column with custom href
    {
        targets: [2],
        htmlType: 'link',
        hrefFunction: (data, type, row) => \`/users/\${row.id}\`,
        dataClassName: 'text-hover-primary'
    },

    // Toggle switch
    {
        targets: [4],
        htmlType: 'toggle',
        checkWhen: (data) => data === 'active',
        uncheckWhen: (data) => data === 'inactive'
    },

    // Actions column with buttons
    {
        targets: [-1],
        htmlType: 'actions',
        className: 'text-end',
        actionButtons: {
            edit: {
                type: 'modal',
                modalTarget: '#edit-modal'
            },
            view: {
                type: 'redirect',
                url: (row) => \`/users/\${row.id}\`
            },
            delete: true
        }
    },

    // Dropdown actions menu
    {
        targets: [-1],
        htmlType: 'dropdownActions',
        actionButtons: {
            edit: {
                type: 'modal',
                modalTarget: '#edit-modal',
                icon: 'bi bi-pencil',
                text: 'Edit User'
            },
            delete: {
                type: 'callback',
                callback: (row) => deleteUser(row.id),
                icon: 'bi bi-trash',
                text: 'Delete User',
                color: 'danger'
            },
            divider1: { divider: true },
            custom: {
                type: 'callback',
                callback: (row) => console.log(row),
                icon: 'bi bi-gear',
                text: 'Custom Action'
            }
        }
    }
])`
            },

            events: {
                title: 'Event Handling',
                description: 'Handle various DataTable events and user interactions.',
                code: `
const usersDataTable = new $DatatableController('kt_datatable_example_1', {
    // ... other options ...

    eventListeners: [
        // Handle delete button click
        {
            event: 'click',
            selector: '.delete-btn',
            handler: function(id, event) {
                this.callCustomFunction(
                    'delete',
                    \`/api/users/\${id}\`,
                    (res) => {
                        SweetAlert.deleteSuccess();
                        this.reload();
                    },
                    (err) => SweetAlert.error('Failed to delete user')
                );
            }
        },

        // Handle toggle switch change
        {
            event: 'click',
            selector: '.status-toggle',
            handler: function(id, event) {
                const toggle = event.target;
                const newStatus = toggle.checked ? 'active' : 'inactive';

                this.callCustomFunction('updateStatus',
                    id,
                    newStatus,
                    (res) => {
                        Toast.success('Status updated successfully');
                        toggle.dataset.currentStatus = newStatus;
                    },
                    (err) => {
                        toggle.checked = !toggle.checked;
                        Toast.error('Failed to update status');
                    }
                );
            }
        }
    ],

    // Custom functions that can be called by event handlers
    customFunctions: {
        delete: async function(endpoint, onSuccess, onError) {
            try {
                const confirmed = await SweetAlert.confirm('Are you sure?');
                if (confirmed) {
                    const response = await HttpRequest.delete(endpoint);
                    onSuccess(response);
                }
            } catch (error) {
                onError(error);
            }
        },

        updateStatus: async function(id, newStatus, onSuccess, onError) {
            try {
                const response = await HttpRequest.put(
                    \`/api/users/\${id}/status\`,
                    { status: newStatus }
                );
                onSuccess(response);
            } catch (error) {
                onError(error);
            }
        }
    }
});`
            },

            customization: {
                title: 'Customization Options',
                description: 'Configure advanced features and customize the DataTable behavior.',
                code: `
const usersDataTable = new $DatatableController('kt_datatable_example_1', {
    // Search configuration
    search: true,
    searchSelector: '[data-table-filter="search"]',
    searchDelay: 500,

    // Filter configuration
    filter: true,
    filterBoxSelector: '.filter-toolbar',
    filterMenuSelector: '#filter-menu',
    filterSelector: '[data-table-filter="filter"]',
    resetFilterSelector: '[data-table-reset="filter"]',
    resetFilter: true,

    // Column visibility
    columnVisibility: true,
    columnVisibilitySelector: '.column-visibility-container',

    // Toolbar configuration
    toggleToolbar: true,
    selectedCountSelector: '[data-table-toggle-select-count="selected_count"]',
    selectedActionSelector: '[data-table-toggle-action-btn="selected_action"]',
    toolbarBaseSelector: '[data-table-toggle-base="base"]',
    toolbarSelectedSelector: '[data-table-toggle-selected="selected"]',

    // Bulk actions
    selectedAction: (selectedIds, callback) => {
        SweetAlert.confirm('Process selected items?').then((result) => {
            if (result.isConfirmed) {
                // Process selected items
                HttpRequest.post('/api/bulk-action', { ids: selectedIds })
                    .then(() => {
                        Toast.success('Bulk action completed');
                        callback(); // Refresh table
                    })
                    .catch(() => Toast.error('Failed to process items'));
            }
        });
    },

    // Cache configuration
    cacheOptions: {
        enabled: true,
        maxAge: 5 * 60 * 1000, // 5 minutes
        maxEntries: 10
    },

    // Callbacks
    callbacks: {
        onSuccess: (data) => {
            console.log('Data loaded:', data);
        },
        onError: (error) => {
            console.error('Error:', error);
        },
        onLoading: () => {
            console.log('Loading data...');
        },
        onCacheHit: (data) => {
            console.log('Data retrieved from cache:', data);
        }
    }
});`
            },

            htmlStructure: {
                title: 'HTML Structure',
                description: 'Required HTML structure for the DataTable using Laravel Blade components and proper layout organization.',
                code: `
{{-- Layout --}}
@extends('dashboard.layout.index')

{{-- Title --}}
@section('title', 'user')

{{-- Toolbar --}}
@section('toolbar')
    @include('dashboard.components.toolbar', [
        'title' => 'Users',
        'currentPage' => 'User Management',
    ])
@endsection

{{-- Columns Configuration --}}
@php
    $columns = ['name', 'email', 'Verified', 'Status', 'Created At', 'Action'];
@endphp

{{-- Main Content --}}
@section('content')
    {{-- DataTable Component --}}
    <x-lebify-table
        id="user-datatable"
        :columns="$columns"
        :create="true"
        :selected="true"
        :filter="false"
        showCheckbox="true"
        :showSearch="false"
        :showColumnVisibility="false"
        columnVisibilityPlacement="bottom-end"
        columnSettingsTitle="Column Settings"
        columnToggles=""
        tableClass="table-class"
        searchPlaceholder="Search..."
        selectedText="Selected"
        selectedActionButtonClass="btn-success"
        selectedActionButtonText="Delete Selected"
        selectedAction="">

        {{-- Filter Options Section --}}
        @section('filter-options')
            {{-- Status Filter --}}
            <label class="form-check form-check-sm form-check-custom form-check-solid">
                <select class="form-select form-select-solid"
                    data-control="select2"
                    data-placeholder="Select Status"
                    name="status">
                    <option></option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </label>

            {{-- Separator --}}
            <div class="separator border-2 my-3"></div>

            {{-- Verification Filters --}}
            <div class="d-flex gap-3 justify-content-between">
                {{-- Verified Filter --}}
                <label class="form-check mt-3 form-check-sm form-check-custom form-check-solid">
                    <input type="checkbox" class="form-check-input" name="verified" value="verified">
                    <span class="form-check-label">Verified</span>
                </label>

                {{-- Not Verified Filter --}}
                <label class="form-check mt-3 form-check-sm form-check-custom form-check-solid">
                    <input type="checkbox" class="form-check-input" name="not_verified" value="not_verified">
                    <span class="form-check-label">Not Verified</span>
                </label>
            </div>
        @endsection

    </x-lebify-table>
@endsection

{{-- Modals --}}
{{-- Create Modal --}}
<x-lebify-modal
    modal-id="create-modal"
    size="lg"
    submit-form-id="createForm"
    title="Create">
</x-lebify-modal>

{{-- Edit Modal --}}
<x-lebify-modal
    modal-id="edit-modal"
    size="lg"
    submit-form-id="editForm"
    title="Edit">
</x-lebify-modal>

{{-- Show Modal --}}
<x-lebify-modal
    modal-id="show-modal"
    size="lg"
    :show-submit-button="false"
    title="Show">
</x-lebify-modal>

{{-- Scripts --}}
@push('scripts')
    <script src="{{ asset('js/dashboard/user.js') }}" type="module" defer></script>
@endpush
`.replace(/</g, '&lt;').replace(/>/g, '&gt;')
            },

            bestPractice: {
                title: 'Best Practice Implementation',
                description: 'A well-structured DataTable implementation following best practices, including separation of concerns and proper error handling.',
                code: `
/*=============================================================================
 * User Management DataTable Module
 *============================================================================*/

import { HttpRequest } from '../services/httpRequest.js';
import { DASHBOARD_URL } from '../config/app-config.js';
import { SweetAlert } from '../notifications/sweetAlert.js';
import { $DatatableController } from '../advanced/advanced.js';
import { ModalLoader } from '../advanced/advanced.js';

/*---------------------------------------------------------------------------
 * Utility Functions
 *--------------------------------------------------------------------------*/
const defaultErrorHandler = (err) => console.error('Error:', err);
const reloadDataTable = () => userDataTable.reload();
const buildApiUrl = (path) => \`\${DASHBOARD_URL}/users/\${path}\`;

/*---------------------------------------------------------------------------
 * API Operation Handlers
 *--------------------------------------------------------------------------*/
const apiOperations = {
    _DELETE_: async (endpoint, onSuccess) => {
        try {
            const confirmDelete = await SweetAlert.deleteAction();
            if (confirmDelete) {
                const response = await HttpRequest.del(endpoint);
                onSuccess(response);
            }
        } catch (error) {
            defaultErrorHandler(error);
        }
    },

    _SHOW_: async (id, endpoint) => {
        new ModalLoader({
            modalBodySelector: '#show-modal .modal-body',
            endpoint,
            onError: defaultErrorHandler
        });
    },

    _EDIT_: async (id, endpoint) => {
        new ModalLoader({
            modalBodySelector: '#edit-modal .modal-body',
            endpoint,
            onError: defaultErrorHandler
        });
    },

    _UPDATE_STATUS_: async (endpoint, data, onSuccess) => {
        try {
            const response = await HttpRequest.patch(endpoint, data);
            onSuccess(response);
        } catch (error) {
            defaultErrorHandler(error);
        }
    }
};

/*---------------------------------------------------------------------------
 * Event Handlers
 *--------------------------------------------------------------------------*/
const userActionHandlers = {
    delete: function(id) {
        this.callCustomFunction('_DELETE_',
            buildApiUrl(id),
            (response) => {
                response.risk ?
                    SweetAlert.error() :
                    (SweetAlert.deleteSuccess(), reloadDataTable());
            }
        );
    },

    show: function(id) {
        this.callCustomFunction('_SHOW_', id, buildApiUrl(\`\${id}/show\`));
    },

    edit: function(id) {
        this.callCustomFunction('_EDIT_', id, buildApiUrl(\`\${id}/edit\`));
    },

        status: function (id) {
        this.callCustomFunction('_PATCH_', buildApiUrl(\`\${id}/status\`), (response) => {
            console.log(response);
        });
    }
};

/*---------------------------------------------------------------------------
 * DataTable Configuration
 *--------------------------------------------------------------------------*/
const tableColumns = [
    { data: 'id' },
    { data: 'name' },
    { data: 'email' },
    { data: 'email_verified_at', title: 'Verified' },
    { data: 'status', title: 'Status' },
    { data: 'created_at', title: 'Created At' },
    { data: null }
];

const tableColumnDefinitions = [
    // Select checkbox column
    {
        targets: [0],
        orderable: false,
        htmlType: 'selectCheckbox'
    },

    // Name column with badge
    {
        targets: [1],
        htmlType: 'badge',
        badgeClass: 'badge-light-primary',
        dataClassName: 'fw-bold'
    },

    // Email column as link
    {
        targets: [2],
        htmlType: 'link',
        hrefFunction: (data, type, row) => \`/users/\${row.id}\`,
        dataClassName: 'text-hover-primary'
    },

    // Verified status with custom render
    {
        targets: [3],
        orderable: true,
        customRender: (data) => data ?
            '<span class="badge badge-success">Yes</span>' :
            '<span class="badge badge-danger">No</span>'
    },

    // Status toggle switch
    {
        targets: [4],
        htmlType: 'toggle',
        dataClassName: 'status-toggle',
        checkWhen: (data) => data === 'active',
        uncheckWhen: (data) => data === 'inactive'
    },

    // Actions column with dropdown
    {
        targets: [-1],
        htmlType: 'dropdownActions',
        className: 'text-end',
        actionButtons: {
            edit: {
                type: 'modal',
                modalTarget: '#edit-modal',
                icon: 'bi bi-pencil',
                text: 'Edit User'
            },
            view: {
                type: 'modal',
                modalTarget: '#show-modal',
                icon: 'bi bi-eye',
                text: 'View Details'
            },
            delete: {
                type: 'callback',
                callback: (row) => userActionHandlers.delete(row.id),
                icon: 'bi bi-trash',
                text: 'Delete User',
                color: 'danger'
            }
        }
    }
];

/*---------------------------------------------------------------------------
 * Event Listeners
 *--------------------------------------------------------------------------*/
const uiEventListeners = [
    {
        event: 'click',
        selector: '.delete-btn',
        handler: userActionHandlers.delete
    },
    {
        event: 'click',
        selector: '.btn-show',
        handler: userActionHandlers.show
    },
    {
        event: 'click',
        selector: '.btn-edit',
        handler: userActionHandlers.edit
    },
    {
        event: 'change',
        selector: '.status-toggle',
        handler: userActionHandlers.status
    }
];

/*---------------------------------------------------------------------------
 * DataTable Initialization
 *--------------------------------------------------------------------------*/
export const userDataTable = new $DatatableController('user-datatable', {
    // Basic configuration
    lengthMenu: [[15, 50, 100, 200, -1], [15, 50, 100, 200, 'All']],
    order: [[5, 'desc']], // Sort by created_at by default

    // Ajax configuration
    ajax: {
        url: buildApiUrl('datatable'),
        data: (d) => ({
            ...d,
            status: document.querySelector('select[name="status"]').value,
            verified: document.querySelector('input[name="verified"]').checked,
            role: document.querySelector('select[name="role"]').value
        })
    },

    // Column configuration
    columns: tableColumns,
    columnDefs: $DatatableController.generateColumnDefs(tableColumnDefinitions),

    // Event handling
    customFunctions: apiOperations,
    eventListeners: uiEventListeners,

    // Additional features
    toggleToolbar: true,
    initColumnVisibility: true,

    // Cache configuration
    cacheOptions: {
        enabled: true,
        maxAge: 5 * 60 * 1000, // 5 minutes
        maxEntries: 10
    }
});

// Initialize create modal
new ModalLoader({
    triggerSelector: '.create',
    endpoint: buildApiUrl('create'),
    onError: defaultErrorHandler
});

// Global access for table reload
window.RDT = reloadDataTable;`
            }
        };

        datatableSelector.addEventListener('change', (e) => {
            const selectedSection = datatableContent[e.target.value];
            if (selectedSection) {
                content.innerHTML = `
                    <div class="datatable-section">
                        <h3 class="datatable-section-title">${selectedSection.title}</h3>
                        <p class="datatable-section-description">${selectedSection.description}</p>
                        <div class="datatable-code-block">
                            <div class="datatable-code-header">
                                <span>Example Code</span>
                                <button class="lebify-devTool-copy-btn" onclick="navigator.clipboard.writeText(this.parentElement.nextElementSibling.textContent)">
                                    Copy Code
                                </button>
                            </div>
                            <div class="datatable-code-content">
                                <pre><code class="language-javascript">${selectedSection.code}</code></pre>
                            </div>
                        </div>
                    </div>
                `;

                if (window.Prism) {
                    Prism.highlightAllUnder(content);
                }
            }
        });
    }
}
