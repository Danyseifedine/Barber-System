@extends('auth.layout.auth')

@section('title', 'Login')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/auth/barber-login.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endpush

@section('content')
    <div class="barber-login-container">
        <div class="login-wrapper">
            <div class="login-left">
                <div class="login-overlay"></div>
            </div>
            <div class="login-right">
                <div class="login-header">
                    <h1>Welcome Back</h1>
                    <p>Sign in to continue to your barber dashboard</p>
                </div>

                <form form-id="login" route="{{ route('login') }}" class="login-form" identifier="single-form-post-handler"
                    http-request feedback redirect>
                    @csrf

                    <div class="form-group">
                        <label for="email"><i class="fas fa-envelope"></i> Email</label>
                        <input type="email" id="email" placeholder="Enter your email" feedback-id="email-feedback"
                            name="email" value="{{ old('email') }}" autofocus>
                        <div id="email-feedback" class="invalid-feedback fw-bold"></div>
                    </div>

                    <div class="form-group">
                        <label for="password"><i class="fas fa-lock"></i> Password</label>
                        <div class="password-input-wrapper">
                            <input type="password" id="password" placeholder="Enter your password"
                                feedback-id="password-feedback" name="password" required>
                            <span class="toggle-password" onclick="togglePasswordVisibility()">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>
                        <div id="password-feedback" class="invalid-feedback fw-bold"></div>
                    </div>

                    <div class="form-options">

                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="forgot-password">
                                Forgot Password?
                            </a>
                        @endif
                    </div>

                    <button type="submit" submit-form-id="login" loading-text="Logging in..." class="login-button">
                        <span>Sign In</span>
                        <i class="fas fa-arrow-right fs-4 text-white"></i>
                    </button>
                </form>

                @if (Route::has('register'))
                    <div class="register-link">
                        Don't have an account? <a href="{{ route('register') }}">Sign Up</a>
                    </div>
                @endif

                <div class="login-footer">
                    <p>&copy; {{ date('Y') }} Premium Barber Management System</p>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function togglePasswordVisibility() {
                const passwordInput = document.getElementById('password');
                const toggleIcon = document.querySelector('.toggle-password i');

                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    toggleIcon.classList.remove('fa-eye');
                    toggleIcon.classList.add('fa-eye-slash');
                } else {
                    passwordInput.type = 'password';
                    toggleIcon.classList.remove('fa-eye-slash');
                    toggleIcon.classList.add('fa-eye');
                }
            }
        </script>
    @endpush
@endsection
