<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Appointment Confirmation - BSharp Cuts</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    <link rel="stylesheet" href="{{ asset('css/appointments.css') }}">
    <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
</head>

<body>
    @include('components.navbar')

    <main class="main-content">
        <div class="container">
            <div class="booking-confirmation">
                <div class="confirmation-header">
                    <i class="fas fa-check-circle"></i>
                    <h1>Appointment Confirmed!</h1>
                    <p>Your appointment has been successfully booked. We look forward to seeing you!</p>
                </div>

                <div class="confirmation-details">
                    <h2>Appointment Details</h2>

                    <div class="detail-item">
                        <span class="detail-label">Appointment ID:</span>
                        <span class="detail-value">{{ $appointment->id }}</span>
                    </div>

                    <div class="detail-item">
                        <span class="detail-label">Date:</span>
                        <span
                            class="detail-value">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('l, F j, Y') }}</span>
                    </div>

                    <div class="detail-item">
                        <span class="detail-label">Time:</span>
                        <span
                            class="detail-value">{{ \Carbon\Carbon::parse($appointment->start_time)->format('g:i A') }}
                            - {{ \Carbon\Carbon::parse($appointment->end_time)->format('g:i A') }}</span>
                    </div>

                    <div class="detail-item">
                        <span class="detail-label">Services:</span>
                        <span class="detail-value">
                            <ul class="service-list">
                                @foreach ($appointment->services as $service)
                                    <li>
                                        <span class="service-name">{{ $service->service_name }}</span>
                                        <span class="service-price">${{ number_format($service->price, 2) }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </span>
                    </div>

                    <div class="detail-item">
                        <span class="detail-label">Total Duration:</span>
                        <span class="detail-value">{{ $appointment->total_duration }} minutes</span>
                    </div>

                    <div class="detail-item">
                        <span class="detail-label">Total Price:</span>
                        <span
                            class="detail-value total-price">${{ number_format($appointment->total_price, 2) }}</span>
                    </div>

                    @if ($appointment->notes)
                        <div class="detail-item">
                            <span class="detail-label">Special Requests:</span>
                            <span class="detail-value">{{ $appointment->notes }}</span>
                        </div>
                    @endif
                </div>

                <div class="confirmation-actions">
                    <a href="{{ route('appointments.index') }}" class="btn btn-primary">View My Appointments</a>


                </div>

                <div class="confirmation-info">
                    <h3>Important Information</h3>
                    <ul>
                        <li>Please arrive 10 minutes before your scheduled appointment time.</li>
                        <li>If you need to cancel or reschedule, please do so at least 24 hours in advance.</li>
                        <li>Payment will be collected at the time of service.</li>
                        <li>For any questions, please call us at (123) 456-7890.</li>
                    </ul>
                </div>
            </div>
        </div>
    </main>

    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-logo">
                    <h2><span class="logo-text">B</span><span class="logo-highlight">Sharp</span> <span
                            class="logo-text">Cuts</span></h2>
                    <p>Premium Barber Experience</p>
                </div>

                <div class="footer-links">
                    <div class="footer-column">
                        <h3>Quick Links</h3>
                        <a href="{{ url('/') }}">Home</a>
                        <a href="{{ route('services') }}">Services</a>
                        <a href="{{ url('/') }}#about">About</a>
                        <a href="{{ url('/') }}#contact">Contact</a>
                    </div>

                    <div class="footer-column">
                        <h3>Our Services</h3>
                        <a href="{{ route('services') }}">Haircuts</a>
                        <a href="{{ route('services') }}">Beard Trims</a>
                        <a href="{{ route('services') }}">Hot Towel Shaves</a>
                        <a href="{{ route('services') }}">Hair Styling</a>
                    </div>

                    <div class="footer-column">
                        <h3>Contact Us</h3>
                        <a href="tel:+1234567890"><i class="fas fa-phone"></i> (123) 456-7890</a>
                        <a href="mailto:info@bsharpcuts.com"><i class="fas fa-envelope"></i> info@bsharpcuts.com</a>
                        <a href="#"><i class="fas fa-map-marker-alt"></i> 123 Main St, City</a>
                    </div>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; {{ date('Y') }} BSharp Cuts. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Confirmation message animation
            const confirmationHeader = document.querySelector('.confirmation-header');
            confirmationHeader.style.opacity = '0';
            confirmationHeader.style.transform = 'translateY(20px)';

            setTimeout(() => {
                confirmationHeader.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                confirmationHeader.style.opacity = '1';
                confirmationHeader.style.transform = 'translateY(0)';
            }, 300);
        });
    </script>
</body>

</html>
