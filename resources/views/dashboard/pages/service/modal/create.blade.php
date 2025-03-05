<form id="create-service-form" form-id="createForm" http-request route="{{ route('dashboard.services.store') }}"
    identifier="single-form-post-handler" feedback close-modal success-toast on-success="RDT">

    <div class="mb-3">
        <label for="service_name" class="form-label">Service Name</label>
        <input type="text" feedback-id="service_name-feedback" placeholder="Enter service name"
            class="form-control form-control-solid" name="service_name" id="service_name">
        <div id="service_name-feedback" class="invalid-feedback"></div>
    </div>

    <div class="mb-3">
        <label for="description" class="form-label">Description</label>
        <textarea feedback-id="description-feedback" placeholder="Enter description" class="form-control form-control-solid"
            name="description" id="description"></textarea>
        <div id="description-feedback" class="invalid-feedback"></div>
    </div>

    <div class="mb-3">
        <label for="duration_minutes" class="form-label">Duration Minutes</label>
        <input type="number" feedback-id="duration_minutes-feedback" placeholder="Enter duration minutes"
            class="form-control form-control-solid" name="duration_minutes" id="duration_minutes">
        <div id="duration_minutes-feedback" class="invalid-feedback"></div>
    </div>

    <div class="mb-3">
        <label for="price" class="form-label">Price</label>
        <input type="number" feedback-id="price-feedback" placeholder="Enter price"
            class="form-control form-control-solid" name="price" id="price">
        <div id="price-feedback" class="invalid-feedback"></div>
    </div>
</form>
