import { validation } from '../config/app-config.js';

export class ValidationManager {
    clearFeedback(form) {
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        form.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
    }

    handleErrors(errors, form) {
        Object.entries(errors).forEach(([field, messages]) => {
            const input = form.querySelector(`[name="${field}"]`);
            if (!input) return;

            const feedbackId = input.getAttribute(validation.attributes.inputFeedbackId);
            if (!feedbackId) return;

            const feedbackElement = document.getElementById(feedbackId);
            if (!feedbackElement) return;

            input.classList.add('is-invalid');
            feedbackElement.textContent = messages[0];
            feedbackElement.style.display = 'block';
        });
    }
}
