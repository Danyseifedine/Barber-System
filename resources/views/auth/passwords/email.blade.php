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
                    <h1>Reset Password</h1>
                    <p>Enter your email to receive a password reset link</p>
                </div>

                <div class="password-content">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="password-icon">
                        <i class="fas fa-key fs-1"></i>
                    </div>

                    <form form-id="send-email" feedback route="{{ route('password.email') }}"
                        identifier="single-form-post-handler" class="password-form" http-request success-toast>
                        @csrf

                        <div class="form-group">
                            <label for="email"><i class="fas fa-envelope"></i> Email Address</label>
                            <input type="email" feedback-id="email-feedback" id="email" name="email"
                                value="{{ old('email') }}" required autofocus placeholder="Enter your registered email">
                            <div id="email-feedback" class="invalid-feedback"></div>
                        </div>

                        <button type="submit" submit-form-id="send-email" loading-text="Sending..." class="reset-button">
                            <i class="fas fa-paper-plane fs-4 text-white"></i>
                            <span>Send Reset Link</span>
                        </button>
                    </form>

                    <div class="back-to-login">
                        <a href="{{ route('login') }}">
                            <i class="fas fa-arrow-left"></i> Back to Login
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
            // Handle form validation feedback
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.querySelector('form[form-id="send-email"]');

                if (form) {
                    form.addEventListener('submit', function(e) {
                        // Clear previous errors
                        document.querySelectorAll('.invalid-feedback').forEach(el => {
                            el.textContent = '';
                            el.classList.remove('show');
                        });

                        // Validate email
                        const email = document.getElementById('email');
                        const emailFeedback = document.getElementById('email-feedback');

                        if (!email.value) {
                            emailFeedback.textContent = 'Email address is required';
                            emailFeedback.classList.add('show');
                            e.preventDefault();
                        } else if (!/^\S+@\S+\.\S+$/.test(email.value)) {
                            emailFeedback.textContent = 'Please enter a valid email address';
                            emailFeedback.classList.add('show');
                            e.preventDefault();
                        }
                    });
                }
            });
        </script>
    @endpush
@endsection
