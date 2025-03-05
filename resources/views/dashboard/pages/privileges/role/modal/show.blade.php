<div class="d-flex flex-column gap-7 gap-lg-10">
    <!-- Role Details Section -->
    <div class="card card-flush">
        <div class="card-header">
            <div class="card-title">
                <h2>Role Details</h2>
            </div>
        </div>
        <div class="card-body pt-0">
            <div class="row mb-7">
                <label class="col-lg-4 fw-semibold">Display Name</label>
                <div class="col-lg-8">
                    <span class="fw-bold">{{ $role->display_name }}</span>
                </div>
            </div>
            <div class="row mb-7">
                <label class="col-lg-4 fw-semibold">Name</label>
                <div class="col-lg-8">
                    <span class="fw-bold">{{ $role->name }}</span>
                </div>
            </div>
            <div class="row mb-7">
                <label class="col-lg-4 fw-semibold">Description</label>
                <div class="col-lg-8">
                    <span class="fw-bold">{{ $role->description ?: 'No description available' }}</span>
                </div>
            </div>
            <div class="row mb-7">
                <label class="col-lg-4 fw-semibold">Created At</label>
                <div class="col-lg-8">
                    <span class="fw-bold">{{ $role->created_at->format('M d, Y H:i:s') }}</span>
                </div>
            </div>
            <div class="row">
                <label class="col-lg-4 fw-semibold">Last Updated</label>
                <div class="col-lg-8">
                    <span class="fw-bold">{{ $role->updated_at->format('M d, Y H:i:s') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Usage Statistics -->
    <div class="card card-flush">
        <div class="card-header">
            <div class="card-title">
                <h2>Usage Statistics</h2>
            </div>
        </div>
        <div class="card-body pt-0">
            <div class="d-flex flex-wrap">
                <!-- Users Count -->
                <div class="border border-dashed rounded py-3 px-4 me-6 mb-3">
                    <div class="d-flex align-items-center">
                        <div class="symbol symbol-30px me-4">
                            <span class="symbol-label">
                                <i class="bi bi-people fs-2"></i>
                            </span>
                        </div>
                        <div>
                            <div class="fs-2 fw-bold">{{ $role->users_count }}</div>
                            <div class="fw-semibold fs-6 opacity-75">Users</div>
                        </div>
                    </div>
                </div>

                <!-- Permissions Count -->
                <div class="border border-dashed rounded py-3 px-4 mb-3">
                    <div class="d-flex align-items-center">
                        <div class="symbol symbol-30px me-4">
                            <span class="symbol-label">
                                <i class="bi bi-shield-check fs-2"></i>
                            </span>
                        </div>
                        <div>
                            <div class="fs-2 fw-bold">
                                {{ $role->permissions->count() }}</div>
                            <div class="fw-semibold fs-6 opacity-75">Permissions</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Permissions List -->
    <div class="card card-flush">
        <div class="card-header">
            <div class="card-title">
                <h2>Assigned Permissions</h2>
            </div>
            <div class="card-toolbar">
                <span class="badge badge-light-primary">
                    Total: {{ $role->permissions->count() }}
                </span>
            </div>
        </div>
        <div class="card-body pt-0">
            @if ($role->permissions->count() > 0)
                <div class="row g-4">
                    @foreach ($role->permissions as $permission)
                        <div class="col-lg-4">
                            <div class="d-flex align-items-center border rounded-3 p-3">
                                <div class="symbol symbol-35px me-3">
                                    <span class="symbol-label">
                                        <i class="bi bi-shield fs-4"></i>
                                    </span>
                                </div>
                                <div class="d-flex flex-column flex-grow-1 overflow-hidden">
                                    <span class="fw-bold text-truncate">{{ $permission->display_name }}</span>
                                    <span class="opacity-75 fs-7 text-truncate">{{ $permission->name }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="notice d-flex rounded border border-dashed p-6">
                    <i class="bi bi-exclamation-triangle fs-2x me-4"></i>
                    <div class="d-flex flex-stack flex-grow-1">
                        <div>
                            <h4 class="fw-bold">No Permissions Assigned</h4>
                            <div class="fs-6 opacity-75">
                                This role currently has no permissions assigned to it.
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>