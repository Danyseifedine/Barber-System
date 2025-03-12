import { sweetAlertConfig, l10n } from '../config/app-config.js';


export class SweetAlert {
    static async deleteSuccess(title, text, options = {}) {
        const defaultOptions = {
            title: title || l10n.getSweetAlertSuccess('delete.title'),
            text: text || l10n.getSweetAlertSuccess('delete.text'),
            icon: 'success',
            timer: sweetAlertConfig.TIMER,
            timerProgressBar: sweetAlertConfig.TIMER_PROGRESS_BAR,
            customClass: {
                title: 'custom-title-class-success-delete',
                text: 'custom-text-class-success-delete',
                confirmButton: 'custom-confirm-button-class-success-delete',
            },
            ...options
        };

        await Swal.fire(defaultOptions);
    }

    static async error(title, text, options = {}) {
        const defaultOptions = {
            title: title || l10n.getSweetAlertError('default.title'),
            text: text || l10n.getSweetAlertError('default.text'),
            icon: 'error',
            timerProgressBar: sweetAlertConfig.TIMER_PROGRESS_BAR,
            timer: sweetAlertConfig.TIMER,
            customClass: {
                title: 'custom-title-class-error',
                text: 'custom-text-class-error',
                confirmButton: 'custom-confirm-button-class-error',
            },
            ...options
        };

        await Swal.fire(defaultOptions);
    }

    static async deleteAction(title = l10n.getSweetAlertConfirm('delete.title'), text = l10n.getSweetAlertConfirm('delete.text'), confirmButtonText = l10n.getSweetAlertButton('delete'), cancelButtonText = l10n.getSweetAlertButton('cancel'), options = {}) {
        const defaultOptions = {
            icon: 'warning',
            html: `<div class="custom-delete-alert">${l10n.getSweetAlertConfirm('delete.text')}</div>`,
            showCancelButton: true,
            confirmButtonText: confirmButtonText,
            cancelButtonText: cancelButtonText,
            customClass: {
                title: 'custom-title-class-delete',
                confirmButton: 'custom-confirm-button-class-delete',
                cancelButton: 'custom-cancel-button-class-delete',
                text: 'custom-text-class-delete'
            },
            reverseButtons: true,
            focusCancel: true,
            allowOutsideClick: false,
            ...options
        };

        const result = await Swal.fire({
            title: title,
            text: text,
            ...defaultOptions
        });

        return result.isConfirmed;
    }
}
