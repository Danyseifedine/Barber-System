<div class="d-flex flex-column">

     {{-- form fields ... --}}

     {{-- example form field --}}
    <div class="mb-3">
        <label class="form-label fw-bold">Created At</label>
        <p class="text-gray-800">{{ $feedback->created_at->diffForHumans() }}</p>
    </div>

</div>