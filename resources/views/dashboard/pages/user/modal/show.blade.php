        <div class="d-flex flex-column">
    <div class="mb-3">
        <label class="form-label fw-bold">Name</label>
        <p class="text-gray-800">{{ $user->name }}</p>
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold">Email</label>
        <p class="text-gray-800">{{ $user->email }}</p>
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold">Created At</label>
        <p class="text-gray-800">{{ $user->created_at->diffForHumans() }}</p>
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold">Status</label>
        <p class="text-gray-800">
            <span class="badge text-white {{ $user->status == 'active' ? 'bg-success' : 'bg-danger' }}">
                {{ ucfirst($user->status) }}
            </span>
        </p>
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold">Email Verified</label>
        <p class="text-gray-800">
            <span class="badge text-white {{ $user->email_verified_at ? 'bg-success' : 'bg-danger' }}">
                {{ $user->email_verified_at ? 'Yes' : 'No' }}
            </span>
        </p>
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold">Last Updated</label>
        <p class="text-gray-800">{{ $user->updated_at->diffForHumans() }}</p>
    </div>
</div>
