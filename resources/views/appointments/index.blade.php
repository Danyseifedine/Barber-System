<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>My Appointments - BSharp Cuts</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    {{-- <link rel="stylesheet" href="{{ asset('css/welcome.css') }}"> --}}
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
            <h1 class="section-title">My Appointments</h1>

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <div class="appointments-tabs">
                <button class="tab-button active" data-tab="upcoming">Upcoming</button>
                <button class="tab-button" data-tab="past">Past</button>
                <button class="tab-button" data-tab="cancelled">Cancelled</button>
            </div>

            <div class="tab-content active" id="upcoming-tab">
                @if ($appointments->where('status', 'scheduled')->where('appointment_date', '>=', now()->format('Y-m-d'))->count() > 0)
                    <div class="appointments-list">
                        @foreach ($appointments->where('status', 'scheduled')->where('appointment_date', '>=', now()->format('Y-m-d'))
                            ->sortBy('appointment_date') as $appointment)
                            <div class="appointment-card">
                                <div class="appointment-header">
                                    <div class="appointment-date">
                                        <span
                                            class="day">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d') }}</span>
                                        <span
                                            class="month">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M') }}</span>
                                    </div>
                                    <div class="appointment-time">
                                        <i class="far fa-clock"></i>
                                        {{ \Carbon\Carbon::parse($appointment->start_time)->format('g:i A') }} -
                                        {{ \Carbon\Carbon::parse($appointment->end_time)->format('g:i A') }}
                                    </div>
                                    <div class="appointment-status status-scheduled">
                                        Scheduled
                                    </div>
                                </div>

                                <div class="appointment-details">
                                    <div class="appointment-services">
                                        <h3>Services</h3>
                                        <ul>
                                            @foreach ($appointment->services as $service)
                                                <li>
                                                    <span class="service-name">{{ $service->service_name }}</span>
                                                    <span
                                                        class="service-price">${{ number_format($service->price, 2) }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>

                                    <div class="appointment-info">
                                        <div class="info-item">
                                            <span class="info-label">Total Duration:</span>
                                            <span class="info-value">{{ $appointment->total_duration }} minutes</span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">Total Price:</span>
                                            <span
                                                class="info-value">${{ number_format($appointment->total_price, 2) }}</span>
                                        </div>
                                        @if ($appointment->notes)
                                            <div class="info-item">
                                                <span class="info-label">Notes:</span>
                                                <span class="info-value">{{ $appointment->notes }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="appointment-actions">
                                    <a href="{{ route('appointments.confirmation', $appointment->id) }}"
                                        class="btn btn-primary">View Details</a>
                                    <form action="{{ route('appointments.cancel', $appointment->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-outline cancel-btn">Cancel</button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="no-appointments">
                        <i class="far fa-calendar-alt"></i>
                        <h3>No Upcoming Appointments</h3>
                        <p>You don't have any upcoming appointments scheduled.</p>
                        <a href="{{ route('home') }}" class="btn btn-primary">Book an Appointment</a>
                    </div>
                @endif
            </div>

            <div class="tab-content" id="past-tab">
                @if (
                    $appointments->where('status', 'completed')->count() > 0 ||
                        $appointments->where('appointment_date', '<', now()->format('Y-m-d'))->where('status', 'scheduled')->count() > 0)
                    <div class="appointments-list">
                        @foreach ($appointments->where(function ($query) {
            return $query->where('status', 'completed')->orWhere(function ($q) {
                return $q->where('appointment_date', '<', now()->format('Y-m-d'))->where('status', 'scheduled');
            });
        })->sortByDesc('appointment_date') as $appointment)
                            <div class="appointment-card">
                                <div class="appointment-header">
                                    <div class="appointment-date">
                                        <span
                                            class="day">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d') }}</span>
                                        <span
                                            class="month">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M') }}</span>
                                    </div>
                                    <div class="appointment-time">
                                        <i class="far fa-clock"></i>
                                        {{ \Carbon\Carbon::parse($appointment->start_time)->format('g:i A') }} -
                                        {{ \Carbon\Carbon::parse($appointment->end_time)->format('g:i A') }}
                                    </div>
                                    <div class="appointment-status status-completed">
                                        {{ $appointment->status === 'completed' ? 'Completed' : 'Past' }}
                                    </div>
                                </div>

                                <div class="appointment-details">
                                    <div class="appointment-services">
                                        <h3>Services</h3>
                                        <ul>
                                            @foreach ($appointment->services as $service)
                                                <li>
                                                    <span class="service-name">{{ $service->service_name }}</span>
                                                    <span
                                                        class="service-price">${{ number_format($service->price, 2) }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>

                                    <div class="appointment-info">
                                        <div class="info-item">
                                            <span class="info-label">Total Duration:</span>
                                            <span class="info-value">{{ $appointment->total_duration }} minutes</span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">Total Price:</span>
                                            <span
                                                class="info-value">${{ number_format($appointment->total_price, 2) }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="appointment-actions">
                                    <a href="{{ route('appointments.confirmation', $appointment->id) }}"
                                        class="btn btn-primary">View Details</a>
                                    <a href="{{ route('home') }}" class="btn btn-outline">Book Again</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="no-appointments">
                        <i class="far fa-calendar-check"></i>
                        <h3>No Past Appointments</h3>
                        <p>You don't have any past appointments.</p>
                        <a href="{{ route('home') }}" class="btn btn-primary">Book an Appointment</a>
                    </div>
                @endif
            </div>

            <div class="tab-content" id="cancelled-tab">
                @if ($appointments->where('status', 'cancelled')->count() > 0)
                    <div class="appointments-list">
                        @foreach ($appointments->where('status', 'cancelled')->sortByDesc('appointment_date') as $appointment)
                            <div class="appointment-card">
                                <div class="appointment-header">
                                    <div class="appointment-date">
                                        <span
                                            class="day">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d') }}</span>
                                        <span
                                            class="month">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M') }}</span>
                                    </div>
                                    <div class="appointment-time">
                                        <i class="far fa-clock"></i>
                                        {{ \Carbon\Carbon::parse($appointment->start_time)->format('g:i A') }} -
                                        {{ \Carbon\Carbon::parse($appointment->end_time)->format('g:i A') }}
                                    </div>
                                    <div class="appointment-status status-cancelled">
                                        Cancelled
                                    </div>
                                </div>

                                <div class="appointment-details">
                                    <div class="appointment-services">
                                        <h3>Services</h3>
                                        <ul>
                                            @foreach ($appointment->services as $service)
                                                <li>
                                                    <span class="service-name">{{ $service->service_name }}</span>
                                                    <span
                                                        class="service-price">${{ number_format($service->price, 2) }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>

                                    <div class="appointment-info">
                                        <div class="info-item">
                                            <span class="info-label">Total Duration:</span>
                                            <span class="info-value">{{ $appointment->total_duration }} minutes</span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">Total Price:</span>
                                            <span
                                                class="info-value">${{ number_format($appointment->total_price, 2) }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="appointment-actions">
                                    <a href="{{ route('home') }}" class="btn btn-primary">Book Again</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="no-appointments">
                        <i class="far fa-calendar-times"></i>
                        <h3>No Cancelled Appointments</h3>
                        <p>You don't have any cancelled appointments.</p>
                        <a href="{{ route('home') }}" class="btn btn-primary">Book an Appointment</a>
                    </div>
                @endif
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
            // Tab functionality
            const tabButtons = document.querySelectorAll('.tab-button');
            const tabContents = document.querySelectorAll('.tab-content');

            tabButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Remove active class from all buttons and contents
                    tabButtons.forEach(btn => btn.classList.remove('active'));
                    tabContents.forEach(content => content.classList.remove('active'));

                    // Add active class to clicked button and corresponding content
                    this.classList.add('active');
                    document.getElementById(`${this.dataset.tab}-tab`).classList.add('active');
                });
            });

            // Alert auto-dismiss
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    setTimeout(() => {
                        alert.style.display = 'none';
                    }, 500);
                }, 5000);
            });
        });
    </script>
</body>

</html>
