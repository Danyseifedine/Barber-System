<form id="edit-businessHour-form" form-id="editForm" http-request route="{{ route('dashboard.businessHours.update') }}"
    identifier="single-form-post-handler" feedback close-modal success-toast on-success="RDT">
    <input type="hidden" name="id" id="id" value="{{ $businessHour->id }}">


    <div class="mb-3">
        <label for="day_of_week" class="form-label">Day of Week</label>
        <input type="text" value="{{ $businessHour->day_of_week }}" feedback-id="day_of_week-feedback"
            class="form-control form-control-solid" name="day_of_week" id="day_of_week">
        <div id="day_of_week-feedback" class="invalid-feedback"></div>
    </div>

    <div class="mb-3">
        <label for="open_time" class="form-label">Open Time</label>
        <input type="time" value="{{ $businessHour->open_time }}" feedback-id="open_time-feedback"
            class="form-control form-control-solid" name="open_time" id="open_time">
    </div>

    <div class="mb-3">
        <label for="close_time" class="form-label">Close Time</label>
        <input type="time" value="{{ $businessHour->close_time }}" feedback-id="close_time-feedback"
            class="form-control form-control-solid" name="close_time" id="close_time">
    </div>

    <div class="mb-3 mt-5">
        <label for="is_closed" class="form-label">Is Closed</label>
        <input type="checkbox" {{ $businessHour->is_closed ? 'checked' : '' }} value="1"
            feedback-id="is_closed-feedback" class="form-check-input form-check-solid mx-3" name="is_closed"
            id="is_closed">
    </div>
</form>
