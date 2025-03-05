<form id="create-appointment-form" form-id="createForm" http-request route="{{ route('dashboard.appointments.store') }}"
    identifier="single-form-post-handler" feedback close-modal success-toast on-success="RDT">

    {{-- form fields ... --}}

    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="user_id" class="form-label">User</label>
                <select class="form-select form-select-solid" data-control="select2" feedback-id="user_id-feedback"
                    data-placeholder="Select User" name="user_id" id="user_id">
                    <option value="">Select User</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
                <div id="user_id-feedback" class="invalid-feedback"></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="appointment_date" class="form-label">Appointment Date</label>
                <input type="date" class="form-control form-control-solid" placeholder="Enter Appointment Date"
                    feedback-id="appointment_date-feedback" name="appointment_date" id="appointment_date">
                <div id="appointment_date-feedback" class="invalid-feedback"></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="start_time" class="form-label">Start Time</label>
                <input type="time" class="form-control form-control-solid" placeholder="Enter Start Time"
                    feedback-id="start_time-feedback" name="start_time" id="start_time">
                <div id="start_time-feedback" class="invalid-feedback"></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="end_time" class="form-label">End Time</label>
                <input type="time" class="form-control form-control-solid" placeholder="Enter End Time"
                    feedback-id="end_time-feedback" name="end_time" id="end_time">
                <div id="end_time-feedback" class="invalid-feedback"></div>
            </div>
        </div>
    </div>

    <div class="mb-4">
        <label class="form-label">Services</label>
        <div id="services-feedback" class="invalid-feedback mb-2"></div>
        <div class="row g-3">
            @foreach ($services as $service)
                <div class="col-md-6 col-lg-4">
                    <div class="card service-card h-100 cursor-pointer" data-service-id="{{ $service->id }}">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center mb-2">
                                <div class="form-check">
                                    <input class="form-check-input service-checkbox" type="checkbox"
                                        name="service_ids[]" value="{{ $service->id }}"
                                        id="service-{{ $service->id }}">
                                </div>
                                <label for="service-{{ $service->id }}"
                                    class="form-check-label fw-bold ms-2 cursor-pointer">
                                    {{ $service->service_name }}
                                </label>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="badge bg-light-primary text-primary">{{ $service->duration_minutes }}
                                    min</span>
                                <span
                                    class="badge bg-light-success text-success">${{ number_format($service->price, 2) }}</span>
                            </div>
                            <p class="text-muted small mt-2 mb-0">{{ Str::limit($service->description, 50) }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="mb-3">
        <label for="notes" class="form-label">Notes</label>
        <textarea class="form-control form-control-solid" placeholder="Enter Notes" feedback-id="notes-feedback" name="notes"
            id="notes" rows="3"></textarea>
        <div id="notes-feedback" class="invalid-feedback"></div>
    </div>
</form>

<style>
    .service-card {
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }

    .service-card:hover {
        border-color: #e9e9e9;
        box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.1);
    }

    .service-card.selected {
        border-color: #009ef7;
        background-color: #f1faff;
    }

    .cursor-pointer {
        cursor: pointer;
    }
</style>

