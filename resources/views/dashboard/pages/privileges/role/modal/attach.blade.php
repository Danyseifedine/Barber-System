<div class="mb-3">
    <h2 class="fw-bold">Manage Permissions: {{ $role->name }}</h2>
    <p class="text-muted">Drag and drop permissions between the containers or use the arrows to assign/remove
        permissions.</p>
</div>

<div class="row g-4">
    <!-- Available Permissions -->
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header">
                <h4 class="card-title m-0">
                    <i class="bi bi-shield-lock me-2"></i>Available Permissions
                </h4>
            </div>
            <div class="card-body">
                <div class="d-flex mb-3">
                    <div class="position-relative flex-grow-1">
                        <input type="text" class="form-control form-control-solid ps-10"
                            id="available-permissions-search" placeholder="Search permissions..." autocomplete="off">
                        <span class="position-absolute top-50 translate-middle-y ms-3">
                            <i class="bi bi-search text-gray-500"></i>
                        </span>
                        <span
                            class="position-absolute top-50 end-0 translate-middle-y me-3 cursor-pointer search-clear">
                            <i class="bi bi-x-lg text-gray-500"></i>
                        </span>
                    </div>
                </div>
                <div class="permissions-container available-permissions" data-container="available"
                    style="min-height: 300px; max-height: 300px; overflow-y: auto;">
                    @foreach ($permissions->whereNotIn('id', $role->permissions->pluck('id')) as $permission)
                        <div class="permission-item d-flex align-items-center p-3 border rounded mb-2" draggable="true"
                            data-permission-id="{{ $permission->id }}">
                            <div class="d-flex align-items-center flex-grow-1">
                                <i class="bi bi-grip-vertical me-3 text-gray-500"></i>
                                <div class="permission-content">
                                    <div class="fw-bold">{{ $permission->display_name }}</div>
                                    <div class="text-muted small">{{ $permission->name }}</div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-icon btn-light-primary assign-permission"
                                data-permission-id="{{ $permission->id }}">
                                <i class="bi bi-arrow-right"></i>
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Assigned Permissions -->
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header">
                <h4 class="card-title m-0">
                    <i class="bi bi-shield-check me-2"></i>Assigned Permissions
                </h4>
            </div>
            <div class="card-body">
                <div class="d-flex mb-3">
                    <div class="position-relative flex-grow-1">
                        <input type="text" class="form-control form-control-solid ps-10"
                            id="assigned-permissions-search" placeholder="Search assigned..." autocomplete="off">
                        <span class="position-absolute top-50 translate-middle-y ms-3">
                            <i class="bi bi-search text-gray-500"></i>
                        </span>
                        <span
                            class="position-absolute top-50 end-0 translate-middle-y me-3 cursor-pointer search-clear">
                            <i class="bi bi-x-lg text-gray-500"></i>
                        </span>
                    </div>
                </div>
                <div class="permissions-container assigned-permissions" data-container="assigned"
                    style="min-height: 300px; max-height: 300px; overflow-y: auto;">
                    @foreach ($role->permissions as $permission)
                        <div class="permission-item d-flex align-items-center p-3 border rounded mb-2" draggable="true"
                            data-permission-id="{{ $permission->id }}">
                            <button type="button" class="btn btn-sm btn-icon btn-light-danger remove-permission me-3"
                                data-permission-id="{{ $permission->id }}">
                                <i class="bi bi-arrow-left"></i>
                            </button>
                            <div class="d-flex align-items-center flex-grow-1">
                                <i class="bi bi-grip-vertical me-3 text-gray-500"></i>
                                <div class="permission-content">
                                    <div class="fw-bold">{{ $permission->display_name }}</div>
                                    <div class="text-muted small">{{ $permission->name }}</div>
                                </div>
                            </div>
                            <input type="hidden" name="permissions[]" value="{{ $permission->id }}">
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .permission-item {
        cursor: move;
        /* background: #fff; */
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        z-index: 1;
        opacity: 1;
        transform: translateY(0);
        user-select: none;
    }

    .permission-item.dragging {
        opacity: 0.9;
        transform: scale(1.02);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        z-index: 1000;
    }

    .permission-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .permission-item-transition {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        opacity: 0;
        transform: translateY(-10px);
    }

    .permission-item-show {
        opacity: 1;
        transform: translateY(0);
    }

    .permission-item-hide {
        opacity: 0;
        transform: translateY(-20px);
    }

    /* Improve button interactions */
    .btn-icon {
        transition: all 0.2s ease;
    }

    .btn-icon:hover {
        transform: scale(1.1);
    }

    /* Improve scrollbar appearance */
    .permissions-container::-webkit-scrollbar {
        width: 8px;
    }

    .permissions-container::-webkit-scrollbar-track {
        /* background: #f1f1f1; */
        border-radius: 4px;
    }

    .permissions-container::-webkit-scrollbar-thumb {
        border-radius: 4px;
    }

    /* Add smooth container transitions */
    .permissions-container {
        transition: all 0.3s ease;
        border: 2px solid transparent;
        border-radius: 6px;
        position: relative;
    }

    /* Improve drag visual feedback */
    .permissions-container.drag-over {
        border: 2px dashed #009ef7;
        box-shadow: inset 0 0 10px rgba(0, 158, 247, 0.1);
    }

    .permission-item.processing {
        pointer-events: none;
        opacity: 0.5;
    }

    .search-clear {
        display: none;
        cursor: pointer;
        padding: 5px;
        opacity: 0.7;
        transition: opacity 0.2s ease;
    }

    .search-clear:hover {
        opacity: 1;
        color: #009ef7 !important;
    }

    .form-control:not(:placeholder-shown)+.search-clear {
        display: block;
    }

    .no-results-message {
        border: 1px dashed #ddd;
        border-radius: 6px;
        margin: 10px 0;
        color: #6c757d;
    }
</style>