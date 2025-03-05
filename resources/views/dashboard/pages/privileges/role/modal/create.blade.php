<form id="create-role-form" form-id="createForm" http-request route="{{ route('dashboard.roles.store') }}"
    identifier="single-form-post-handler" feedback close-modal success-toast on-success="RDT">

    <div class="mb-3">
        <label for="name" class="form-label">name</label>
        <input type="text" feedback-id="name-feedback" d-required placeholder="Enter name"
            class="form-control form-control-solid" name="name" id="name">
        <div id="name-feedback" class="invalid-feedback"></div>
    </div>

    <div class="mb-3">
        <label for="display_name" class="form-label">Display Name</label>
        <input type="text" feedback-id="display_name-feedback" d-required placeholder="Enter display name"
            class="form-control form-control-solid" name="display_name" id="display_name">
        <div id="display_name-feedback" class="invalid-feedback"></div>
    </div>

    <div class="mb-3">
        <label for="description" class="form-label">Description</label>
        <textarea type="text" feedback-id="description-feedback" placeholder="Enter description"
            class="form-control form-control-solid" name="description" id="description"></textarea>
        <div id="description-feedback" class="invalid-feedback"></div>
    </div>

</form>
