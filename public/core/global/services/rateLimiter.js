import { rateLimiter } from '../config/app-config.js';

export class RateLimiter {
    static isLimited(form) {
        const rateLimit = parseInt(form.getAttribute(rateLimiter.attributes.rateLimit) || 0, 10);
        if (rateLimit === 0) return false;

        const lastSubmission = parseInt(form.getAttribute(rateLimiter.attributes.lastSubmission) || '0', 10);
        return Date.now() - lastSubmission < rateLimit;
    }

    static setLimit(form) {
        form.setAttribute(rateLimiter.attributes.lastSubmission, Date.now().toString());
    }
}
