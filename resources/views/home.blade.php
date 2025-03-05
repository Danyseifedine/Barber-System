<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Book Your Appointment - BSharp Cuts</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
</head>

<body>
    @include('components.navbar')

    <main class="main-content">
        <div class="container">
            <h1 class="section-title">Book Your Appointment</h1>

            <div class="booking-section">
                <div class="booking-form">
                    <form id="appointment-form" action="{{ route('appointments.store') }}" method="POST">
                        @csrf

                        <div class="service-selection">
                            <h2 class="section-title">Select Service</h2>
                            <div class="service-cards">
                                @if (count($services) > 0)
                                    @foreach ($services as $service)
                                        <div class="service-card" data-service-id="{{ $service->id }}"
                                            data-service-price="{{ $service->price }}"
                                            data-service-duration="{{ $service->duration_minutes }}">
                                            <h3>{{ $service->service_name }}</h3>
                                            <div class="service-price">${{ number_format($service->price, 2) }}</div>
                                            <div class="service-duration">
                                                <i class="far fa-clock"></i> {{ $service->duration_minutes }} min
                                            </div>
                                            <input type="checkbox" name="services[]" value="{{ $service->id }}"
                                                class="service-checkbox" style="display: none;">
                                        </div>
                                    @endforeach
                                @else
                                    <div class="service-card" data-service-id="1" data-service-price="25.00"
                                        data-service-duration="30">
                                        <h3>Classic Haircut</h3>
                                        <div class="service-price">$25.00</div>
                                        <div class="service-duration">
                                            <i class="far fa-clock"></i> 30 min
                                        </div>
                                        <input type="checkbox" name="services[]" value="1" class="service-checkbox"
                                            style="display: none;">
                                    </div>
                                    <div class="service-card" data-service-id="2" data-service-price="15.00"
                                        data-service-duration="20">
                                        <h3>Beard Trim</h3>
                                        <div class="service-price">$15.00</div>
                                        <div class="service-duration">
                                            <i class="far fa-clock"></i> 20 min
                                        </div>
                                        <input type="checkbox" name="services[]" value="2" class="service-checkbox"
                                            style="display: none;">
                                    </div>
                                    <div class="service-card" data-service-id="3" data-service-price="45.00"
                                        data-service-duration="60">
                                        <h3>Premium Package</h3>
                                        <div class="service-price">$45.00</div>
                                        <div class="service-duration">
                                            <i class="far fa-clock"></i> 60 min
                                        </div>
                                        <input type="checkbox" name="services[]" value="3" class="service-checkbox"
                                            style="display: none;">
                                    </div>
                                    <div class="service-card" data-service-id="4" data-service-price="30.00"
                                        data-service-duration="30">
                                        <h3>Hot Towel Shave</h3>
                                        <div class="service-price">$30.00</div>
                                        <div class="service-duration">
                                            <i class="far fa-clock"></i> 30 min
                                        </div>
                                        <input type="checkbox" name="services[]" value="4" class="service-checkbox"
                                            style="display: none;">
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="appointment-date">Select Date</label>
                            <input type="date" id="appointment-date" name="appointment_date" class="form-control"
                                required min="{{ date('Y-m-d') }}">
                        </div>

                        <div class="form-group">
                            <label for="appointment-time">Select Time</label>
                            <div class="time-slots">
                                <div class="time-slots-container" id="time-slots-container">
                                    <div class="time-slots-loading">
                                        <i class="fas fa-spinner fa-spin"></i> Loading available times...
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" id="appointment-time" name="appointment_time" required>
                        </div>

                        <div class="form-group">
                            <label for="notes">Special Requests (Optional)</label>
                            <textarea id="notes" name="notes" class="form-control" rows="3"
                                placeholder="Any special requests or notes for your appointment..."></textarea>
                        </div>

                        <div class="booking-summary">
                            <h3>Booking Summary</h3>
                            <div class="summary-item">
                                <span class="summary-label">Selected Services:</span>
                                <span id="selected-services-summary">None</span>
                            </div>
                            <div class="summary-item">
                                <span class="summary-label">Date:</span>
                                <span id="selected-date-summary">Not selected</span>
                            </div>
                            <div class="summary-item">
                                <span class="summary-label">Time:</span>
                                <span id="selected-time-summary">Not selected</span>
                            </div>
                            <div class="summary-item">
                                <span class="summary-label">Total Duration:</span>
                                <span id="total-duration-summary">0 min</span>
                            </div>
                            <div class="summary-item">
                                <span class="summary-label">Total Price:</span>
                                <span id="total-price-summary" class="summary-total">$0.00</span>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block" id="booking-submit-btn">Book
                            Appointment</button>
                    </form>
                </div>

                <div class="booking-info">
                    <h2 class="section-title">Booking Information</h2>

                    <div class="calendar-container">
                        <div class="calendar-header">
                            <div class="calendar-month" id="current-month">June 2023</div>
                            <div class="calendar-nav">
                                <button id="prev-month"><i class="fas fa-chevron-left"></i></button>
                                <button id="next-month"><i class="fas fa-chevron-right"></i></button>
                            </div>
                        </div>

                        <div class="calendar-grid" id="calendar-days">
                            <div class="calendar-day-header">Sun</div>
                            <div class="calendar-day-header">Mon</div>
                            <div class="calendar-day-header">Tue</div>
                            <div class="calendar-day-header">Wed</div>
                            <div class="calendar-day-header">Thu</div>
                            <div class="calendar-day-header">Fri</div>
                            <div class="calendar-day-header">Sat</div>
                            <!-- Calendar days will be generated by JavaScript -->
                        </div>
                    </div>

                    <div class="booking-summary" style="margin-top: 30px;">
                        <h3>Business Hours</h3>
                        <div class="summary-item">
                            <span class="summary-label">Monday - Friday:</span>
                            <span>9:00 AM - 6:00 PM</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Saturday - Sunday:</span>
                            <span>Closed</span>
                        </div>
                    </div>

                    <div class="booking-summary" style="margin-top: 30px;">
                        <h3>Booking Policy</h3>
                        <p>Please arrive 10 minutes before your scheduled appointment time. If you need to cancel or
                            reschedule, please do so at least 24 hours in advance.</p>
                    </div>
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
            // Service selection
            const serviceCards = document.querySelectorAll('.service-card');
            const serviceCheckboxes = document.querySelectorAll('.service-checkbox');

            // Fetch business hours from database
            let businessHours = {
                'Monday': {
                    open: '09:00',
                    close: '18:00',
                    closed: false
                },
                'Tuesday': {
                    open: '09:00',
                    close: '18:00',
                    closed: false
                },
                'Wednesday': {
                    open: '09:00',
                    close: '18:00',
                    closed: false
                },
                'Thursday': {
                    open: '09:00',
                    close: '18:00',
                    closed: false
                },
                'Friday': {
                    open: '09:00',
                    close: '18:00',
                    closed: false
                },
                'Saturday': {
                    open: null,
                    close: null,
                    closed: true
                },
                'Sunday': {
                    open: null,
                    close: null,
                    closed: true
                }
            };

            // Fetch actual business hours from server
            async function fetchBusinessHours() {
                try {
                    const response = await fetch('/api/business-hours');
                    if (response.ok) {
                        const data = await response.json();
                        // Update business hours with data from server
                        data.forEach(day => {
                            const dayName = day.day_of_week;
                            businessHours[dayName] = {
                                open: day.is_closed ? null : day.open_time,
                                close: day.is_closed ? null : day.close_time,
                                closed: day.is_closed
                            };
                        });

                        // Regenerate calendar with updated business hours
                        generateCalendar(currentMonth, currentYear);

                        // Update time slots if date is already selected
                        if (appointmentDateInput.value) {
                            updateAvailableTimeSlots();
                        }
                    }
                } catch (error) {
                    console.error('Error fetching business hours:', error);
                }
            }

            // Call the function to fetch business hours
            fetchBusinessHours();

            // Service selection logic with animation
            serviceCards.forEach(card => {
                card.addEventListener('click', function() {
                    // Add animation class
                    this.classList.add('service-selecting');

                    // Toggle selected state after animation
                    setTimeout(() => {
                        this.classList.toggle('selected');
                        this.classList.remove('service-selecting');

                        // Update checkbox state
                        const checkbox = this.querySelector('.service-checkbox');
                        checkbox.checked = !checkbox.checked;

                        // Update UI
                        updateBookingSummary();
                        updateAvailableTimeSlots();
                    }, 150);
                });
            });

            // Time slot selection with improved availability checking
            const timeSlots = document.querySelectorAll('.time-slot');
            const appointmentTimeInput = document.getElementById('appointment-time');

            timeSlots.forEach(slot => {
                slot.addEventListener('click', function() {
                    if (!this.classList.contains('unavailable')) {
                        // Remove selection from all slots
                        timeSlots.forEach(s => s.classList.remove('selected'));

                        // Add selection animation
                        this.classList.add('time-selecting');

                        // Complete selection after animation
                        setTimeout(() => {
                            this.classList.add('selected');
                            this.classList.remove('time-selecting');
                            appointmentTimeInput.value = this.getAttribute('data-time');
                            updateBookingSummary();
                        }, 150);
                    } else {
                        // Show tooltip for unavailable slots
                        const reason = this.getAttribute('title') ||
                            'This time slot is unavailable';
                        showTooltip(this, reason);
                    }
                });
            });

            // Show tooltip function
            function showTooltip(element, message) {
                // Create tooltip element if it doesn't exist
                let tooltip = document.getElementById('custom-tooltip');
                if (!tooltip) {
                    tooltip = document.createElement('div');
                    tooltip.id = 'custom-tooltip';
                    tooltip.className = 'custom-tooltip';
                    document.body.appendChild(tooltip);
                }

                // Set tooltip content and position
                tooltip.textContent = message;

                // Get element position
                const rect = element.getBoundingClientRect();
                tooltip.style.top = `${rect.top - 40}px`;
                tooltip.style.left = `${rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2)}px`;

                // Show tooltip
                tooltip.classList.add('show');

                // Hide tooltip after delay
                setTimeout(() => {
                    tooltip.classList.remove('show');
                }, 2000);
            }

            // Date selection with availability checking
            const appointmentDateInput = document.getElementById('appointment-date');
            appointmentDateInput.addEventListener('change', function() {
                // Clear time selection when date changes
                timeSlots.forEach(slot => slot.classList.remove('selected'));
                appointmentTimeInput.value = '';

                // Update available time slots
                updateAvailableTimeSlots();
                updateBookingSummary();
            });

            // Calendar functionality with improved date selection
            const calendarDays = document.getElementById('calendar-days');
            const currentMonthElement = document.getElementById('current-month');
            const prevMonthButton = document.getElementById('prev-month');
            const nextMonthButton = document.getElementById('next-month');

            let currentDate = new Date();
            let currentMonth = currentDate.getMonth();
            let currentYear = currentDate.getFullYear();

            function generateCalendar(month, year) {
                // Clear previous calendar days (except headers)
                const dayHeaders = document.querySelectorAll('.calendar-day-header');
                calendarDays.innerHTML = '';

                // Add day headers back
                dayHeaders.forEach(header => {
                    calendarDays.appendChild(header.cloneNode(true));
                });

                // Set current month display
                const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August',
                    'September', 'October', 'November', 'December'
                ];
                currentMonthElement.textContent = `${monthNames[month]} ${year}`;

                // Get first day of month and total days
                const firstDay = new Date(year, month, 1).getDay();
                const daysInMonth = new Date(year, month + 1, 0).getDate();

                // Add empty cells for days before first day of month
                for (let i = 0; i < firstDay; i++) {
                    const emptyDay = document.createElement('div');
                    emptyDay.classList.add('calendar-day');
                    emptyDay.style.visibility = 'hidden';
                    calendarDays.appendChild(emptyDay);
                }

                // Add days of month
                const today = new Date();
                today.setHours(0, 0, 0, 0);

                for (let i = 1; i <= daysInMonth; i++) {
                    const dayElement = document.createElement('div');
                    dayElement.classList.add('calendar-day');
                    dayElement.textContent = i;

                    const currentDateCheck = new Date(year, month, i);
                    currentDateCheck.setHours(0, 0, 0, 0);

                    // Get day of week name
                    const dayOfWeekName = currentDateCheck.toLocaleDateString('en-US', {
                        weekday: 'long'
                    });

                    // Check if it's a closed day based on business hours
                    if (businessHours[dayOfWeekName] && businessHours[dayOfWeekName].closed) {
                        dayElement.classList.add('unavailable');
                        dayElement.setAttribute('title', 'Closed on ' + dayOfWeekName);
                    }

                    // Check if it's today
                    if (currentDateCheck.getTime() === today.getTime()) {
                        dayElement.classList.add('today');
                    }

                    // Check if it's in the past
                    if (currentDateCheck < today) {
                        dayElement.classList.add('unavailable');
                        dayElement.setAttribute('title', 'Past date');
                    }

                    // Add click event for available days
                    if (!dayElement.classList.contains('unavailable')) {
                        dayElement.addEventListener('click', function() {
                            // Remove selection from all days
                            document.querySelectorAll('.calendar-day').forEach(day => {
                                day.classList.remove('selected');
                            });

                            // Add selection animation
                            this.classList.add('day-selecting');

                            // Complete selection after animation
                            setTimeout(() => {
                                this.classList.add('selected');
                                this.classList.remove('day-selecting');

                                // Create a date object for the selected date
                                const selectedDate = new Date(year, month, parseInt(this
                                    .textContent));

                                // Add 1 day to the selected date
                                selectedDate.setDate(selectedDate.getDate() + 1);

                                // Format date for input (YYYY-MM-DD)
                                const formattedDate = selectedDate.toISOString().split('T')[0];
                                appointmentDateInput.value = formattedDate;

                                // Trigger change event to update summary and time slots
                                const event = new Event('change');
                                appointmentDateInput.dispatchEvent(event);
                            }, 150);
                        });
                    } else {
                        // Add tooltip event for unavailable days
                        dayElement.addEventListener('click', function() {
                            const reason = this.getAttribute('title') || 'This date is unavailable';
                            showTooltip(this, reason);
                        });
                    }

                    calendarDays.appendChild(dayElement);
                }
            }

            // Initialize calendar
            generateCalendar(currentMonth, currentYear);

            // Previous month button
            prevMonthButton.addEventListener('click', function() {
                currentMonth--;
                if (currentMonth < 0) {
                    currentMonth = 11;
                    currentYear--;
                }
                generateCalendar(currentMonth, currentYear);
            });

            // Next month button
            nextMonthButton.addEventListener('click', function() {
                currentMonth++;
                if (currentMonth > 11) {
                    currentMonth = 0;
                    currentYear++;
                }
                generateCalendar(currentMonth, currentYear);
            });

            // Enhanced time slot generation that removes unavailable slots
            function generateTimeSlots(date, businessHours) {
                const timeSlotsContainer = document.getElementById('time-slots-container');
                timeSlotsContainer.innerHTML = `
                    <div class="time-slots-loading">
                        <div class="loading-spinner"></div>
                        <p>Finding available times...</p>
                    </div>`;

                // Get day of week
                const selectedDate = new Date(date);
                const dayOfWeek = selectedDate.toLocaleDateString('en-US', {
                    weekday: 'long'
                });
                const hours = businessHours[dayOfWeek];

                // If business is closed on selected day
                if (hours.closed) {
                    timeSlotsContainer.innerHTML = `
                        <div class="time-slots-message">
                            <i class="fas fa-calendar-times"></i>
                            <p>We're closed on ${dayOfWeek}s</p>
                            <span>Please select another day</span>
                        </div>`;
                    return;
                }

                // Get selected services and calculate total duration
                const selectedServices = document.querySelectorAll('.service-card.selected');
                let totalDuration = 0;
                selectedServices.forEach(service => {
                    totalDuration += parseInt(service.getAttribute('data-service-duration'));
                });

                // If no services selected, show message
                if (totalDuration === 0) {
                    timeSlotsContainer.innerHTML = `
                        <div class="time-slots-message">
                            <i class="fas fa-cut"></i>
                            <p>Please select a service first</p>
                            <span>Time slots will appear after you select a service</span>
                        </div>`;
                    return;
                }

                // Show loading message while checking availability
                timeSlotsContainer.innerHTML = `
                    <div class="time-slots-loading">
                        <div class="loading-spinner"></div>
                        <p>Finding available times for your ${totalDuration} minute appointment</p>
                    </div>`;

                // Generate time slots based on business hours
                const openTime = hours.open.split(':');
                const closeTime = hours.close.split(':');

                const startHour = parseInt(openTime[0]);
                const startMinute = parseInt(openTime[1]);
                const endHour = parseInt(closeTime[0]);
                const endMinute = parseInt(closeTime[1]);

                const startDate = new Date(selectedDate);
                startDate.setHours(startHour, startMinute, 0);

                const endDate = new Date(selectedDate);
                endDate.setHours(endHour, endMinute, 0);

                // Create time slots at 30-minute intervals
                const interval = 30 * 60 * 1000; // 30 minutes in milliseconds

                // Create arrays to store available slots
                const morningSlots = [];
                const afternoonSlots = [];
                const eveningSlots = [];

                // Generate all possible slots first
                for (let time = startDate.getTime(); time < endDate.getTime(); time += interval) {
                    const slotTime = new Date(time);
                    const formattedTime = slotTime.toLocaleTimeString('en-US', {
                        hour: 'numeric',
                        minute: '2-digit',
                        hour12: true
                    });

                    const timeValue = slotTime.toTimeString().substring(0, 5);

                    // Check if appointment would end after closing time
                    const endTime = new Date(slotTime.getTime() + totalDuration * 60000);
                    const endTimeStr = endTime.toTimeString().substring(0, 5);

                    if (endTimeStr > hours.close) {
                        continue; // Skip this slot
                    }

                    const hour = slotTime.getHours();
                    const slot = {
                        time: timeValue,
                        display: formattedTime,
                        hour: hour
                    };

                    // Categorize by time of day
                    if (hour < 12) {
                        morningSlots.push(slot);
                    } else if (hour < 17) {
                        afternoonSlots.push(slot);
                    } else {
                        eveningSlots.push(slot);
                    }
                }

                // Check availability for all slots in batches
                checkSlotsAvailability(date, totalDuration, morningSlots, afternoonSlots, eveningSlots);
            }

            // Function to check availability and render time slots
            async function checkSlotsAvailability(date, duration, morningSlots, afternoonSlots, eveningSlots) {
                const timeSlotsContainer = document.getElementById('time-slots-container');

                // Create containers for each time period
                const container = document.createElement('div');
                container.className = 'time-slots-container';

                // Process slots in batches to avoid rate limiting
                const allSlots = [...morningSlots, ...afternoonSlots, ...eveningSlots];
                const batchSize = 3;
                const availableSlots = {
                    morning: [],
                    afternoon: [],
                    evening: []
                };

                // Process in batches
                for (let i = 0; i < allSlots.length; i += batchSize) {
                    const batch = allSlots.slice(i, i + batchSize);
                    const batchPromises = batch.map(slot =>
                        checkAppointmentAvailability(date, slot.time, duration)
                        .then(isAvailable => {
                            if (isAvailable) {
                                // Add to appropriate category
                                const hour = slot.hour;
                                if (hour < 12) {
                                    availableSlots.morning.push(slot);
                                } else if (hour < 17) {
                                    availableSlots.afternoon.push(slot);
                                } else {
                                    availableSlots.evening.push(slot);
                                }
                            }
                            return isAvailable;
                        })
                        .catch(error => {
                            console.error('Error checking availability:', error);
                            return false;
                        })
                    );

                    await Promise.all(batchPromises);

                    // Add a small delay between batches
                    if (i + batchSize < allSlots.length) {
                        await new Promise(resolve => setTimeout(resolve, 300));
                    }
                }

                // Clear loading indicator
                timeSlotsContainer.innerHTML = '';

                // Render available time slots by category
                let hasAvailableSlots = false;

                // Morning slots
                if (availableSlots.morning.length > 0) {
                    hasAvailableSlots = true;
                    const morningSection = createTimeSection('Morning', availableSlots.morning);
                    timeSlotsContainer.appendChild(morningSection);
                }

                // Afternoon slots
                if (availableSlots.afternoon.length > 0) {
                    hasAvailableSlots = true;
                    const afternoonSection = createTimeSection('Afternoon', availableSlots.afternoon);
                    timeSlotsContainer.appendChild(afternoonSection);
                }

                // Evening slots
                if (availableSlots.evening.length > 0) {
                    hasAvailableSlots = true;
                    const eveningSection = createTimeSection('Evening', availableSlots.evening);
                    timeSlotsContainer.appendChild(eveningSection);
                }

                // If no available slots
                if (!hasAvailableSlots) {
                    timeSlotsContainer.innerHTML = `
                        <div class="time-slots-message">
                            <i class="fas fa-calendar-times"></i>
                            <p>No available time slots</p>
                            <span>Please try another day or select different services</span>
                        </div>`;
                }
            }

            // Helper function to create a time section
            function createTimeSection(title, slots) {
                const section = document.createElement('div');
                section.className = 'time-slots-section';

                const heading = document.createElement('h4');
                heading.textContent = title;
                section.appendChild(heading);

                const grid = document.createElement('div');
                grid.className = 'time-slots-grid';

                slots.forEach(slot => {
                    const timeSlot = document.createElement('div');
                    timeSlot.className = 'time-slot';
                    timeSlot.setAttribute('data-time', slot.time);
                    timeSlot.textContent = slot.display;

                    // Add click event
                    timeSlot.addEventListener('click', function() {
                        // Remove selection from all slots
                        document.querySelectorAll('.time-slot').forEach(s => s.classList.remove(
                            'selected'));

                        // Add selection animation
                        this.classList.add('time-selecting');

                        // Complete selection after animation
                        setTimeout(() => {
                            this.classList.add('selected');
                            this.classList.remove('time-selecting');
                            document.getElementById('appointment-time').value = this
                                .getAttribute('data-time');
                            updateBookingSummary();
                        }, 150);
                    });

                    grid.appendChild(timeSlot);
                });

                section.appendChild(grid);
                return section;
            }

            // Improved availability checking function with better error handling
            async function checkAppointmentAvailability(date, time, duration) {
                try {
                    const response = await fetch('/api/check-availability', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .getAttribute('content')
                        },
                        body: JSON.stringify({
                            date,
                            time,
                            duration
                        })
                    });

                    // Check if response is ok before trying to parse JSON
                    if (!response.ok) {
                        // If we hit rate limiting, assume slot is available for now
                        if (response.status === 429) {
                            console.warn('Rate limit hit, assuming slot is available');
                            return true;
                        }
                        throw new Error(`Server responded with ${response.status}`);
                    }

                    const data = await response.json();

                    // For debugging
                    if (!data.available) {
                        console.log(`Time slot ${time} is unavailable. Conflicts: ${data.conflictingCount}`);
                    }

                    return data.available;
                } catch (error) {
                    console.error('Error checking availability:', error);
                    // Default to available if API fails to prevent blocking all slots
                    return true;
                }
            }

            // Update booking summary with more details
            function updateBookingSummary() {
                // Selected services
                const selectedServices = document.querySelectorAll('.service-card.selected');
                const selectedServicesElement = document.getElementById('selected-services-summary');

                if (selectedServices.length === 0) {
                    selectedServicesElement.textContent = 'None';
                    selectedServicesElement.classList.remove('has-services');
                } else {
                    const serviceDetails = Array.from(selectedServices).map(card => {
                        const name = card.querySelector('h3').textContent;
                        const price = card.getAttribute('data-service-price');
                        const duration = card.getAttribute('data-service-duration');
                        return `${name} ($${price}, ${duration} min)`;
                    });
                    selectedServicesElement.textContent = serviceDetails.join(', ');
                    selectedServicesElement.classList.add('has-services');
                }

                // Selected date
                const selectedDateElement = document.getElementById('selected-date-summary');
                if (appointmentDateInput.value) {
                    const date = new Date(appointmentDateInput.value);
                    selectedDateElement.textContent = date.toLocaleDateString('en-US', {
                        weekday: 'long',
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    });
                    selectedDateElement.classList.add('has-date');
                } else {
                    selectedDateElement.textContent = 'Not selected';
                    selectedDateElement.classList.remove('has-date');
                }

                // Selected time
                const selectedTimeElement = document.getElementById('selected-time-summary');
                const selectedTimeSlot = document.querySelector('.time-slot.selected');

                if (selectedTimeSlot) {
                    const startTime = selectedTimeSlot.textContent.trim();

                    // Calculate end time based on service duration
                    const totalDuration = Array.from(selectedServices).reduce((total, service) => {
                        return total + parseInt(service.getAttribute('data-service-duration'));
                    }, 0);

                    if (totalDuration > 0 && appointmentDateInput.value) {
                        const startDateTime = new Date(
                            `${appointmentDateInput.value}T${selectedTimeSlot.getAttribute('data-time')}`);
                        const endDateTime = new Date(startDateTime.getTime() + totalDuration * 60000);
                        const endTime = endDateTime.toLocaleTimeString('en-US', {
                            hour: 'numeric',
                            minute: '2-digit',
                            hour12: true
                        });

                        selectedTimeElement.textContent = `${startTime} - ${endTime}`;
                    } else {
                        selectedTimeElement.textContent = startTime;
                    }
                    selectedTimeElement.classList.add('has-time');
                } else {
                    selectedTimeElement.textContent = 'Not selected';
                    selectedTimeElement.classList.remove('has-time');
                }

                // Total duration
                const totalDurationElement = document.getElementById('total-duration-summary');
                let totalDuration = 0;

                selectedServices.forEach(service => {
                    totalDuration += parseInt(service.getAttribute('data-service-duration'));
                });

                totalDurationElement.textContent = `${totalDuration} min`;

                // Highlight if duration is long
                if (totalDuration > 60) {
                    totalDurationElement.classList.add('long-duration');
                } else {
                    totalDurationElement.classList.remove('long-duration');
                }

                // Total price
                const totalPriceElement = document.getElementById('total-price-summary');
                let totalPrice = 0;

                selectedServices.forEach(service => {
                    totalPrice += parseFloat(service.getAttribute('data-service-price'));
                });

                totalPriceElement.textContent = `$${totalPrice.toFixed(2)}`;

                // Update submit button state
                const submitButton = document.getElementById('booking-submit-btn');
                if (selectedServices.length > 0 && appointmentDateInput.value && appointmentTimeInput.value) {
                    submitButton.removeAttribute('disabled');
                    submitButton.classList.add('ready');
                } else {
                    submitButton.setAttribute('disabled', 'disabled');
                    submitButton.classList.remove('ready');
                }
            }

            // Form submission with enhanced validation and loading state
            const appointmentForm = document.getElementById('appointment-form');
            const submitButton = document.getElementById('booking-submit-btn');

            appointmentForm.addEventListener('submit', function(e) {
                e.preventDefault();

                // Validate form
                const selectedServices = document.querySelectorAll('.service-card.selected');
                if (selectedServices.length === 0) {
                    showAlert('Please select at least one service.', 'error');
                    return;
                }

                if (!appointmentDateInput.value) {
                    showAlert('Please select a date for your appointment.', 'error');
                    return;
                }

                if (!appointmentTimeInput.value) {
                    showAlert('Please select a time for your appointment.', 'error');
                    return;
                }

                // Check if selected time slot is available
                const selectedTimeSlot = document.querySelector('.time-slot.selected');
                if (selectedTimeSlot && selectedTimeSlot.classList.contains('unavailable')) {
                    showAlert('The selected time slot is not available. Please choose another time.',
                        'error');
                    return;
                }

                // Show loading state on button
                submitButton.disabled = true;
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
                submitButton.classList.add('loading');

                // Calculate end time based on service duration
                const totalDuration = Array.from(selectedServices).reduce((total, service) => {
                    return total + parseInt(service.getAttribute('data-service-duration'));
                }, 0);

                if (totalDuration > 0 && appointmentDateInput.value && appointmentTimeInput.value) {
                    const startTime = appointmentTimeInput.value;
                    const startDateTime = new Date(`${appointmentDateInput.value}T${startTime}`);
                    const endDateTime = new Date(startDateTime.getTime() + totalDuration * 60000);
                    const endTime = endDateTime.toTimeString().substring(0, 5);

                    // Add end time to form
                    const endTimeInput = document.createElement('input');
                    endTimeInput.type = 'hidden';
                    endTimeInput.name = 'end_time';
                    endTimeInput.value = endTime;
                    this.appendChild(endTimeInput);
                }

                // If all validations pass, submit the form
                this.submit();
            });

            // Helper function to show loading overlay
            function showLoading() {
                // Create loading overlay if it doesn't exist
                let loadingOverlay = document.getElementById('loading-overlay');
                if (!loadingOverlay) {
                    loadingOverlay = document.createElement('div');
                    loadingOverlay.id = 'loading-overlay';
                    loadingOverlay.className = 'loading-overlay';

                    const spinner = document.createElement('div');
                    spinner.className = 'loading-spinner';
                    spinner.innerHTML = `
                        <i class="fas fa-cut fa-spin"></i>
                        <p>Booking your appointment...</p>
                    `;

                    loadingOverlay.appendChild(spinner);
                    document.body.appendChild(loadingOverlay);
                }

                // Show the overlay with animation
                setTimeout(() => {
                    loadingOverlay.classList.add('show');
                }, 10);
            }

            // Helper function to show alerts
            function showAlert(message, type = 'info') {
                // Create alert element
                const alertElement = document.createElement('div');
                alertElement.className = `alert alert-${type}`;
                alertElement.innerHTML = `
                    <div class="alert-icon">
                        <i class="fas fa-${type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
                    </div>
                    <div class="alert-message">${message}</div>
                    <button class="alert-close">&times;</button>
                `;

                // Add to document
                document.body.appendChild(alertElement);

                // Show with animation
                setTimeout(() => {
                    alertElement.classList.add('show');
                }, 10);

                // Add close button functionality
                const closeButton = alertElement.querySelector('.alert-close');
                closeButton.addEventListener('click', () => {
                    alertElement.classList.remove('show');
                    setTimeout(() => {
                        alertElement.remove();
                    }, 300);
                });

                // Auto-close after 5 seconds for non-error alerts
                if (type !== 'error') {
                    setTimeout(() => {
                        alertElement.classList.remove('show');
                        setTimeout(() => {
                            alertElement.remove();
                        }, 300);
                    }, 5000);
                }
            }

            // Define the updateAvailableTimeSlots function that calls our new implementation
            function updateAvailableTimeSlots() {
                const selectedDate = appointmentDateInput.value;
                if (!selectedDate) return;

                // Call our new implementation
                generateTimeSlots(selectedDate, businessHours);
            }

            // Replace the handleCalendarDayClick function with this version
            function handleCalendarDayClick(dayElement) {
                if (dayElement.classList.contains('selectable')) {
                    // Get the exact day number from the calendar UI
                    const day = parseInt(dayElement.textContent.trim());
                    const currentMonthText = document.getElementById('current-month').textContent.trim();
                    const [monthName, year] = currentMonthText.split(' ');

                    // Convert month name to month number (1-12)
                    const months = {
                        'January': 1,
                        'February': 2,
                        'March': 3,
                        'April': 4,
                        'May': 5,
                        'June': 6,
                        'July': 7,
                        'August': 8,
                        'September': 9,
                        'October': 10,
                        'November': 11,
                        'December': 12
                    };
                    const month = months[monthName];

                    // Create a date object and add 1 day
                    const dateObj = new Date(year, month - 1, day);
                    dateObj.setDate(dateObj.getDate() + 1);

                    // Format the adjusted date as YYYY-MM-DD
                    const adjustedYear = dateObj.getFullYear();
                    const adjustedMonth = String(dateObj.getMonth() + 1).padStart(2, '0');
                    const adjustedDay = String(dateObj.getDate()).padStart(2, '0');
                    const selectedDate = `${adjustedYear}-${adjustedMonth}-${adjustedDay}`;

                    console.log("Selected date from calendar:", selectedDate); // Debug

                    // Set the input value directly with the adjusted date string
                    const dateInput = document.getElementById('appointment-date');
                    dateInput.value = selectedDate;

                    // Make the input disabled
                    dateInput.disabled = true;

                    // Update the summary display with the original selected date (for UI consistency)
                    document.getElementById('selected-date-summary').textContent =
                        `${monthName} ${day}, ${year}`;

                    // Update UI to show the selected date
                    document.querySelectorAll('.calendar-day').forEach(d => {
                        d.classList.remove('selected');
                    });
                    dayElement.classList.add('selected');

                    // Update available time slots
                    updateAvailableTimeSlots();
                }
            }

            // Add this to your DOMContentLoaded event to ensure the date input is disabled on page load
            document.addEventListener('DOMContentLoaded', function() {
                // Disable the date input
                document.getElementById('appointment-date').disabled = true;
            });
        });
    </script>

    <style>
        /* Enhanced time slot styling */
        .time-slots-container {
            margin-top: 15px;
            width: 100%;
        }

        .time-slots-section {
            margin-bottom: 25px;
            background-color: #fff;
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .time-slots-section h4 {
            margin-bottom: 15px;
            color: #333;
            font-weight: 600;
            font-size: 16px;
            position: relative;
            padding-left: 20px;
            display: flex;
            align-items: center;
        }

        .time-slots-section h4::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: #007bff;
        }

        .time-slots {
            display: flex !important;
        }

        .time-slots-grid {
            /* display: grid; */
            grid-template-columns: repeat(auto-fill, minmax(90px, 1fr));
            gap: 10px;
        }

        .time-slot {
            padding: 12px 8px;
            text-align: center;
            background-color: #f8f9fa;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
            border: 1px solid #e9ecef;
            font-weight: 500;
            font-size: 14px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .time-slot:hover {
            background-color: #e9ecef;
            transform: translateY(-2px);
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.08);
        }

        .time-slot.selected {
            background-color: #007bff;
            color: white;
            border-color: #0062cc;
            box-shadow: 0 3px 6px rgba(0, 123, 255, 0.25);
        }

        .time-slot.time-selecting {
            animation: pulse 0.3s ease;
        }

        .time-slots-loading {
            text-align: center;
            padding: 30px;
            background-color: #f8f9fa;
            border-radius: 8px;
            margin: 15px 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 150px;
        }

        .time-slots-loading i,
        .time-slots-loading .loading-spinner {
            color: #007bff;
            margin-bottom: 15px;
            display: block;
        }

        .loading-spinner {
            width: 40px;
            height: 40px;
            border: 3px solid #e9ecef;
            border-radius: 50%;
            border-top-color: #007bff;
            animation: spin 1s linear infinite;
            margin-bottom: 15px;
        }

        .time-slots-loading p {
            margin: 0;
            font-weight: 500;
            color: #6c757d;
        }

        .time-slots-message {
            text-align: center;
            padding: 30px;
            background-color: #f8f9fa;
            border-radius: 8px;
            margin: 15px 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 150px;
        }

        .time-slots-message i {
            font-size: 32px;
            color: #6c757d;
            margin-bottom: 15px;
            display: block;
        }

        .time-slots-message p {
            margin: 0 0 5px 0;
            font-size: 16px;
            font-weight: 600;
            color: #495057;
        }

        .time-slots-message span {
            font-size: 14px;
            color: #6c757d;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }

            100% {
                transform: scale(1);
            }
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* No slots available message */
        .no-slots-available {
            text-align: center;
            padding: 25px;
            background-color: #f8f9fa;
            border-radius: 8px;
            color: #6c757d;
            border: 1px dashed #dee2e6;
            margin: 15px 0;
        }
    </style>
</body>

</html>
