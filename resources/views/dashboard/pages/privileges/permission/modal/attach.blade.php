<div class="mb-3">
    <h2 class="fw-bold">Manage Roles: {{ $permission->name }}</h2>
    <p class="text-muted">Drag and drop roles between the containers or use the arrows to assign/remove roles.</p>
</div>

<div class="row g-4">
    <!-- Available Roles -->
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header">
                <h4 class="card-title m-0">
                    <i class="bi bi-shield-lock me-2"></i>Available Roles
                </h4>
            </div>
            <div class="card-body">
                <div class="d-flex mb-3">
                    <div class="position-relative flex-grow-1">
                        <input type="text" class="form-control form-control-solid ps-10" id="available-roles-search"
                            placeholder="Search roles..." autocomplete="off">
                        <span class="position-absolute top-50 translate-middle-y ms-3">
                            <i class="bi bi-search text-gray-500"></i>
                        </span>
                        <span
                            class="position-absolute top-50 end-0 translate-middle-y me-3 cursor-pointer search-clear">
                            <i class="bi bi-x-lg text-gray-500"></i>
                        </span>
                    </div>
                </div>
                <div class="roles-container available-roles" data-container="available"
                    style="min-height: 300px; max-height: 300px; overflow-y: auto;">
                    @foreach ($roles->whereNotIn('id', $permission->roles->pluck('id')) as $role)
                        <div class="role-item d-flex align-items-center p-3 border rounded mb-2" draggable="true"
                            data-role-id="{{ $role->id }}">
                            <div class="d-flex align-items-center flex-grow-1">
                                <i class="bi bi-grip-vertical me-3 text-gray-500"></i>
                                <div class="role-content">
                                    <div class="fw-bold">{{ $role->display_name }}</div>
                                    <div class="text-muted small">{{ $role->name }}</div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-icon btn-light-primary assign-role"
                                data-role-id="{{ $role->id }}">
                                <i class="bi bi-arrow-right"></i>
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Assigned Roles -->
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header">
                <h4 class="card-title m-0">
                    <i class="bi bi-shield-check me-2"></i>Assigned Roles
                </h4>
            </div>
            <div class="card-body">
                <div class="d-flex mb-3">
                    <div class="position-relative flex-grow-1">
                        <input type="text" class="form-control form-control-solid ps-10" id="assigned-roles-search"
                            placeholder="Search assigned..." autocomplete="off">
                        <span class="position-absolute top-50 translate-middle-y ms-3">
                            <i class="bi bi-search text-gray-500"></i>
                        </span>
                        <span
                            class="position-absolute top-50 end-0 translate-middle-y me-3 cursor-pointer search-clear">
                            <i class="bi bi-x-lg text-gray-500"></i>
                        </span>
                    </div>
                </div>
                <div class="roles-container assigned-roles" data-container="assigned"
                    style="min-height: 300px; max-height: 300px; overflow-y: auto;">
                    @foreach ($permission->roles as $role)
                        <div class="role-item d-flex align-items-center p-3 border rounded mb-2" draggable="true"
                            data-role-id="{{ $role->id }}">
                            <button type="button" class="btn btn-sm btn-icon btn-light-danger remove-role me-3"
                                data-role-id="{{ $role->id }}">
                                <i class="bi bi-arrow-left"></i>
                            </button>
                            <div class="d-flex align-items-center flex-grow-1">
                                <i class="bi bi-grip-vertical me-3 text-gray-500"></i>
                                <div class="role-content">
                                    <div class="fw-bold">{{ $role->display_name }}</div>
                                    <div class="text-muted small">{{ $role->name }}</div>
                                </div>
                            </div>
                            <input type="hidden" name="roles[]" value="{{ $role->id }}">
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .role-item {
        cursor: move;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        z-index: 1;
        opacity: 1;
        transform: translateY(0);
        user-select: none;
    }

    .role-item.dragging {
        opacity: 0.9;
        transform: scale(1.02);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        z-index: 1000;
    }

    .role-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .role-item-transition {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        opacity: 0;
        transform: translateY(-10px);
    }

    .role-item-show {
        opacity: 1;
        transform: translateY(0);
    }

    .role-item-hide {
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
    .roles-container::-webkit-scrollbar {
        width: 8px;
    }

    .roles-container::-webkit-scrollbar-track {
        border-radius: 4px;
    }

    .roles-container::-webkit-scrollbar-thumb {
        border-radius: 4px;
    }


    /* Add smooth container transitions */
    .roles-container {
        transition: all 0.3s ease;
        border: 2px solid transparent;
        border-radius: 6px;
        position: relative;
    }

    /* Improve drag visual feedback */
    .roles-container.drag-over {
        border: 2px dashed #009ef7;
        box-shadow: inset 0 0 10px rgba(0, 158, 247, 0.1);
    }

    .role-item.processing {
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
