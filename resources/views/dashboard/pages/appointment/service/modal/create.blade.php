<form id="create-appointmentService-form" form-id="createForm" http-request
    route="{{ route('dashboard.appointments.services.store') }}" identifier="single-form-post-handler" feedback
    close-modal success-toast on-success="RDT">

    {{-- form fields ... --}}

    {{-- example form field --}}
    <div class="mb-3">
        <label for="appointment_id" class="form-label">Appointment</label>
        <select class="form-control form-control-solid" data-control="select2" feedback-id="appointment_id-feedback"
            name="appointment_id" id="appointment_id">
            <option value="">Select Appointment</option>
            @foreach ($appointments as $appointment)
                <option value="{{ $appointment->id }}">{{ $appointment->appointment_date }}</option>
            @endforeach
        </select>
        <div id="appointment_id-feedback" class="invalid-feedback"></div>
    </div>

    <div class="mb-3">
        <label for="service_id" class="form-label">Service</label>
        <select class="form-control form-control-solid" data-control="select2" feedback-id="service_id-feedback"
            name="service_id" id="service_id">
            <option value="">Select Service</option>
            @foreach ($services as $service)
                <option value="{{ $service->id }}">{{ $service->service_name }}</option>
            @endforeach
        </select>
        <div id="service_id-feedback" class="invalid-feedback"></div>
    </div>

</form>
