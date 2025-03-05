@extends('auth.layout.auth')

@section('title', 'Register')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/auth/barber-register.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endpush

@section('content')
    <div class="barber-register-container">
        <div class="register-wrapper">
            <div class="register-left">
                <div class="register-overlay"></div>
            </div>
            <div class="register-right">
                <div class="register-header">
                    <h1>Create Account</h1>
                    <p>Join our premium barber management system</p>
                </div>

                <form form-id="register" class="register-form" route="{{ route('register') }}"
                    identifier="single-form-post-handler" http-request feedback redirect>
                    @csrf

                    <div class="form-row">
                        <div class="form-group">
                            <label for="name"><i class="fas fa-user"></i> Name</label>
                            <input type="text" placeholder="Enter your name" feedback-id="name-feedback" id="name"
                                name="name" value="{{ old('name') }}" required>
                            <div id="name-feedback" class="invalid-feedback fw-bold"></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email"><i class="fas fa-envelope"></i> Email Address</label>
                        <input type="email" placeholder="Enter your email address" feedback-id="email-feedback"
                            id="email" name="email" value="{{ old('email') }}" required>
                        <div id="email-feedback" class="invalid-feedback fw-bold"></div>
                    </div>

                    <div class="form-group">
                        <label for="phone"><i class="fas fa-phone"></i> Phone Number</label>
                        <input type="tel" placeholder="Enter your phone number" feedback-id="phone-feedback"
                            id="phone_number" name="phone_number" value="{{ old('phone_number') }}" required>
                        <div id="phone-feedback" class="invalid-feedback fw-bold"></div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="password"><i class="fas fa-lock"></i> Password</label>
                            <div class="password-input-wrapper">
                                <input type="password" placeholder="Enter your password" feedback-id="password-feedback"
                                    id="password" name="password" required>
                                <span class="toggle-password" onclick="togglePasswordVisibility('password')">
                                    <i class="fas fa-eye"></i>
                                </span>
                                <div id="password-feedback" class="invalid-feedback fw-bold"></div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation"><i class="fas fa-lock"></i> Confirm Password</label>
                            <div class="password-input-wrapper">
                                <input type="password" placeholder="Confirm your password"
                                    feedback-id="password_confirmation-feedback" id="password_confirmation"
                                    name="password_confirmation" required>
                                <span class="toggle-password" onclick="togglePasswordVisibility('password_confirmation')">
                                    <i class="fas fa-eye"></i>
                                </span>
                                <div id="password_confirmation-feedback" class="invalid-feedback fw-bold"></div>
                            </div>
                        </div>
                    </div>

                    <div class="form-options">
                        <div class="terms-agreement">
                            <input type="checkbox" id="terms" name="terms" required>
                            <label for="terms">I agree to the <a href="#" class="terms-link">Terms &
                                    Conditions</a></label>
                        </div>
                    </div>

                    <button type="submit" submit-form-id="register" loading-text="Creating account..."
                        class="register-button">
                        <span>Create Account</span>
                        <i class="fas fa-arrow-right fs-4 text-white"></i>
                    </button>
                </form>

                <div class="login-link">
                    Already have an account? <a href="{{ route('login') }}">Sign In</a>
                </div>

                <div class="register-footer">
                    <p>&copy; {{ date('Y') }} Premium Barber Management System</p>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function togglePasswordVisibility(inputId) {
                const passwordInput = document.getElementById(inputId);
                const toggleIcon = passwordInput.parentElement.querySelector('.toggle-password i');

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
