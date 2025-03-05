<div class="d-flex flex-column">

    {{-- form fields ... --}}

    {{-- example form field --}}
    <div class="mb-3">
        <label class="form-label fw-bold">Created At</label>
        <p class="text-gray-800">{{ $appointment->created_at->diffForHumans() }}</p>
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold">Appointment Date</label>
        <p class="text-gray-800">{{ $appointment->appointment_date }}</p>
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold">Start Time</label>
        <p class="text-gray-800">{{ $appointment->start_time }}</p>
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold">Notes</label>
        <p class="text-gray-800">{{ $appointment->notes }}</p>
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold">End Time</label>
        <p class="text-gray-800">{{ $appointment->end_time }}</p>
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold">Status</label>
        <p class="text-gray-800">{{ $appointment->status }}</p>
    </div>


</div>
