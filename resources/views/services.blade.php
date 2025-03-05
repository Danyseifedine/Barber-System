<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('core/packages/iziToast/iziToast.min.css') }}">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Our Services - BSharp Cuts</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
    <link rel="stylesheet" href="{{ asset('css/services.css') }}">
    <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
</head>

<body>
    @include('components.navbar')


    <section class="services-intro">
        <div class="container">
            <div class="intro-text">
                <h2>Craftsmanship & Precision</h2>
                <p>At BSharp Cuts, we believe that a great haircut is more than just a service—it's an experience. Our
                    skilled barbers combine traditional techniques with modern styles to create looks that are uniquely
                    you.</p>
                <p>Each service includes a consultation to understand your preferences and a relaxing experience in our
                    premium barber chairs.</p>
            </div>
            <div class="intro-image">
                <img src="https://images.unsplash.com/photo-1503951914875-452162b0f3f1?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80"
                    alt="Barber Shop Interior">
            </div>
        </div>
    </section>

    <section class="services-list">
        <div class="container">
            <div class="section-header">
                <h2>Our Services</h2>
                <div class="section-divider"></div>
                <p>Premium grooming services tailored to your style</p>
            </div>

            <div class="services-grid">
                @if (count($services) > 0)
                    @php
                        // Shuffle the services collection to randomize the order
                        $randomServices = $services->shuffle();
                    @endphp
                    @foreach ($randomServices as $service)
                        <div class="service-card">
                            <div class="service-image">
                                <img src="{{ $service->image_url ?? 'https://images.unsplash.com/photo-1622286342621-4bd786c2447c?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80' }}"
                                    alt="{{ $service->name }}">
                            </div>
                            <div class="service-details">
                                <h3>{{ $service->name }}</h3>
                                <div class="service-price">${{ number_format($service->price, 2) }}</div>
                                <p>{{ $service->description }}</p>
                                <div class="service-duration">
                                    <i class="far fa-clock"></i> {{ $service->duration }} minutes
                                </div>
                                <a href="{{ route('register') }}" class="btn btn-primary">Book Now</a>
                            </div>
                        </div>
                    @endforeach
                @else
                    @php
                        // Array of static services
                        $staticServices = [
                            [
                                'name' => 'Classic Haircut',
                                'price' => 25.0,
                                'description' =>
                                    'Our signature haircut service includes consultation, precision cutting, styling, and a hot towel finish for the perfect look.',
                                'duration' => 30,
                                'image' =>
                                    'https://images.unsplash.com/photo-1622286342621-4bd786c2447c?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80',
                            ],
                            [
                                'name' => 'Beard Trim & Shaping',
                                'price' => 15.0,
                                'description' =>
                                    'Expert beard trimming and shaping to enhance your facial features. Includes hot towel treatment and premium beard oil application.',
                                'duration' => 20,
                                'image' =>
                                    'https://images.unsplash.com/photo-1534297635766-a262cdcb8ee4?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80',
                            ],
                            [
                                'name' => 'Premium Package',
                                'price' => 45.0,
                                'description' =>
                                    'The complete experience: haircut, beard trim, facial massage, and styling with premium products for the ultimate refresh.',
                                'duration' => 60,
                                'image' =>
                                    'https://images.unsplash.com/photo-1605497788044-5a32c7078486?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1374&q=80',
                            ],
                            [
                                'name' => 'Hot Towel Shave',
                                'price' => 30.0,
                                'description' =>
                                    'Experience the luxury of a traditional hot towel shave. Includes pre-shave oil, hot towel preparation, precision straight razor shave, and aftershave balm.',
                                'duration' => 30,
                                'image' =>
                                    'https://images.unsplash.com/photo-1599351431202-1e0f0137899a?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80',
                            ],
                            [
                                'name' => 'Hair Styling',
                                'price' => 20.0,
                                'description' =>
                                    'Professional styling with premium products to achieve your desired look. Perfect for special occasions or just to freshen up your style.',
                                'duration' => 25,
                                'image' =>
                                    'https://images.unsplash.com/photo-1621605815971-fbc98d665033?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80',
                            ],
                            [
                                'name' => 'Kids Haircut',
                                'price' => 18.0,
                                'description' =>
                                    'Gentle and patient haircut service for children. Includes a fun atmosphere and a small treat to make the experience enjoyable.',
                                'duration' => 20,
                                'image' =>
                                    'https://images.unsplash.com/photo-1595956553066-fe24a8c33395?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1374&q=80',
                            ],
                        ];

                        // Shuffle the array to randomize the order
                        shuffle($staticServices);
                    @endphp

                    @foreach ($staticServices as $service)
                        <div class="service-card">
                            <div class="service-image">
                                <img src="{{ $service['image'] }}" alt="{{ $service['name'] }}">
                            </div>
                            <div class="service-details">
                                <h3>{{ $service['name'] }}</h3>
                                <div class="service-price">${{ number_format($service['price'], 2) }}</div>
                                <p>{{ $service['description'] }}</p>
                                <div class="service-duration">
                                    <i class="far fa-clock"></i> {{ $service['duration'] }} minutes
                                </div>
                                <a href="{{ route('register') }}" class="btn btn-primary">Book Now</a>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </section>

    <section class="booking-cta">
        <div class="container">
            <div class="cta-content">
                <h2>Ready to Experience BSharp Cuts?</h2>
                <p>Book your appointment today and discover the difference</p>
                <a href="{{ route('register') }}" class="btn btn-primary">Book Your Appointment</a>
            </div>
        </div>
    </section>

    <footer class="footer">
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
                    <a href="{{ url('/') }}#about">About</a>
                    <a href="{{ route('services') }}">Services</a>
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
                    <h3>Legal</h3>
                    <a href="#">Terms of Service</a>
                    <a href="#">Privacy Policy</a>
                    <a href="#">Cookie Policy</a>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; {{ date('Y') }} BSharp Cuts. All rights reserved.</p>
        </div>
    </footer>

    <script src="{{ asset('core/packages/iziToast/iziToast.min.js') }}"></script>
    <script src="{{ url('core/vendor/js/plugins.bundle.js') }}"></script>
    <script src="{{ url('core/vendor/js/scripts.bundle.js') }}"></script>
    <script src="{{ asset('core/global/Launcher.js') }}" type="module"></script>

    <script>
        // Mobile menu toggle
        document.querySelector('.menu-toggle').addEventListener('click', function() {
            document.querySelector('.nav-links').classList.toggle('active');
            document.querySelector('.auth-buttons').classList.toggle('active');
        });
    </script>
</body>

</html>
