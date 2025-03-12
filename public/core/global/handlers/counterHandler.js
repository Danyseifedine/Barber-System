import { Identifiable } from './base/identifiable.js';
import { errorHandler } from '../utils/classes/error-utils.js';
import { env, handlers } from '../config/app-config.js';
import { Documentation } from '../doc/documentation.js';

export class CounterHandler extends Identifiable {
    constructor(identifier) {
        super(identifier);
        this.config = handlers.counterHandler;
        this.init();
    }

    init() {
        this.initializeCounters();
        document.addEventListener('click', this.handleClick.bind(this));
    }

    initializeCounters() {
        const counters = document.querySelectorAll(`[identifier="${this.getIdentifier()}"][${this.config.attributes.id}]`);
        counters.forEach(counterElement => {
            const display = counterElement.querySelector(`[${this.config.attributes.display}]`);
            if (display) {
                const config = this.getCounterConfig(counterElement);
                display.textContent = config.init;
                this.updateButtonStates(counterElement, config.init);
            } else {
                errorHandler.logMissingAttributeError('SimpleCounter', this.config.attributes.display, 'counter', 'as a child element', 'Required to display the counter value');
            }
        });
    }

    handleClick(event) {
        const counterElement = event.target.closest(`[identifier="${this.getIdentifier()}"][${this.config.attributes.id}]`);
        if (!counterElement) return;

        const increment = event.target.closest(`[${this.config.attributes.increment}]`);
        const decrement = event.target.closest(`[${this.config.attributes.decrement}]`);

        if (increment || decrement) {
            this.updateCounter(counterElement, increment ? 'increment' : 'decrement', event.target);
        }
    }

    updateCounter(counterElement, action, button) {
        const config = this.getCounterConfig(counterElement);
        if (!config) return;

        const display = counterElement.querySelector(`[${this.config.attributes.display}]`);
        if (!display) {
            errorHandler.logMissingAttributeError('SimpleCounter', this.config.attributes.display, 'counter', 'as a child element', 'Required to display the counter value');
            return;
        }

        const currentValue = parseInt(display.textContent, 10);

        const customFunctionName = action === 'increment' ? config.onIncrease : config.onDecrease;
        if (customFunctionName && typeof window[customFunctionName] === 'function') {
            window[customFunctionName](button, currentValue, config.min, config.max, (newValue) => {
                this.setCounterValue(counterElement, newValue, config);
            });
        } else {
            this.defaultCounterBehavior(counterElement, currentValue, action, config);
        }
    }

    defaultCounterBehavior(counterElement, currentValue, action, config) {
        const newValue = action === 'increment' ? currentValue + config.step : currentValue - config.step;
        this.setCounterValue(counterElement, newValue, config);
    }

    setCounterValue(counterElement, newValue, config) {
        const display = counterElement.querySelector(`[${this.config.attributes.display}]`);
        if (config.min !== null) newValue = Math.max(newValue, config.min);
        if (config.max !== null) newValue = Math.min(newValue, config.max);
        display.textContent = newValue;
        this.updateButtonStates(counterElement, newValue);
    }

    updateButtonStates(counterElement, value) {
        const config = this.getCounterConfig(counterElement);
        const incrementButton = counterElement.querySelector(`[${this.config.attributes.increment}]`);
        const decrementButton = counterElement.querySelector(`[${this.config.attributes.decrement}]`);

        if (incrementButton) {
            incrementButton.disabled = config.max !== null && value >= config.max;
        }
        if (decrementButton) {
            decrementButton.disabled = config.min !== null && value <= config.min;
        }
    }

    getCounterConfig(element) {
        const config = {};
        for (const [key, attr] of Object.entries(this.config.attributes)) {
            const value = element.getAttribute(attr);
            if (['onIncrease', 'onDecrease'].includes(key)) {
                config[key] = value;
            } else if (value !== null) {
                config[key] = ['id', 'display', 'increment', 'decrement'].includes(key) ? value : Number(value);
            } else {
                config[key] = this.config.defaults[key];
            }
        }

        if (Object.values(config).some(value => typeof value === 'number' && isNaN(value))) {
            errorHandler.logError('SimpleCounter: Invalid configuration values');
            return null;
        }
        return config;
    }

    static documentation() {
        return Documentation.generate(
            'SimpleCounter',
            'The SimpleCounter class provides a versatile and customizable counter functionality for web applications. It supports multiple counters, custom increment/decrement steps, min/max values, and custom event handlers.',
            `// Initialize a CounterHandler
const myCounter = new CounterHandler('counter-handler');

// The counter is now active and will handle all elements with the specified identifier`,
            `<div class="counter-wrapper">
    <div identifier="counter-handler"
         counter-id="unique-counter-1"
         counter-init="5"
         counter-step="1"
         counter-min="0"
         counter-max="10"
         on-increase="customIncreaseFunction"
         on-decrease="customDecreaseFunction">
        <button class="counter-button" counter-dec>-</button>
        <span class="counter-display" counter-display></span>
        <button class="counter-button" counter-inc>+</button>
    </div>
</div>`,
            [
                { name: 'identifier', description: 'Identifies elements managed by this SimpleCounter instance', required: true },
                { name: 'counter-id', description: 'Unique identifier for each counter element', required: true },
                { name: 'counter-init', description: 'Initial value of the counter', required: false, default: '0' },
                { name: 'counter-step', description: 'Step value for incrementing/decrementing', required: false, default: '1' },
                { name: 'counter-min', description: 'Minimum allowed value', required: false, default: 'null' },
                { name: 'counter-max', description: 'Maximum allowed value', required: false, default: 'null' },
                { name: 'counter-display', description: 'Attribute for the display element', required: true },
                { name: 'counter-inc', description: 'Attribute for the increment button', required: true },
                { name: 'counter-dec', description: 'Attribute for the decrement button', required: true },
                { name: 'on-increase', description: 'Name of custom function to call on increase', required: false },
                { name: 'on-decrease', description: 'Name of custom function to call on decrease', required: false }
            ],
            [
                'Automatically initializes all counters with the specified identifier on the page.',
                'Supports multiple independent counters with unique configurations.',
                'Handles increment and decrement actions with customizable step values.',
                'Enforces minimum and maximum values if set.',
                'Uses event delegation for efficient event handling and improved performance.',
                'Provides detailed error logging for missing or invalid attributes.',
                'Allows custom functions to be called on increase and decrease events.',
                'Custom functions have full control over counter value changes and behavior.',
                'Falls back to default behavior if custom functions are not specified.',
                'Automatically disables increment/decrement buttons when min/max values are reached.',
                'Supports responsive design and can be easily styled with CSS.'
            ],
            `// Custom increase function example
function customIncreaseFunction(button, currentValue, minValue, maxValue, updateFunction) {
    console.log('Custom increase function called');
    let newValue = currentValue + 2; // Custom logic: increment by 2
    if (maxValue !== null && newValue > maxValue) {
        newValue = maxValue;
        console.log('Maximum value reached');
    }
    updateFunction(newValue);
}

// Custom decrease function example
function customDecreaseFunction(button, currentValue, minValue, maxValue, updateFunction) {
    console.log('Custom decrease function called');
    let newValue = currentValue - 0.5; // Custom logic: decrement by 0.5
    if (minValue !== null && newValue < minValue) {
        newValue = minValue;
        console.log('Minimum value reached');
    }
    updateFunction(newValue);
// Usage:
// 1. Define these functions in your global scope or as methods of an object
// 2. Set the 'on-increase' and 'on-decrease' attributes in your HTML to the function names
// 3. The SimpleCounter will automatically call these functions when the buttons are clicked`
        );
    }

}

env.isDevelopment && (window.CounterHandler = CounterHandler);
