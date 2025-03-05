<div class="d-flex flex-column">

    {{-- form fields ... --}}

    {{-- example form field --}}
    <div class="mb-3">
        <label class="form-label fw-bold">Created At</label>
        <p class="text-gray-800">{{ $service->created_at->diffForHumans() }}</p>
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold">Description</label>
        <p class="text-gray-800">{{ $service->description }}</p>
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold">Duration Minutes</label>
        <p class="text-gray-800">{{ $service->duration_minutes }}</p>
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold">Price</label>
        <p class="text-gray-800">{{ $service->price }}</p>
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold">Status</label>
        <p class="text-gray-800">{{ $service->status }}</p>
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold">Updated At</label>
        <p class="text-gray-800">{{ $service->updated_at->diffForHumans() }}</p>
    </div>

</div>
