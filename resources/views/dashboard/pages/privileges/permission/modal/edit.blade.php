<form id="edit-permission-form" form-id="editForm" http-request route="{{ route('dashboard.permissions.update') }}"
    identifier="single-form-post-handler" feedback close-modal success-toast on-success="RDT">
    <input type="hidden" name="id" id="id" value="{{ $permission->id }}">

     {{-- form fields ... --}}

     {{-- example form field --}}
    <div class="mb-3">
        <label for="name" class="form-label">name</label>
        <input type="text" value="{{ $permission->name }}" feedback-id="name-feedback" class="form-control form-control-solid"
            name="name" id="name">
        <div id="name-feedback" class="invalid-feedback"></div>
    </div>

    <div class="mb-3">
        <label for="display_name" class="form-label">Display Name</label>
        <input type="text" value="{{ $permission->display_name }}" feedback-id="display_name-feedback" class="form-control form-control-solid"
            name="display_name" id="display_name">
        <div id="display_name-feedback" class="invalid-feedback"></div>
    </div>

    <div class="mb-3">
        <label for="description" class="form-label">Description</label>
        <textarea type="text" feedback-id="description-feedback" placeholder="Enter description"
            class="form-control form-control-solid" name="description" id="description">{{ $permission->description }}</textarea>
        <div id="description-feedback" class="invalid-feedback"></div>
    </div>
</form>
