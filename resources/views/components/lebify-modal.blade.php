<div class="modal fade" tabindex="-1" id="{{ $modalId }}" data-bs-backdrop="{{ $backdrop }}">
    <div class="modal-dialog modal-dialog-centered {{ 'modal-' . $size }}">
        <div class="modal-content">
            <div class="modal-header d-flex justify-content-between">
                @if (isset($header))
                    {{ $header }}
                @else
                    <h3 class="modal-title">{{ $title ?? 'Modal Title' }}</h3>
                    @if ($showXButton)
                        <div data-bs-dismiss="modal"
                            class="btn btn-icon btn-sm btn-active-light-primary ms-2 float-end close-modal">
                            <i class="bi bi-x fs-1"></i>
                        </div>
                    @endif
                @endif
            </div>

            <div class="modal-body">
                {{ $slot }}
            </div>

            <div class="modal-footer">
                @if (isset($footer))
                    {{ $footer }}
                @else
                    @if ($showCloseButton)
                        <button type="button" class="btn close-modal btn-light" data-bs-dismiss="modal">Close</button>
                    @endif
                    @if ($showSubmitButton)
                        <button type="submit" loading-text="loading" submit-form-id="{{ $submitFormId }}"
                            with-spinner="true" class="btn btn-custom" id="{{ $submitButtonId }}">
                            <span class="ld-span">{{ $submitButtonText }}</span>
                        </button>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>
