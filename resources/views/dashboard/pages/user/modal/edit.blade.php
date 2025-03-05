        <form id="edit-user-form" form-id="editForm" http-request route="{{ route('dashboard.users.update') }}"
    identifier="single-form-post-handler" feedback close-modal success-toast on-success="RDT">
    <input type="hidden" name="id" id="id" value="{{ $user->id }}">

    <div class="mb-3">
        <label for="name" class="form-label">name</label>
        <input type="text" value="{{ $user->name }}" feedback-id="name-feedback" class="form-control form-control-solid"
            name="name" id="name">
        <div id="name-feedback" class="invalid-feedback"></div>
    </div>
    <div class="mb-3">
        <label for="email" class="form-label">email</label>
        <input type="text" value="{{ $user->email }}" feedback-id="email-feedback" class="form-control form-control-solid"
            name="email" id="email">
        <div id="email-feedback" class="invalid-feedback"></div>
    </div>
</form>
