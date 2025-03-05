@extends('auth.layout.auth')

@section('title', 'Verification')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/auth/barber-verify.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endpush

@section('content')
    <div class="barber-verify-container">
        <div class="verify-wrapper">
            <div class="verify-left">
                <div class="verify-overlay"></div>
            </div>
            <div class="verify-right">
                <div class="verify-header">
                    <h1>Verify Your Email</h1>
                    <p>We've sent a verification link to your email address</p>
                </div>

                <div class="verify-content">
                    @if (session('resent'))
                        <div class="alert alert-success">
                            A fresh verification link has been sent to your email address.
                        </div>
                    @endif

                    <div class="verify-icon">
                        <i class="fas fa-envelope-open-text"></i>
                    </div>

                    <p class="verify-message">
                        Before proceeding, please check your email for a verification link.
                        If you did not receive the email, click the button below to request another.
                    </p>

                    <form method="POST" action="{{ route('verification.resend') }}" class="verify-form">
                        @csrf
                        <button type="submit" class="resend-button">
                            <i class="fas fa-paper-plane"></i>
                            <span>Resend Verification Email</span>
                        </button>
                    </form>

                    <div class="logout-container">
                        <form action="{{ route('logout') }}" method="POST" id="logout-form">
                            @csrf
                            <button type="submit" class="logout-button">
                                <i class="fas fa-sign-out-alt"></i>
                                <span>Log Out</span>
                            </button>
                        </form>
                    </div>

                </div>

                <div class="verify-footer">
                    <p>&copy; {{ date('Y') }} Premium Barber Management System</p>
                </div>
            </div>
        </div>
    </div>
@endsection
