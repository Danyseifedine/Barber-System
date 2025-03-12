export const UTILITY_FUNCTIONS = [
    // Form Functions
    {
        category: 'Form',
        name: 'dispatchFormEvent',
        description: 'Dispatches a custom event on a form element',
        usage: 'dispatchFormEvent(form, eventName, detail)',
        example: `// HTML:
// &lt;form id="myForm"&gt;
//     &lt;input type="text" name="name"&gt;
//     &lt;input type="number" name="age"&gt;
// &lt;/form&gt;

// JavaScript:
// const form = document.getElementById('myForm');
// dispatchFormEvent(form, "submit", { name: "John", age: 30 });

// Listen for the custom event
// form.addEventListener("submit", (e) => {
//    console.log(e.detail); // { name: "John", age: 30 }
// });`,
        params: [{
            name: 'form',
            type: 'HTMLFormElement',
            description: 'The form element to dispatch the event on'
        },
        {
            name: 'eventName',
            type: 'string',
            description: 'Name of the custom event'
        },
        {
            name: 'detail',
            type: 'any',
            description: 'Data to be passed with the event'
        }
        ]
    },
    {
        category: 'Form',
        name: 'serializeFormData',
        description: 'Serializes form data into different formats',
        usage: 'serializeFormData(form, type)',
        example: `// HTML:
// &lt;form id="userForm"&gt;
//     &lt;input type="text" name="name" value="John"&gt;
//     &lt;input type="email" name="email" value="john@example.com"&gt;
// &lt;/form&gt;

// JavaScript:
// const form = document.getElementById('userForm');

// Get as FormData
// const formData = serializeFormData(form, 'formdata');

// Get as JSON object
// const jsonData = serializeFormData(form, 'json');
// Result: { name: "John", email: "john@example.com" }

// Get as URL encoded string
// const urlEncoded = serializeFormData(form, 'urlencoded');
// Result: "name=John&email=john%40example.com"`,
        params: [{
            name: 'form',
            type: 'HTMLFormElement',
            description: 'The form to serialize'
        },
        {
            name: 'type',
            type: "'formdata'|'json'|'urlencoded'",
            description: 'Output format type'
        }
        ]
    },
    {
        category: 'Form',
        name: 'resetFormInputs',
        description: 'Clears all inputs in a form and removes validation states',
        usage: 'resetFormInputs(formSelector)',
        example: `// HTML:
// &lt;form class="myForm"&gt;
//     &lt;input type="text" value="some text"&gt;
//     &lt;textarea&gt;some content&lt;/textarea&gt;
//     &lt;div class="invalid-feedback"&gt;Error message&lt;/div&gt;
// &lt;/form&gt;

// JavaScript:
// Reset all inputs and remove validation states
// resetFormInputs('.myForm');`,
        params: [{
            name: 'formSelector',
            type: 'string',
            description: 'CSS selector for the form'
        }]
    },
    {
        category: 'Form',
        name: 'toggleSubmitButtonOnFormInput',
        description: 'Toggles button disabled state based on whether form has any filled inputs',
        usage: 'toggleSubmitButtonOnFormInput(form, button)',
        example: `// HTML:
// &lt;form id="myForm"&gt;
//     &lt;input type="text" name="name"&gt;
//     &lt;input type="email" name="email"&gt;
//     &lt;textarea name="message"&gt;&lt;/textarea&gt;
//     &lt;button id="submitBtn" class="disabled"&gt;Submit&lt;/button&gt;
// &lt;/form&gt;

// JavaScript:
// const form = document.getElementById('myForm');
// const submitBtn = document.getElementById('submitBtn');

// Initialize the button toggle functionality
// toggleSubmitButtonOnFormInput(form, submitBtn);

// Button will be enabled when any input has a value
// Button will be disabled when all inputs are empty`,
        params: [{
            name: 'form',
            type: 'HTMLFormElement',
            description: 'The form to monitor'
        },
        {
            name: 'button',
            type: 'HTMLButtonElement',
            description: 'The button to toggle'
        }
        ]
    },
    {
        category: 'Random',
        name: 'copyTextToClipboard',
        description: 'Copies text to clipboard and shows a toast notification',
        usage: 'copyTextToClipboard(text, isToasted)',
        example: `// HTML:
// &lt;button onclick="copyTextToClipboard('Hello World!', true)"&gt;
//     Copy Text
// &lt;/button&gt;

// JavaScript:
// Copy with toast notification
// copyTextToClipboard("Hello World!", true);

// Copy without toast notification
// copyTextToClipboard("Hello World!", false);`,
        params: [{
            name: 'text',
            type: 'string',
            description: 'Text to copy to clipboard'
        },
        {
            name: 'isToasted',
            type: 'boolean',
            description: 'Whether to show a toast notification'
        }
        ]
    },
    // Navigation Functions
    {
        category: 'Navigation',
        name: 'goToUrl',
        description: 'Navigates to a URL without page reload using History API',
        usage: 'goToUrl(url)',
        example: `// Navigate without page reload
     goToUrl('/dashboard/users');
     // Navigate with query parameters
     goToUrl('/dashboard/users?sort=name');`,
        params: [{
            name: 'url',
            type: 'string',
            description: 'The URL to navigate to'
        }]
    },
    {
        category: 'Navigation',
        name: 'goToUrlReload',
        description: 'Navigates to a URL with a full page reload',
        usage: 'goToUrlReload(url)',
        example: `// Navigate with page reload
     goToUrlReload('/dashboard/users');
     // Navigate to external URL
     goToUrlReload('https://example.com');`,
        params: [{
            name: 'url',
            type: 'string',
            description: 'The URL to navigate to with page reload'
        }]
    },
    // URL Functions
    {
        category: 'URL',
        name: 'getUrlQueryParameter',
        description: 'Gets a query parameter value from the current URL',
        usage: 'getUrlQueryParameter(name)',
        example: `// URL: https://example.com?name=John&age=30

// Get single parameter
// const name = getUrlQueryParameter('name'); // Returns "John"
// const age = getUrlQueryParameter('age'); // Returns "30"
// const missing = getUrlQueryParameter('notfound'); // Returns null`,
        params: [{
            name: 'name',
            type: 'string',
            description: 'Name of the query parameter'
        }]
    },
    {
        category: 'URL',
        name: 'getUrlParams',
        description: 'Gets all query parameters from the current URL as an object',
        usage: 'getUrlParams()',
        example: `// Current URL: https://example.com/page?name=John&age=25
     const params = getUrlParams();
     // Returns: { name: 'John', age: '25' }
     // Check specific parameter
     const name = params.name; // Returns 'John'`,
        params: []
    },
    {
        category: 'URL',
        name: 'setUrlParams',
        description: 'Updates URL query parameters without page reload',
        usage: 'setUrlParams(params)',
        example: `// Current URL: https://example.com/page?sort=name
     // Add/update parameters
     setUrlParams({
        page: 2,
        sort: 'date'
     });
     // Result: https://example.com/page?sort=date&page=2
     // Remove parameter
     setUrlParams({
        sort: null
     });
     // Result: https://example.com/page?page=2`,
        params: [{
            name: 'params',
            type: 'Object',
            description: 'Object containing parameters to update or remove'
        }]
    },
    {
        category: 'URL',
        name: 'checkUrlEnds',
        description: 'Checks if the current URL path ends with the given path',
        usage: 'checkUrlEnds(path)',
        example: `// Current URL: https://example.com/dashboard/users
     // Check if URL ends with 'users'
     const isUsers = checkUrlEnds('users'); // Returns true
     // Check if URL ends with 'dashboard'
     const isDashboard = checkUrlEnds('dashboard'); // Returns false`,
        params: [{
            name: 'path',
            type: 'string',
            description: 'The path to check against the end of the URL'
        }]
    },
    {
        category: 'URL',
        name: 'checkUrlContains',
        description: 'Checks if the current URL path contains the given path',
        usage: 'checkUrlContains(path)',
        example: `// Current URL: https://example.com/dashboard/users/edit
     // Check if URL contains 'dashboard'
     const hasDashboard = checkUrlContains('dashboard'); // Returns true
     // Check if URL contains 'admin'
     const hasAdmin = checkUrlContains('admin'); // Returns false`,
        params: [{
            name: 'path',
            type: 'string',
            description: 'The path to search for in the URL'
        }]
    },
    {
        category: 'URL',
        name: 'findInUrl',
        description: 'Finds a specific part in the current URL path',
        usage: 'findInUrl(text)',
        example: `// Current URL: https://example.com/dashboard/users/123
     // Find 'users' in URL
     const userPart = findInUrl('users'); // Returns 'users'
     // Find non-existent part
     const adminPart = findInUrl('admin'); // Returns undefined`,
        params: [{
            name: 'text',
            type: 'string',
            description: 'The text to search for in the URL'
        }]
    },
    {
        category: 'URL',
        name: 'findNextInUrl',
        description: 'Finds the URL part that comes after a specific text',
        usage: 'findNextInUrl(text)',
        example: `// Current URL: https://example.com/dashboard/users/123/edit
     // Find part after 'users'
     const id = findNextInUrl('users'); // Returns '123'
     // Find part after last segment
     const last = findNextInUrl('edit'); // Returns undefined`,
        params: [{
            name: 'text',
            type: 'string',
            description: 'The text to find the next part after'
        }]
    },
    {
        category: 'URL',
        name: 'findPrevInUrl',
        description: 'Finds the URL part that comes before a specific text',
        usage: 'findPrevInUrl(text)',
        example: `// Current URL: https://example.com/dashboard/users/123
     // Find part before 'users'
     const section = findPrevInUrl('users'); // Returns 'dashboard'
     // Find part before first segment
     const first = findPrevInUrl('dashboard'); // Returns undefined`,
        params: [{
            name: 'text',
            type: 'string',
            description: 'The text to find the previous part before'
        }]
    },
    {
        category: 'URL',
        name: 'getUrlParts',
        description: 'Gets the current URL path split into parts',
        usage: 'getUrlParts()',
        example: `// Current URL: https://example.com/dashboard/users/123
     const parts = getUrlParts();
     // Returns: ['dashboard', 'users', '123']
     // Get specific part
     const section = parts[0]; // Returns 'dashboard'`,
        params: []
    },
    {
        category: 'URL',
        name: 'checkUrlMatches',
        description: 'Checks if current URL matches a string or regular expression pattern',
        usage: 'checkUrlMatches(pattern)',
        example: `// Current URL: https://example.com/dashboard/users
     // Check with string
     const hasUsers = checkUrlMatches('users'); // Returns true
     // Check with regex
     const isDashboard = checkUrlMatches(/\\/dashboard\\/\\w+/); // Returns true`,
        params: [{
            name: 'pattern',
            type: 'string|RegExp',
            description: 'String or regular expression to match against the URL'
        }]
    },
    {
        category: 'Form',
        name: 'initSelect2',
        description: 'Initializes Select2 on a given selector with a dropdown parent',
        usage: 'initSelect2(selector, watchElement)',
        example: 'initSelect2(".select2-input", ".container")',
        params: [{
            name: 'selector',
            type: 'string',
            description: 'The selector for the element to initialize Select2 on'
        },
        {
            name: 'watchElement',
            type: 'string',
            description: 'The selector for the element that will trigger updates or changes in the Select2 dropdown when modified'
        }]
    }
]


export const CLASSIC_CLASSES = [
    {
        name: '$SingleFormPostController',
        JavaScriptExample: `
        // Example usage of $SingleFormPostController
        const sendEmailConfig = {
            formSelector: '#send-email-form', // Required
            buttonSelector: '#send-email-form button[type="submit"]', // Required
            loadingTemplate: 'version_1', // Required (choose from version_1 to version_11 or provide custom)
            endpoint: 'https://api.example.com/send-email', // Required

            // Optional callbacks
            onLoading: (btn) => {
                btn.disabled = true;
                console.log('Form is loading...');
            },
            onSuccess: (res) => {
                console.log('Email sent successfully!', res);
            },
            onError: (err) => {
                console.error('Error sending email', err);
            },
            onLoaded: (btn) => {
                btn.disabled = false;
                console.log('Loading finished.');
            },

            feedback: true // Optional: Enable feedback for validation errors
        };

        const sendEmail = new $SingleFormPostController(sendEmailConfig);
        sendEmail.init(); // Initialize the form controller
        `,
        BladeExample: `
    <div class="container card mt-12 p-3">
        <form action="" id="classicForm">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" class="form-control form-control-solid" id="name" name="name">
            </div>
            <button class="btn bg-logo d-flex align-items-center justify-content-center gap-2"
                loading="Loading..." with-spinner="true" type="submit">
                <span class="ld-span">Submit</span>
            </button>
        </form>
    </div>
        `,
        params: {
            formSelector: {
                required: true,
                type: 'string',
                description: 'The selector for the form element.'
            },
            buttonSelector: {
                required: true,
                type: 'string',
                description: 'The selector for the submit button element.'
            },
            externalButtonSelector: {
                required: false,
                type: 'string',
                description: 'The selector for the external submit button element.'
            },
            loadingTemplate: {
                required: true,
                type: 'string',
                description: 'HTML template or version string ("version_1" to "version_11") for the loading state.'
            },
            endpoint: {
                required: true,
                type: 'string',
                description: 'The URL to which the form data will be submitted.'
            },
            onLoading: {
                required: false,
                type: 'function',
                description: 'Callback function for handling the loading state.'
            },
            onSuccess: {
                required: false,
                type: 'function',
                description: 'Callback function for handling successful form submission.'
            },
            onError: {
                required: false,
                type: 'function',
                description: 'Callback function for handling errors during form submission.'
            },
            onLoaded: {
                required: false,
                type: 'function',
                description: 'Callback function for handling post-loading state.'
            },
            feedback: {
                required: false,
                type: 'boolean',
                description: 'Flag to enable validation feedback handling (default: false).'
            },
            attribute: {
                required: false,
                type: 'string',
                description: 'The attribute to add to the button element. (default: "loading") loading="Loading..." with-spinner="true" '
            }
        }
    },
    {
        name: '$GetTabController',
        JavaScriptExample: `
// Example usage of $GetTabController
const tabConfig = {
    TabSelector: '.nav-tab', // Required: The selector for the tab elements
    LoadingHtml: 'Please wait...', // Required: HTML to display while loading tab content
    ActiveTabSelector: '.nav-tab.active', // Required: The selector for the active tab
    TabContentSelector: '.tab-content', // Required: The selector for the content container
    endpoint: '/dashboard/tab', // Required: The base URL for fetching tab data

    // Optional callbacks
    onSuccess: (data) => {
        console.log('Tab content loaded successfully!', data);
        document.querySelector('.tab-content').innerHTML = data.html;
    },
    onError: (err) => {
        console.error('Error loading tab content', err);
    },
    onLoading: () => {
        console.log('Loading tab content...');
    },
    onLoad: () => {
        console.log('Tab content loading completed.');
    },
    updateUrl: true // Optional: Enable updating the URL with the active tab
};

const tabController = new $GetTabController(tabConfig);
tabController.initTab(); // Initialize the tab controller
            `,
        BladeExample: `
    <ul class="d-flex align-items-center justify-content-between">
        <div class="nav nav-stretch nav-tabs nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bold">
            <!--begin::Nav item-->
            <li class="nav-item mt-2">
                <a class="nav-link nav-tab text-active-primary ms-0 me-10 py-5" tab-name="tab1"
                    data-bs-toggle="tab">tab1</a>
            </li>
            <!--end::Nav item-->
            <!--begin::Nav item-->
            <li class="nav-item mt-2">
                <a class="nav-link nav-tab text-active-primary ms-0 me-10 py-5" tab-name="tab2"
                    data-bs-toggle="tab">tab2</a>
            </li>
            <!--end::Nav item-->
        </div>
    </ul>

    <div class="tab-content"></div>
            `,
        params: {
            TabSelector: {
                required: true,
                type: 'string',
                description: 'The selector for the tab elements.'
            },
            LoadingHtml: {
                required: true,
                type: 'string',
                description: 'HTML to display while loading tab content.'
            },
            ActiveTabSelector: {
                required: true,
                type: 'string',
                description: 'The selector for the active tab element.'
            },
            TabContentSelector: {
                required: true,
                type: 'string',
                description: 'The selector for the tab content container.'
            },
            endpoint: {
                required: true,
                type: 'string',
                description: 'The base URL for fetching tab data.'
            },
            onSuccess: {
                required: false,
                type: 'function',
                description: 'Callback function for handling successful tab data retrieval.'
            },
            onError: {
                required: false,
                type: 'function',
                description: 'Callback function for handling errors during tab data retrieval.'
            },
            onLoading: {
                required: false,
                type: 'function',
                description: 'Callback function triggered while tab data is loading.'
            },
            onLoad: {
                required: false,
                type: 'function',
                description: 'Callback function triggered after tab data loading is complete.'
            },
            updateUrl: {
                required: false,
                type: 'boolean',
                description: 'Flag to enable updating the URL with the active tab (default: false).'
            }
        }
    },
    {
        name: '$InfiniteLoaderController',
        JavaScriptExample: `
const PostConfig = {
    endpoint: 'dashboard/content',
    scrollHeight: 500,
    params: {},
    headers: {},
    debounceDelay: 1000,
    onSuccess: (res) => {
        console.log(res)
        const container = document.getElementById('post-container');

        const postsArray = Object.values(res);
        postsArray.forEach(post => {
            container.insertAdjacentHTML('beforeend', post.html);
        });
    },
    onError: (res) => {
        console.error(res);
    },
    onLoading: () => {
        const container = document.getElementById('post-container');
        if (!container.querySelector('.loading-indicator')) {
            container.insertAdjacentHTML('beforeend', '<div class="loading-indicator">khaled...</div>');
        }
    },
    onLoaded: () => {
        const loadingIndicator = document.querySelector('.loading-indicator');
        if (loadingIndicator) {
            loadingIndicator.remove();
        }
    },
};

new $InfiniteLoaderController(PostConfig);

            `,

        BladeExample: `
    <div id="scroll-sentinel"></div>

    <div id="post-container" class="container mt-4">
        <!-- Posts will be dynamically loaded here -->
    </div>

    // CONTROLLER ---------------------------------------------------------
            $fakeActivities = [];

        for ($i = 1; $i <= 50; $i++) {
            $fakeActivities[] = [
                'id' => $i,
                'html' => '<div class="post" style="height: 100px;">Fake Activity ' . $i . '</div>'
            ];
        }

        $page = $request->query('page', 1);
        $perPage = 10;
        $initialSkip = 5;
        $skip = ($page - 1) * $perPage + $initialSkip;

        $activities = collect($fakeActivities)->slice($skip, $perPage);

        if ($activities->isEmpty()) {
            return response()->json(['empty' => true]);
        }

        return response()->json($activities);
            `,

        params: {
            endpoint: {
                required: true,
                type: 'string',
                description: 'The URL endpoint for fetching data.'
            },
            scrollHeight: {
                required: false,
                type: 'number',
                description: 'Height from the bottom of the viewport to trigger data fetching (default: 500).'
            },
            debounceDelay: {
                required: false,
                type: 'number',
                description: 'Delay in milliseconds for debouncing fetchData calls (default: 200).'
            },
            initialPage: {
                required: false,
                type: 'number',
                description: 'The starting page for data fetching (default: 1).'
            },
            pageSize: {
                required: false,
                type: 'number',
                description: 'Number of items to fetch per page (default: 20).'
            },
            maxRetries: {
                required: false,
                type: 'number',
                description: 'Maximum number of retry attempts in case of network errors (default: 3).'
            },
            retryDelay: {
                required: false,
                type: 'number',
                description: 'Delay in milliseconds between retries (default: 1000).'
            },
            onSuccess: {
                required: false,
                type: 'function',
                description: 'Callback invoked on successful data fetching.'
            },
            onError: {
                required: false,
                type: 'function',
                description: 'Callback invoked when an error occurs during fetching.'
            },
            onLoading: {
                required: false,
                type: 'function',
                description: 'Callback invoked before fetching starts.'
            },
            onLoaded: {
                required: false,
                type: 'function',
                description: 'Callback invoked after fetching completes.'
            },
            params: {
                required: false,
                type: 'object',
                description: 'Additional query parameters to include in the request.'
            },
            headers: {
                required: false,
                type: 'object',
                description: 'Custom headers to include in the fetch request.'
            }
        }
    },
    {
        name: '$IconStateController',
        JavaScriptExample: `
        // Example usage of $IconStateController
        const likeFormController = new $IconStateController({
            formSelector: '.like-product-form', // CSS selector for target forms
            dataAttribute: 'data-like-product-id', // Data attribute for unique IDs
            endpoint: 'https://example.com/api/like', // API endpoint for submission
            iconSelector: 'i', // CSS selector for the icon element
            beforeClickIcon: 'bi-heart', // Icon class before click
            afterClickIcon: 'bi-heart-fill', // Icon class after click
            increaseCount: true, // Whether to update the count on click
            countSelector: 'like-count', // CSS selector for the count element
            beforeSubmit: (form, dataId) => {
                console.log(\`Submitting form for product ID: \${dataId}\`);
            },
            onSuccess: (response, form, dataId) => {
                console.log(\`Form submitted successfully for product ID: \${dataId}\`, response);
            },
            onError: (error, form, dataId) => {
                console.error(\`Error submitting form for product ID: \${dataId}\`, error);
            },
        });

        likeFormController.initForms();
        `,

        BladeExample: `
    <div class="container mt-4">
        <form class="like-product-form" data-like-product-id="1">
            <button type="submit" style="inset: 0; background-color: transparent; border: none;">
                <i class="bi-heart"></i>
            </button>
            <p class="like-count-1">0</p>
        </form>
    </div>
    `,
        params: {
            formSelector: {
                required: true,
                type: 'string',
                description: 'CSS selector for identifying forms to be handled.'
            },
            dataAttribute: {
                required: true,
                type: 'string',
                description: 'Data attribute used to uniquely identify each form.'
            },
            endpoint: {
                required: true,
                type: 'string',
                description: 'API endpoint where the form data is sent.'
            },
            iconSelector: {
                required: true,
                type: 'string',
                description: 'CSS selector for the icon element inside the form.'
            },
            beforeClickIcon: {
                required: false,
                type: 'string',
                description: 'CSS class applied to the icon before a click.'
            },
            afterClickIcon: {
                required: false,
                type: 'string',
                description: 'CSS class applied to the icon after a click.'
            },
            toggleClass: {
                required: false,
                type: 'string',
                description: 'CSS class toggled on the icon for state changes.'
            },
            increaseCount: {
                required: false,
                type: 'boolean',
                description: 'Indicates whether to update the count element upon icon click.'
            },
            countSelector: {
                required: false,
                type: 'string',
                description: 'CSS class for the count element associated with each form.'
            },
            countChangeAmount: {
                required: false,
                type: 'number',
                description: 'Amount by which the count is increased or decreased (default: 1).'
            },
            beforeSubmit: {
                required: false,
                type: 'function',
                description: 'Callback executed before the form is submitted. Receives `form` and `dataId` as arguments.'
            },
            onSuccess: {
                required: false,
                type: 'function',
                description: 'Callback executed after successful form submission. Receives `response`, `form`, and `dataId` as arguments.'
            },
            onError: {
                required: false,
                type: 'function',
                description: 'Callback executed when an error occurs during form submission. Receives `error`, `form`, and `dataId` as arguments.'
            }
        }
    },
    {
        name: '$ModalLoaderController',
        JavaScriptExample: `
        // Example usage of $ModalLoaderController
        const modalConfig = {
            modalBodySelector: '#details-modal .modal-body', // Required
            endpoint: 'https://api.example.com/details', // Required
            loadingTemplate: 'version_1', // Optional (defaults to skeleton loader)

            // Optional callbacks
            onSuccess: (response) => {
                console.log('Content loaded successfully!', response);
            },
            onError: (error) => {
                console.error('Error loading content:', error);
            },
            onLoading: () => {
                console.log('Loading modal content...');
            },
            onLoaded: () => {
                console.log('Modal content loaded.');
            }
        };

        const modalLoader = new $ModalLoaderController(modalConfig);
        modalLoader.load(itemId); // Load content for specific item
    `,
        BladeExample: `
    <!-- Modal Structure -->
    <div class="modal fade" id="details-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Content will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Trigger Button -->
    <button class="btn btn-primary" onclick="modalLoader.load(1)">
        Show Details
    </button>
    `,
        params: {
            modalBodySelector: {
                required: true,
                type: 'string',
                description: 'CSS selector for the modal body where content will be loaded.'
            },
            endpoint: {
                required: true,
                type: 'string',
                description: 'API endpoint URL for fetching modal content.'
            },
            loadingTemplate: {
                required: false,
                type: 'string',
                description: 'Template version for loading state ("version_1" to "version_11") or custom HTML.'
            },
            onSuccess: {
                required: false,
                type: 'function',
                description: 'Callback function for successful content loading.'
            },
            onError: {
                required: false,
                type: 'function',
                description: 'Callback function for handling loading errors.'
            },
            onLoading: {
                required: false,
                type: 'function',
                description: 'Callback function executed when loading begins.'
            },
            onLoaded: {
                required: false,
                type: 'function',
                description: 'Callback function executed when loading completes.'
            },
            headers: {
                required: false,
                type: 'object',
                description: 'Additional headers to include in the fetch request.'
            }
        }
    },
    {
        name: '$ModalDataDisplayController',
        JavaScriptExample: `
            // Example usage of $ModalDataDisplayController
            const modalDisplayConfig = {
                modalId: 'details-modal', // Required
                endpoint: 'api/users', // Required
                buttonSelector: '.show-details-btn', // Required
                setTimeout: 1000, // Optional: delay in milliseconds

                // Required: Function to render modal content
                renderContent: (data) => {
                    return \`
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">\${data.name}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <p>Email: \${data.email}</p>
                                <p>Role: \${data.role}</p>
                            </div>
                        </div>
                    \`;
                },

                // Optional callbacks
                onSuccess: (data) => {
                    console.log('Data loaded successfully:', data);
                },
                onError: (error) => {
                    console.error('Error loading data:', error);
                },
                onLoading: () => {
                    // Custom loading template
                    return \`
                        <div class="modal-content">
                            <div class="modal-body">
                                <div class="text-center p-5">
                                    <div class="spinner-border text-primary"></div>
                                    <p class="mt-2">Loading details...</p>
                                </div>
                            </div>
                        </div>
                    \`;
                }
            };

            const modalDisplay = new $ModalDataDisplayController(modalDisplayConfig);
        `,
        BladeExample: `
        <!-- Modal Structure -->
        <div class="modal fade" id="details-modal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <!-- Content will be dynamically inserted here -->
            </div>
        </div>

        <!-- Trigger Buttons -->
        <button class="btn btn-primary show-details-btn" data-id="1">
            Show User 1 Details
        </button>
        <button class="btn btn-primary show-details-btn" data-id="2">
            Show User 2 Details
        </button>
        `,
        params: {
            modalId: {
                required: true,
                type: 'string',
                description: 'ID of the modal element where content will be displayed.'
            },
            endpoint: {
                required: true,
                type: 'string',
                description: 'API endpoint URL for fetching data.'
            },
            buttonSelector: {
                required: true,
                type: 'string',
                description: 'CSS selector for buttons that trigger the modal. Buttons should have data-id attribute.'
            },
            renderContent: {
                required: true,
                type: 'function',
                description: 'Function that receives API response data and returns HTML string for modal content.'
            },
            setTimeout: {
                required: false,
                type: 'number',
                description: 'Optional delay (in milliseconds) before fetching data.'
            },
            onSuccess: {
                required: false,
                type: 'function',
                description: 'Callback function executed after successful data fetch. Receives response data as parameter.'
            },
            onError: {
                required: false,
                type: 'function',
                description: 'Callback function executed when an error occurs. Receives error object as parameter.'
            },
            onLoading: {
                required: false,
                type: 'function',
                description: 'Function that returns custom loading HTML template. If not provided, default loading spinner is used.'
            }
        }
    }
];
