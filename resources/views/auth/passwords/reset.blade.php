@extends('auth.layout.auth')

@section('title', 'Reset Password')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/auth/barber-password.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endpush

@section('content')
    <div class="barber-password-container">
        <div class="password-wrapper">
            <div class="password-left">
                <div class="password-overlay"></div>
            </div>
            <div class="password-right">
                <div class="password-header">
                    <h1>Create New Password</h1>
                    <p>Set a new secure password for your account</p>
                </div>

                <div class="password-content">
                    <div class="password-icon">
                        <i class="fas fa-lock fs-1"></i>
                    </div>

                    <form form-id="reset-password" feedback route="{{ route('password.update') }}"
                        identifier="single-form-post-handler" class="password-form" redirect http-request>
                        @csrf

                        <input type="hidden" name="token" value="{{ $token }}">

                        <div class="form-group">
                            <label for="email"><i class="fas fa-envelope"></i> Email Address</label>
                            <input type="email" feedback-id="email-feedback" id="email" name="email"
                                value="{{ $email ?? old('email') }}" required readonly>
                            <div id="email-feedback" class="invalid-feedback"></div>
                        </div>

                        <div class="form-group">
                            <label for="password"><i class="fas fa-lock"></i> New Password</label>
                            <div class="password-input-wrapper">
                                <input type="password" feedback-id="password-feedback" id="password" name="password"
                                    required placeholder="Enter your new password">
                                <span class="toggle-password" onclick="togglePasswordVisibility('password')">
                                    <i class="fas fa-eye"></i>
                                </span>
                            </div>
                            <div id="password-feedback" class="invalid-feedback"></div>
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation"><i class="fas fa-lock"></i> Confirm Password</label>
                            <div class="password-input-wrapper">
                                <input type="password" feedback-id="password_confirmation-feedback"
                                    id="password_confirmation" name="password_confirmation" required
                                    placeholder="Confirm your new password">
                                <span class="toggle-password" onclick="togglePasswordVisibility('password_confirmation')">
                                    <i class="fas fa-eye"></i>
                                </span>
                            </div>
                            <div id="password_confirmation-feedback" class="invalid-feedback"></div>
                        </div>

                        <button type="submit" submit-form-id="reset-password" loading-text="Resetting..."
                            class="reset-button">
                            <i class="fas fa-check-circle fs-4 text-white"></i>
                            <span>Reset Password</span>
                        </button>
                    </form>

                    <div class="back-to-login">
                        <a href="{{ route('login') }}">
                            <i class="fas fa-arrow-left fs-4"></i> Back to Login
                        </a>
                    </div>
                </div>

                <div class="password-footer">
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

            // Handle form validation feedback
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.querySelector('form[form-id="reset-password"]');

                if (form) {
                    form.addEventListener('submit', function(e) {
                        // Clear previous errors
                        document.querySelectorAll('.invalid-feedback').forEach(el => {
                            el.textContent = '';
                            el.classList.remove('show');
                        });

                        // Validate password match
                        const password = document.getElementById('password');
                        const passwordConfirm = document.getElementById('password_confirmation');
                        const passwordFeedback = document.getElementById('password-feedback');
                        const passwordConfirmFeedback = document.getElementById(
                            'password_confirmation-feedback');

                        if (password.value.length < 8) {
                            passwordFeedback.textContent = 'Password must be at least 8 characters';
                            passwordFeedback.classList.add('show');
                            e.preventDefault();
                        }

                        if (password.value !== passwordConfirm.value) {
                            passwordConfirmFeedback.textContent = 'Passwords do not match';
                            passwordConfirmFeedback.classList.add('show');
                            e.preventDefault();
                        }
                    });
                }
            });
        </script>
    @endpush
@endsection
