<link rel="stylesheet" href="{{ asset('css/navbar.css') }}">

<header class="header">
    <div class="container header-container">
        <div class="logo">
            <a href="{{ url('/') }}">
                <h1><span class="logo-text">B</span><span class="logo-highlight">Sharp</span> <span
                        class="logo-text">Cuts</span></h1>
            </a>
        </div>
        <div class="nav-links">
            <a href="{{ url('/') }}#about"
                class="{{ request()->is('/') && request()->segment(1) == '' && request()->url() == url('/') . '#about' ? 'active' : '' }}">About</a>
            <a href="{{ route('services') }}" class="{{ request()->routeIs('services') ? 'active' : '' }}">Services</a>
            <a href="{{ url('/') }}#contact"
                class="{{ request()->is('/') && request()->segment(1) == '' && request()->url() == url('/') . '#contact' ? 'active' : '' }}">Contact</a>
        </div>
        <div class="user-actions">
            @auth
                @if (auth()->user()->hasRole('admin'))
                    <a href="{{ route('dashboard.index') }}" class="btn btn-secondary">Dashboard</a>
                @endif
                <a href="{{ route('appointments.index') }}" class="btn btn-secondary">My Appointments</a>
                <a href="{{ route('logout') }}" class="btn btn-primary">Logout</a>
            @else
                <a href="{{ route('login') }}" class="btn btn-outline">Login</a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="btn btn-primary">Register</a>
                @endif
            @endauth
        </div>
        <div class="menu-toggle">
            <i class="fas fa-bars"></i>
        </div>
    </div>
</header>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Mobile menu toggle
        document.querySelector('.menu-toggle').addEventListener('click', function() {
            document.querySelector('.nav-links').classList.toggle('active');
            document.querySelector('.user-actions').classList.toggle('active');
        });

        // Add active class to current page link
        const currentPath = window.location.pathname;
        const currentHash = window.location.hash;
        const navLinks = document.querySelectorAll('.nav-links a');

        navLinks.forEach(link => {
            const linkPath = link.getAttribute('href').split('#')[0];
            const linkHash = link.getAttribute('href').includes('#') ? '#' + link.getAttribute('href')
                .split('#')[1] : '';

            if ((linkPath === currentPath || (currentPath === '/' && linkPath === '/')) &&
                (linkHash === '' || linkHash === currentHash)) {
                link.classList.add('active');
            }
        });
    });
</script>
