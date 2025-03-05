<div class="d-none" identifier="single-form-post-handler"></div>

<div class="row g-6 g-xl-9">
    <div class="card">
        <div class="card-body">
            <div class="datatable-body mb-5">
                <!-- Search and Tools Section -->
                <div class="d-flex align-items-center position-relative my-1">
                    @if ($showSearch ?? true)
                        <i class="bi bi-search fs-3 position-absolute ms-6"></i>
                        <input type="text" data-table-filter="search"
                            class="form-control form-control-solid form-control form-control-solid-solid w-250px ps-15"
                            placeholder="{{ $searchPlaceholder ?? 'Search...' }}" />
                    @endif
                    <!-- Column Visibility -->
                    @if (isset($columns) && ($showColumnVisibility ?? true))
                        <div class="column-visibility-container ms-5">
                            <button class="btn btn-icon btn-light" type="button" data-kt-menu-trigger="click"
                                data-kt-menu-placement="{{ $columnVisibilityPlacement ?? 'bottom-end' }}">
                                <i class="bi bi-gear fs-2 text-danger"></i>
                            </button>
                            <div class="menu menu-sub menu-sub-dropdown w-250px w-md-300px" data-kt-menu="true">
                                <div class="px-7 py-5">
                                    <div class="fs-5 fw-bolder">{{ $columnSettingsTitle ?? 'Column Settings' }}</div>
                                </div>
                                <div class="separator border-gray-200"></div>
                                <div class="px-7 py-5" id="column-toggles">
                                    @if (isset($columnToggles))
                                        {!! $columnToggles !!}
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                <!-- Toolbar Section -->
                <div class="d-flex filter-toolbar flex-wrap align-items-center gap-2 gap-lg-3">
                    <!-- Filter Button -->
                    @if (isset($filter) && $filter)
                        <a href="#"
                            class="btn btn-sm btn-flex {{ $filterButtonClass ?? 'btn-secondary' }} fw-bold"
                            data-kt-menu-trigger="click"
                            data-kt-menu-placement="{{ $filterPlacement ?? 'bottom-end' }}">
                            <i class="bi bi-funnel fs-2 text-muted p-0"></i>
                        </a>
                        <div class="menu menu-sub menu-sub-dropdown w-250px w-md-300px" data-kt-menu="true"
                            id="filter-menu">
                            <div class="px-7 py-5">
                                <div class="fs-5 text-gray-900 fw-bold">{{ $filterTitle ?? 'Filter Options' }}</div>
                            </div>
                            <div class="separator border-gray-200"></div>
                            <div class="px-7 py-5">
                                <div class="mb-10">
                                    @yield('filter-options')
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button type="reset" class="btn btn-sm btn-light btn-active-light-primary me-2"
                                        data-table-reset="filter" data-kt-menu-dismiss="true">
                                        {{ $resetButtonText ?? 'Reset' }}
                                    </button>
                                    <button type="submit" class="btn btn-sm custom-btn btn-custom"
                                        data-kt-menu-dismiss="true" data-table-filter="filter">
                                        {{ $applyButtonText ?? 'Apply' }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif
                    <!-- Create Button -->
                    @if (isset($create) && $create)
                        <a href="{{ $createRoute ?? '#' }}"
                            class="btn btn-sm create fw-bold {{ $createButtonClass ?? 'btn-custom' }}"
                            @if ($createRoute ?? false) @else
                           data-bs-toggle="modal" data-bs-target="#{{ $createModalId ?? 'create-modal' }}" @endif>
                            {{ $createButtonText ?? 'Create' }}
                        </a>
                    @endif
                    <!-- Custom Toolbar Buttons -->
                    @if (isset($toolbarButtons))
                        <div class="toolbar-buttons">
                            {!! $toolbarButtons !!}
                        </div>
                    @endif
                    <div data-table-toggle-base="base">
                        @if (isset($additionalToolbar))
                            {!! $additionalToolbar !!}
                        @endif
                    </div>
                </div>
                <!-- Selection Toolbar -->
                @if (isset($selected) && $selected)
                    <div class="d-flex justify-content-end align-items-center d-none"
                        data-table-toggle-selected="selected">
                        <div class="fw-bold me-5">
                            <span class="me-2" data-table-toggle-select-count="selected_count"></span>
                            {{ $selectedText ?? 'Selected' }}
                        </div>
                        <button type="button" class="btn {{ $selectedActionButtonClass ?? 'btn-danger' }}"
                            data-table-toggle-action-btn="selected_action">
                            {{ $selectedActionButtonText ?? 'Delete Selected' }}
                        </button>
                    </div>
                @endif
            </div>
            <!-- Table -->
            <table id="{{ $id }}"
                class="table align-middle table-row-dashed fs-6 gy-5 {{ $tableClass ?? '' }}">
                <thead>
                    <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                        @if ($showCheckbox ?? true)
                            <th class="w-10px pe-2">
                                <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                    <input class="form-check-input select-all-checkbox" type="checkbox"
                                        data-kt-check="true"
                                        data-kt-check-target="#{{ $id }} .row-select-checkbox"
                                        value="1" />
                                </div>
                            </th>
                        @endif
                        @foreach ($columns as $column)
                            <th>{{ $column }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="text-gray-600 fw-semibold">
                </tbody>
            </table>
        </div>
    </div>
</div>
