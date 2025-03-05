import { validationHandlerConfig as config } from '../config/app-config.js';

class ValidationHandler {
    constructor() {
        this.init();

        const modalObserver = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.addedNodes.length) {
                    mutation.addedNodes.forEach(node => {
                        if (node.nodeType === 1 && node.querySelector) {
                            const form = document.querySelector(`form[${config.ATTRIBUTES.form.validator}="${config.IDENTIFIER}"]`);
                            if (form) {
                                this.initializeControls();
                            }
                        }
                    });
                }
            });
        });

        modalObserver.observe(document.body, {
            childList: true,
            subtree: true
        });
    }

    init() {
        document.addEventListener('click', this.handleSubmitClick.bind(this), true);
        document.addEventListener('input', this.handleInput.bind(this));
        document.addEventListener('change', this.handleControlChange.bind(this));

        // Initialize controls for pre-checked checkboxes and disable-type containers
        requestAnimationFrame(() => {
            this.initializeControls();
        });
    }

    initializeControls() {
        const controllingElements = document.querySelectorAll(`[${config.ATTRIBUTES.control.controls}]`);
        controllingElements.forEach(element => {
            const controlType = element.getAttribute(config.ATTRIBUTES.control.controlType) || 'visibility';
            const controlsSelector = element.getAttribute(config.ATTRIBUTES.control.controls);
            const containers = document.querySelectorAll(`.${controlsSelector}`);

            containers.forEach(container => {
                if (container && controlType === 'visibility') {
                    container.classList.add('transition-element');
                    container.classList.add('d-none');
                    container.classList.remove('show');
                }
            });

            this.handleControlChange({ target: element });
        });
    }

    handleControlChange(event) {
        const element = event.target;
        const controlsSelector = element.getAttribute(config.ATTRIBUTES.control.controls);
        if (!controlsSelector) return;

        const controlledContainers = document.querySelectorAll(`.${controlsSelector}`);
        if (!controlledContainers.length) return;

        const controlType = element.getAttribute(config.ATTRIBUTES.control.controlType) || 'visibility';
        const controlCondition = element.getAttribute(config.ATTRIBUTES.control.controlCondition) || 'checked';
        const controlValue = element.getAttribute(config.ATTRIBUTES.control.controlValue);

        const isConditionMet = this.checkControlCondition(element, controlCondition, controlValue);

        controlledContainers.forEach(controlledContainer => {
            const controlledInputs = controlledContainer.querySelectorAll(`[${config.ATTRIBUTES.control.controlledBy}="${element.id}"]`);

            if (controlType === 'visibility') {
                if (isConditionMet) {
                    controlledContainer.classList.remove('d-none');
                    setTimeout(() => {
                        controlledContainer.classList.add('show');
                        controlledContainer.style.opacity = '1';
                        controlledContainer.style.transform = 'translateY(0)';
                    }, 10);
                } else {
                    controlledContainer.classList.remove('show');
                    controlledContainer.style.opacity = '0';
                    controlledContainer.style.transform = 'translateY(-10px)';
                    setTimeout(() => {
                        controlledContainer.classList.add('d-none');
                    }, 300);
                }

                controlledInputs.forEach(input => {
                    if (isConditionMet) {
                        input.removeAttribute('disabled');
                    } else {
                        input.setAttribute('disabled', '');
                        this.clearValidation(input);
                    }
                });
            } else if (controlType === 'disable') {
                controlledContainer.classList.add('show');
                controlledContainer.classList.remove('d-none');
                controlledInputs.forEach(input => {
                    if (isConditionMet) {
                        input.removeAttribute('disabled');
                    } else {
                        input.setAttribute('disabled', '');
                        this.clearValidation(input);
                    }
                });
            }
        });
    }

    checkControlCondition(element, condition, expectedValue) {
        switch (condition) {
            case 'checked':
                return element.type === 'checkbox' ? element.checked : false;
            case 'empty':
                return !element.value.trim();
            case 'not-empty':
                return element.value.trim().length > 0;
            case 'equals':
                return element.value === expectedValue;
            case 'not-equals':
                return element.value !== expectedValue;
            default:
                return false;
        }
    }

    handleSubmitClick(event) {
        const submitButton = event.target.closest('button[type="submit"]');
        if (!submitButton) return;

        const formId = submitButton.getAttribute('submit-form-id');
        if (!formId) return;

        const form = document.querySelector(`form[form-id="${formId}"]`);
        if (!form || form.getAttribute(config.ATTRIBUTES.form.validator) !== config.IDENTIFIER) return;

        if (!this.validateForm(form)) {
            event.preventDefault();
            event.stopImmediatePropagation();
            return false;
        }
    }

    handleInput(event) {
        const input = event.target;
        const form = input.closest('form');

        if (form?.getAttribute(config.ATTRIBUTES.form.validator) === config.IDENTIFIER) {
            // For match fields, only validate if both fields have values
            if (input.hasAttribute(config.ATTRIBUTES.input.match)) {
                const targetId = input.getAttribute(config.ATTRIBUTES.input.match);
                const targetInput = document.getElementById(targetId);

                if (targetInput && input.value.trim() && targetInput.value.trim()) {
                    this.validateInput(input);
                } else {
                    this.clearValidation(input);
                }
            } else {
                this.validateInput(input);

                // If this input is being matched by other inputs, validate them too
                const matchingInputs = form.querySelectorAll(`[${config.ATTRIBUTES.input.match}="${input.id}"]`);
                matchingInputs.forEach(matchingInput => {
                    if (matchingInput !== input && matchingInput.value.trim()) {
                        this.validateInput(matchingInput);
                    } else {
                        this.clearValidation(matchingInput);
                    }
                });
            }
        }

        // Check if this input controls other elements
        if (input.hasAttribute(config.ATTRIBUTES.control.controls)) {
            this.handleControlChange(event);
        }
    }

    clearValidation(input) {
        const feedbackId = input.getAttribute(config.ATTRIBUTES.ui.feedbackId);
        const feedbackElement = document.getElementById(feedbackId);
        input.classList.remove(config.CLASSES.invalid);
        if (feedbackElement) {
            feedbackElement.textContent = '';
            feedbackElement.classList.remove(config.CLASSES.visible);
        }
    }

    validateForm(form) {
        let isValid = true;
        const inputs = form.querySelectorAll('input, select, textarea');

        inputs.forEach(input => {
            // For controlled inputs, only validate if their controlling checkbox is checked
            const controlledBy = input.getAttribute(config.ATTRIBUTES.control.controlledBy);
            if (controlledBy) {
                const controllingCheckbox = document.getElementById(controlledBy);
                if (controllingCheckbox && !controllingCheckbox.checked) {
                    return; // Skip validation for this input
                }
            }

            if (!this.validateInput(input)) {
                isValid = false;
            }
        });

        return isValid;
    }

    getLabelName(input) {
        // First try to get the l-name attribute
        const labelName = input.getAttribute(config.ATTRIBUTES.ui.labelName);
        if (labelName) return labelName;

        // Then try to get the name attribute
        const name = input.getAttribute('name');
        if (name) {
            return name
                .split(/[_-]/) // Use regex to split by both '_' and '-'
                .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                .join(' ');
        }

        // If no name attributes are found, try to get label text if there's an associated label
        const id = input.id;
        if (id) {
            const label = document.querySelector(`label[for="${id}"]`);
            if (label && label.textContent) {
                return label.textContent.trim();
            }
        }

        // Finally, try to get placeholder
        const placeholder = input.getAttribute('placeholder');
        if (placeholder) return placeholder;

        // If all else fails, return "This field"
        return 'This field';
    }

    getErrorMessage(input, type, ...args) {
        // First check for custom error message
        const customMessage = input.getAttribute(config.ATTRIBUTES.errorMessages[type]);
        if (customMessage) {
            return customMessage;
        }

        // If no custom message, use default message from config
        const fieldName = this.getLabelName(input);
        return config.MESSAGES[type](fieldName, ...args);
    }

    validateInput(input) {
        const feedbackId = input.getAttribute(config.ATTRIBUTES.ui.feedbackId);
        const feedbackElement = document.getElementById(feedbackId);
        let isValid = true;
        let errorMessage = '';

        // Check if input is controlled by a checkbox
        const controlledBy = input.getAttribute(config.ATTRIBUTES.control.controlledBy);
        if (controlledBy) {
            const controllingCheckbox = document.getElementById(controlledBy);
            // Skip validation if the controlling checkbox is unchecked
            if (controllingCheckbox && !controllingCheckbox.checked) {
                this.clearValidation(input);
                return true;
            }
        }

        // Required validation
        if (input.hasAttribute(config.ATTRIBUTES.input.required)) {
            if (input.type === 'checkbox') {
                if (!input.checked) {
                    isValid = false;
                    errorMessage = this.getErrorMessage(input, 'required');
                }
            } else if (!input.value.trim()) {
                isValid = false;
                errorMessage = this.getErrorMessage(input, 'required');
            }
        }

        // Only proceed with other validations if there's a value and it's not a checkbox
        if (input.type !== 'checkbox' && input.value.trim() && isValid) {
            // Email validation
            if (input.hasAttribute(config.ATTRIBUTES.input.email)) {
                if (!config.PATTERNS.email.test(input.value)) {
                    isValid = false;
                    errorMessage = this.getErrorMessage(input, 'email');
                }
            }

            // Date validation
            if (input.hasAttribute(config.ATTRIBUTES.input.date)) {
                if (!config.PATTERNS.date.test(input.value)) {
                    isValid = false;
                    errorMessage = this.getErrorMessage(input, 'date');
                } else {
                    const date = new Date(input.value);

                    // Min date validation
                    if (input.hasAttribute(config.ATTRIBUTES.input.dateMin)) {
                        const minDateAttr = input.getAttribute(config.ATTRIBUTES.input.dateMin);
                        let minDate;

                        if (minDateAttr === 'today') {
                            minDate = new Date();
                            minDate.setHours(0, 0, 0, 0);
                        } else {
                            minDate = new Date(minDateAttr);
                        }

                        if (date < minDate) {
                            isValid = false;
                            errorMessage = this.getErrorMessage(input, 'dateMin', minDateAttr);
                        }
                    }

                    // Max date validation
                    if (input.hasAttribute(config.ATTRIBUTES.input.dateMax)) {
                        const maxDateAttr = input.getAttribute(config.ATTRIBUTES.input.dateMax);
                        let maxDate;

                        if (maxDateAttr === 'today') {
                            maxDate = new Date();
                            maxDate.setHours(23, 59, 59, 999);
                        } else {
                            maxDate = new Date(maxDateAttr);
                        }

                        if (date > maxDate) {
                            isValid = false;
                            errorMessage = this.getErrorMessage(input, 'dateMax', maxDateAttr);
                        }
                    }

                    // Related date validation
                    if (input.hasAttribute(config.ATTRIBUTES.input.dateRelatesTo)) {
                        const relatedInputId = input.getAttribute(config.ATTRIBUTES.input.dateRelatesTo);
                        const relatedInput = document.getElementById(relatedInputId);

                        if (relatedInput && relatedInput.value) {
                            const relatedDate = new Date(relatedInput.value);
                            const relationType = input.getAttribute(config.ATTRIBUTES.input.dateRelationType);
                            const relatedName = this.getLabelName(relatedInput);

                            if (relationType === 'before') {
                                if (date >= relatedDate) {
                                    isValid = false;
                                    errorMessage = this.getErrorMessage(input, 'dateBefore', relatedName);
                                }
                            } else if (relationType === 'after') {
                                if (date <= relatedDate) {
                                    isValid = false;
                                    errorMessage = this.getErrorMessage(input, 'dateAfter', relatedName);
                                }
                            }
                        }
                    }

                    // Date range validation
                    if (isValid && input.hasAttribute(config.ATTRIBUTES.input.dateRange)) {
                        const range = parseInt(input.getAttribute(config.ATTRIBUTES.input.dateRange)) ||
                            config.DATE_VALIDATION.DEFAULT_RANGE;
                        const rangeType = input.getAttribute(config.ATTRIBUTES.input.dateRangeType) || 'past';

                        if (!this.isWithinDateRange(date, range, rangeType)) {
                            isValid = false;
                            errorMessage = this.getErrorMessage(input, 'dateRange', range, rangeType);
                        }
                    }

                    // Age validation
                    if (isValid && input.hasAttribute(config.ATTRIBUTES.input.dateAge)) {
                        const minAge = parseInt(input.getAttribute(config.ATTRIBUTES.input.dateAge)) || config.DATE_VALIDATION.MIN_AGE;
                        const age = this.calculateAge(date);

                        if (age < minAge) {
                            isValid = false;
                            errorMessage = this.getErrorMessage(input, 'dateAge', minAge);
                        }
                    }

                    // Duration validation
                    if (isValid && input.hasAttribute(config.ATTRIBUTES.input.dateDuration)) {
                        const duration = parseInt(input.getAttribute(config.ATTRIBUTES.input.dateDuration)) || 0;
                        const durationType = input.getAttribute(config.ATTRIBUTES.input.dateDurationType) || 'days';

                        if (!this.isValidDuration(date, duration, durationType)) {
                            isValid = false;
                            errorMessage = this.getErrorMessage(input, 'dateDuration', duration, durationType);
                        }
                    }

                    // Relative period validation
                    if (isValid && input.hasAttribute(config.ATTRIBUTES.input.dateRelative)) {
                        const period = input.getAttribute(config.ATTRIBUTES.input.dateRelative);
                        if (!this.isWithinRelativePeriod(date, period)) {
                            isValid = false;
                            errorMessage = this.getErrorMessage(input, 'dateRelative', period);
                        }
                    }
                }
            }

            // IP address validation
            if (input.hasAttribute(config.ATTRIBUTES.input.ip)) {
                if (!config.PATTERNS.ip.test(input.value)) {
                    isValid = false;
                    errorMessage = this.getErrorMessage(input, 'ip');
                }
            }

            // Color validation
            if (input.hasAttribute(config.ATTRIBUTES.input.color)) {
                if (!config.PATTERNS.color.test(input.value)) {
                    isValid = false;
                    errorMessage = this.getErrorMessage(input, 'color');
                }
            }

            // Numeric validation
            if (input.hasAttribute(config.ATTRIBUTES.input.numeric)) {
                if (!config.PATTERNS.numeric.test(input.value)) {
                    isValid = false;
                    errorMessage = this.getErrorMessage(input, 'numeric');
                }
            }

            // URL validation
            if (input.hasAttribute(config.ATTRIBUTES.input.url)) {
                if (!config.PATTERNS.url.test(input.value)) {
                    isValid = false;
                    errorMessage = this.getErrorMessage(input, 'url');
                }
            }

            // Phone validation
            if (input.hasAttribute(config.ATTRIBUTES.input.tel)) {
                if (!config.PATTERNS.tel.test(input.value)) {
                    isValid = false;
                    errorMessage = this.getErrorMessage(input, 'tel');
                }
            }

            // Min/Max for numbers
            if (input.hasAttribute(config.ATTRIBUTES.input.numeric)) {
                const value = Number(input.value);
                if (input.hasAttribute(config.ATTRIBUTES.input.min)) {
                    const min = Number(input.getAttribute(config.ATTRIBUTES.input.min));
                    if (value < min) {
                        isValid = false;
                        errorMessage = this.getErrorMessage(input, 'min', min);
                    }
                }
                if (input.hasAttribute(config.ATTRIBUTES.input.max)) {
                    const max = Number(input.getAttribute(config.ATTRIBUTES.input.max));
                    if (value > max) {
                        isValid = false;
                        errorMessage = this.getErrorMessage(input, 'max', max);
                    }
                }
            }

            // Length validations
            if (input.hasAttribute(config.ATTRIBUTES.input.minLength)) {
                const minLength = parseInt(input.getAttribute(config.ATTRIBUTES.input.minLength));
                if (input.value.length < minLength) {
                    isValid = false;
                    errorMessage = this.getErrorMessage(input, 'minLength', minLength);
                }
            }
            if (input.hasAttribute(config.ATTRIBUTES.input.maxLength)) {
                const maxLength = parseInt(input.getAttribute(config.ATTRIBUTES.input.maxLength));
                if (input.value.length > maxLength) {
                    isValid = false;
                    errorMessage = this.getErrorMessage(input, 'maxLength', maxLength);
                }
            }

            // Pattern validation
            if (input.hasAttribute(config.ATTRIBUTES.input.pattern)) {
                const pattern = new RegExp(input.getAttribute(config.ATTRIBUTES.input.pattern));
                if (!pattern.test(input.value)) {
                    isValid = false;
                    errorMessage = this.getErrorMessage(input, 'pattern');
                }
            }

            // Match validation
            if (input.hasAttribute(config.ATTRIBUTES.input.match)) {
                const targetId = input.getAttribute(config.ATTRIBUTES.input.match);
                const targetInput = document.getElementById(targetId);

                if (targetInput && targetInput.value.trim()) {
                    if (input.value !== targetInput.value) {
                        isValid = false;
                        const targetName = targetInput.getAttribute(config.ATTRIBUTES.ui.labelName) ||
                            targetInput.getAttribute('name') ||
                            targetId;
                        errorMessage = this.getErrorMessage(input, 'match', targetName);
                    }
                }
            }

            // Unicode validation
            if (input.hasAttribute(config.ATTRIBUTES.input.unicode)) {
                if (!config.PATTERNS.unicode.test(input.value)) {
                    isValid = false;
                    errorMessage = this.getErrorMessage(input, 'unicode');
                }
            }

            // Enhanced profanity check
            if (input.hasAttribute(config.ATTRIBUTES.input.profanity)) {
                if (this.containsProfanity(input.value)) {
                    isValid = false;
                    errorMessage = this.getErrorMessage(input, 'profanity');
                }
            }

            // Rich text/HTML validation
            if (input.hasAttribute(config.ATTRIBUTES.input.richText)) {
                const htmlTags = input.value.match(config.PATTERNS.htmlTags) || [];
                const hasInvalidTags = htmlTags.some(tag =>
                    !config.TEXT_VALIDATION.ALLOWED_HTML_TAGS.includes(tag.toLowerCase())
                );
                if (hasInvalidTags) {
                    isValid = false;
                    errorMessage = this.getErrorMessage(input, 'richText');
                }
            }

            // JSON format validation
            if (input.hasAttribute(config.ATTRIBUTES.input.json)) {
                try {
                    JSON.parse(input.value);
                } catch (e) {
                    isValid = false;
                    errorMessage = this.getErrorMessage(input, 'json');
                }
            }

            // XML format validation
            if (input.hasAttribute(config.ATTRIBUTES.input.xml)) {
                try {
                    new DOMParser().parseFromString(input.value, 'text/xml');
                    const parseError = new DOMParser()
                        .parseFromString(input.value, 'text/xml')
                        .getElementsByTagName('parsererror');
                    if (parseError.length > 0) {
                        throw new Error('Invalid XML');
                    }
                } catch (e) {
                    isValid = false;
                    errorMessage = this.getErrorMessage(input, 'xml');
                }
            }

            // Maximum word count validation
            if (input.hasAttribute(config.ATTRIBUTES.input.maxWords)) {
                const maxWords = parseInt(input.getAttribute(config.ATTRIBUTES.input.maxWords));
                const wordCount = input.value.trim().split(/\s+/).length;
                if (wordCount > maxWords) {
                    console.log(maxWords)
                    isValid = false;
                    errorMessage = this.getErrorMessage(input, 'maxWords', maxWords);
                }
            }

            // Consecutive characters validation
            if (input.hasAttribute(config.ATTRIBUTES.input.noConsecutive)) {
                // Check for consecutive spaces
                if (config.PATTERNS.consecutiveSpaces.test(input.value)) {
                    isValid = false;
                    errorMessage = this.getErrorMessage(input, 'consecutiveSpaces');
                }
                // Check for consecutive characters (excluding spaces)
                else if (config.PATTERNS.consecutiveChars.test(input.value.replace(/\s/g, ''))) {
                    isValid = false;
                    errorMessage = this.getErrorMessage(input, 'consecutiveChars');
                }
            }

            // Domain name validation
            if (input.hasAttribute(config.ATTRIBUTES.input.domain)) {
                if (!config.PATTERNS.domain.test(input.value)) {
                    isValid = false;
                    errorMessage = this.getErrorMessage(input, 'domain');
                }
            }

            // Base64 validation
            if (input.hasAttribute(config.ATTRIBUTES.input.base64)) {
                if (!config.PATTERNS.base64.test(input.value)) {
                    isValid = false;
                    errorMessage = this.getErrorMessage(input, 'base64');
                }
            }

            // Decimal places validation
            if (input.hasAttribute(config.ATTRIBUTES.input.decimal)) {
                const decimalPlaces = input.getAttribute(config.ATTRIBUTES.input.decimal);
                const value = input.value.trim();

                if (value) {  // Only validate if there's a value
                    const pattern = config.PATTERNS.decimal(decimalPlaces);
                    if (!pattern.test(value)) {
                        isValid = false;
                        const labelName = this.getLabelName(input);
                        errorMessage = config.MESSAGES.decimal(labelName, decimalPlaces);
                    }
                }
            }

            // Scientific notation validation
            if (input.hasAttribute(config.ATTRIBUTES.input.scientific)) {
                if (!config.PATTERNS.scientific.test(input.value)) {
                    isValid = false;
                    errorMessage = this.getErrorMessage(input, 'scientific');
                }
            }

            // Percentage validation
            if (input.hasAttribute(config.ATTRIBUTES.input.percentage)) {
                const value = input.value.trim();
                const number = parseInt(value);

                // Check if it's a valid number and within range (0-100)
                if (isNaN(number) || number <= 0 || number > 100) {
                    isValid = false;
                    errorMessage = this.getErrorMessage(input, 'percentage');
                }
            }

            // Roman numeral validation
            if (input.hasAttribute(config.ATTRIBUTES.input.roman)) {
                if (!config.PATTERNS.roman.test(input.value)) {
                    isValid = false;
                    errorMessage = this.getErrorMessage(input, 'roman');
                }
            }
        }

        // File validations
        if (input.type === 'file' && input.files.length > 0) {
            const fieldName = this.getLabelName(input);

            // Maximum number of files
            if (input.hasAttribute(config.ATTRIBUTES.input.maxFiles)) {
                const maxFiles = parseInt(input.getAttribute(config.ATTRIBUTES.input.maxFiles)) || config.FILE_VALIDATION.DEFAULT_MAX_FILES;
                if (input.files.length > maxFiles) {
                    isValid = false;
                    errorMessage = this.getErrorMessage(input, 'maxFiles', maxFiles, fieldName);
                    return;
                }
            }

            // Total size validation
            if (input.hasAttribute(config.ATTRIBUTES.input.totalSize)) {
                const maxTotalSize = parseFloat(input.getAttribute(config.ATTRIBUTES.input.totalSize)) || config.FILE_VALIDATION.DEFAULT_TOTAL_SIZE;
                let totalSize = 0;
                for (let file of input.files) {
                    totalSize += file.size / (1024 * 1024); // Convert to MB
                }
                if (totalSize > maxTotalSize) {
                    isValid = false;
                    errorMessage = this.getErrorMessage(input, 'totalSize', maxTotalSize, fieldName);
                    return;
                }
            }

            // Validate each file
            for (let file of input.files) {
                // File size validation
                if (input.hasAttribute(config.ATTRIBUTES.input.fileSize)) {
                    const maxSize = parseFloat(input.getAttribute(config.ATTRIBUTES.input.fileSize)) || config.FILE_VALIDATION.DEFAULT_MAX_SIZE;
                    const fileSize = file.size / (1024 * 1024); // Convert to MB
                    if (fileSize > maxSize) {
                        isValid = false;
                        errorMessage = this.getErrorMessage(input, 'fileSize', maxSize, fieldName);
                        break;
                    }
                }

                // File type validation
                if (input.hasAttribute(config.ATTRIBUTES.input.fileTypes)) {
                    const allowedTypes = input.getAttribute(config.ATTRIBUTES.input.fileTypes)?.split(',')
                        .map(type => type.trim().toLowerCase()) || [];
                    const fileExtension = file.name.split('.').pop().toLowerCase();

                    if (!allowedTypes.length || !allowedTypes.includes(fileExtension)) {
                        isValid = false;
                        errorMessage = this.getErrorMessage(input, 'fileTypes', allowedTypes.join(', ') || 'No file types specified', fieldName);
                        break;
                    }
                }
            }
        }

        // Update UI
        if (!isValid) {
            input.classList.add(config.CLASSES.invalid);
            if (feedbackElement) {
                feedbackElement.textContent = errorMessage;
                feedbackElement.classList.add(config.CLASSES.visible);
            }
        } else {
            input.classList.remove(config.CLASSES.invalid);
            if (feedbackElement) {
                feedbackElement.textContent = '';
                feedbackElement.classList.remove(config.CLASSES.visible);
            }
        }

        return isValid;
    }


    isInHiddenContainer(input) {
        const container = input.closest(`[controlled-by]`);
        if (!container) return false;

        return !container.classList.contains('show');
    }

    normalizeText(text) {
        const profanityConfig = config.TEXT_VALIDATION.PROFANITY;
        let normalized = text.toLowerCase();

        // Remove all separators
        profanityConfig.SEPARATORS.forEach(sep => {
            normalized = normalized.split(sep).join('');
        });

        // Replace leetspeak and similar characters
        Object.entries(profanityConfig.LEETSPEAK_MAP).forEach(([char, replacements]) => {
            replacements.forEach(replacement => {
                normalized = normalized.split(replacement).join(char);
            });
        });

        return normalized;
    }

    calculateSimilarity(str1, str2) {
        if (str1.length < 2 || str2.length < 2) return 0;

        // Create bigrams for each string
        const getBigrams = str => {
            const bigrams = new Set();
            for (let i = 0; i < str.length - 1; i++) {
                bigrams.add(str.substring(i, i + 2));
            }
            return bigrams;
        };

        const bigrams1 = getBigrams(str1);
        const bigrams2 = getBigrams(str2);

        // Calculate intersection and union
        const intersection = new Set([...bigrams1].filter(x => bigrams2.has(x)));
        const union = new Set([...bigrams1, ...bigrams2]);

        // Calculate Dice's coefficient
        return (2.0 * intersection.size) / (bigrams1.size + bigrams2.size);
    }

    containsProfanity(text) {
        const profanityConfig = config.TEXT_VALIDATION.PROFANITY;
        const normalizedInput = this.normalizeText(text);

        // Check for exact matches after normalization
        const hasExactMatch = profanityConfig.WORD_LIST.some(word =>
            normalizedInput.includes(this.normalizeText(word))
        );

        if (hasExactMatch) return true;

        // Check for similar matches using sliding window
        const words = normalizedInput.split(/\s+/);
        return profanityConfig.WORD_LIST.some(badWord => {
            const normalizedBadWord = this.normalizeText(badWord);

            // Check each word and possible combinations of words
            for (let i = 0; i < words.length; i++) {
                for (let j = i + 1; j <= Math.min(i + 3, words.length); j++) {
                    const phrase = words.slice(i, j).join('');
                    const similarity = this.calculateSimilarity(phrase, normalizedBadWord);

                    if (similarity >= profanityConfig.MIN_SIMILARITY) {
                        return true;
                    }
                }
            }
            return false;
        });
    }

    // New helper methods for date validation
    isWithinDateRange(date, range, type) {
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        const rangeDate = new Date(today);
        if (type === 'past') {
            rangeDate.setDate(today.getDate() - range);
            return date >= rangeDate && date <= today;
        } else {
            rangeDate.setDate(today.getDate() + range);
            return date >= today && date <= rangeDate;
        }
    }

    calculateAge(birthDate) {
        const today = new Date();
        let age = today.getFullYear() - birthDate.getFullYear();
        const monthDiff = today.getMonth() - birthDate.getMonth();

        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }

        return age;
    }

    isValidDuration(date, duration, type) {
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        const difference = Math.abs(date - today);
        const days = difference / (1000 * 60 * 60 * 24);

        switch (type) {
            case 'days':
                return days <= duration;
            case 'weeks':
                return days <= (duration * 7);
            case 'months':
                return days <= (duration * 30);
            default:
                return false;
        }
    }

    isWithinRelativePeriod(date, period) {
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        const endDate = new Date(today);

        switch (period) {
            case 'next-week':
                endDate.setDate(today.getDate() + 7);
                break;
            case 'next-month':
                endDate.setMonth(today.getMonth() + 1);
                break;
            case 'next-quarter':
                endDate.setMonth(today.getMonth() + 3);
                break;
            case 'next-year':
                endDate.setFullYear(today.getFullYear() + 1);
                break;
            default:
                return false;
        }

        return date >= today && date <= endDate;
    }
}

export { ValidationHandler };
