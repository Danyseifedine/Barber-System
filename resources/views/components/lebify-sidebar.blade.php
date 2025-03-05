@if (isset($item['is_heading']))
    <div class="menu-item pt-5">
        <div class="menu-content">
            <span class="menu-heading fw-bold text-uppercase fs-7">{{ __($item['title']) }}</span>
        </div>
    </div>
@else
    <div data-kt-menu-trigger="click"
        class="menu-item {{ isset($item['submenu']) ? 'menu-accordion' : '' }}
        {{ isset($item['route_in']) && request()->routeIs($item['route_in']) ? 'here show' : '' }}">
        <span class="menu-link">
            @if (isset($item['icon']))
                <span class="menu-icon">
                    <i class="{{ $item['icon'] }}"></i>
                </span>
            @endif
            <span class="menu-title">{{ __($item['title']) }}</span>
            @if (isset($item['submenu']))
                <span class="menu-arrow"></span>
            @endif
        </span>

        @if (isset($item['submenu']))
            <div class="menu-sub menu-sub-accordion">
                @foreach ($item['submenu'] as $submenu)
                    @if (isset($submenu['submenu']))
                        <x-lebify-sidebar :item="$submenu" />
                    @else
                        <div class="menu-item {{ isset($submenu['is_route']) && $submenu['is_route'] && request()->routeIs($submenu['link']) ? 'active' : '' }}">
                            <a class="menu-link"
                                href="{{ isset($submenu['is_route']) && $submenu['is_route'] ? route($submenu['link']) : $submenu['link'] }}">
                                @if (isset($submenu['icon']))
                                    <span class="menu-icon">
                                        <i class="{{ $submenu['icon'] }} icon"></i>
                                    </span>
                                @else
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                @endif
                                <span class="menu-title">{{ __($submenu['title']) }}</span>
                            </a>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif
    </div>
@endif


<style>
    [data-bs-theme="dark"] .menu-item {
        color: red !important;
    }

    [data-bs-theme="dark"] .menu-item.active {
        background-color: rgba(255, 255, 255, 0.1);
        border-radius: 6px;
    }

    [data-bs-theme="dark"] .menu-item.active .menu-title {
        color: #ffffff;
        font-weight: 500;
    }

    [data-bs-theme="dark"] .menu-item.active .menu-icon i {
        color: #ffffff;
    }

    [data-bs-theme="light"] .menu-item.active {
        background-color: rgba(0, 0, 0, 0.05);
        border-radius: 6px;
    }

    [data-bs-theme="light"] .menu-item.active .menu-title {
        color: #009ef7;
        font-weight: 500;
    }

    [data-bs-theme="light"] .menu-item.active .menu-icon i {
        color: #009ef7;
    }

    /* Common hover effects */
    .menu-item.active:hover {
        transition: all 0.3s ease;
    }

    /* Add subtle transition for smooth state changes */
    .menu-item {
        transition: background-color 0.2s ease, color 0.2s ease;
        margin: 0 0.5rem;
        padding: 0.2rem;
    }
</style>
