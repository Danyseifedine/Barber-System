<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('core/packages/iziToast/iziToast.min.css') }}">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>BSharp Cuts - Premium Barber Experience</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
    <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
</head>

<body>
    <div class="hero">
        @include('components.navbar')

        <div class="hero-content">
            <div class="hero-text">
                <h1>Premium Haircuts.<br>Zero Waiting.</h1>
                <p>Book your next haircut online in seconds. Experience the best barber service with BSharp Cuts.</p>
                <div class="hero-buttons">
                    <a href="{{ route('register') }}" class="btn btn-primary">Book Appointment</a>
                    <a href="#how-it-works" class="btn btn-secondary">How It Works</a>
                </div>
            </div>
            <div class="hero-image">
                <img src="https://images.unsplash.com/photo-1599351431202-1e0f0137899a?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80"
                    alt="Barber Shop">
            </div>
        </div>
    </div>

    <section id="how-it-works" class="how-it-works">
        <div class="section-header">
            <h2>How It Works</h2>
            <div class="section-divider"></div>
            <p>Book your appointment in 3 simple steps</p>
        </div>

        <div class="steps-container">
            <div class="step-card">
                <div class="step-number">1</div>
                <div class="step-icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <h3>Create Account</h3>
                <p>Sign up in seconds with your email or social media accounts</p>
            </div>

            <div class="step-card">
                <div class="step-number">2</div>
                <div class="step-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <h3>Book Appointment</h3>
                <p>Choose your service, barber, date and time that works for you</p>
            </div>

            <div class="step-card">
                <div class="step-number">3</div>
                <div class="step-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h3>Get Your Cut</h3>
                <p>Show up at your scheduled time and enjoy your premium service</p>
            </div>
        </div>

        <div class="cta-small">
            <a href="{{ route('register') }}" class="btn btn-primary">Book Your Appointment Now</a>
        </div>
    </section>

    <section id="about" class="about">
        <div class="about-content">
            <div class="about-text">
                <h2>About BSharp Cuts</h2>
                <div class="section-divider"></div>
                <p>BSharp Cuts combines traditional barbering expertise with modern technology to deliver an exceptional
                    grooming experience.</p>
                <p>Our online booking system eliminates waiting times and ensures you get the exact service you want,
                    when you want it. Every barber in our network is professionally trained and passionate about their
                    craft.</p>
                <a href="#contact" class="btn btn-primary">Contact Us</a>
            </div>
            <div class="about-image">
                <img src="https://images.unsplash.com/photo-1621605815971-fbc98d665033?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80"
                    alt="Barber Shop">
            </div>
        </div>
    </section>

    <section id="app-features" class="app-features">
        <div class="section-header">
            <h2>App Features</h2>
            <div class="section-divider"></div>
            <p>Everything you need for a seamless barber experience</p>
        </div>

        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <h3>Easy Booking</h3>
                <p>Book, reschedule or cancel appointments with just a few taps</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-bell"></i>
                </div>
                <h3>Reminders</h3>
                <p>Get notifications before your appointment so you never forget</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-user-circle"></i>
                </div>
                <h3>Barber Selection</h3>
                <p>Choose your favorite barber and stick with them for consistent results</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-credit-card"></i>
                </div>
                <h3>Easy Payment</h3>
                <p>Securely pay online or in-person with multiple payment options</p>
            </div>
        </div>
    </section>

    <section id="testimonials" class="testimonials">
        <div class="section-header">
            <h2>What Our Clients Say</h2>
            <div class="section-divider"></div>
            <p>Join thousands of satisfied customers</p>
        </div>

        <div class="testimonial-slider">
            <div class="testimonial-card active">
                <div class="testimonial-content">
                    <p>"Booking with BSharp Cuts has saved me so much time. No more waiting around - I just show up at
                        my appointment time and get right in the chair."</p>
                </div>
                <div class="testimonial-author">
                    <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="James Wilson">
                    <div class="author-info">
                        <h4>James Wilson</h4>
                        <p>Regular Customer</p>
                    </div>
                </div>
            </div>

            <div class="testimonial-card">
                <div class="testimonial-content">
                    <p>"The ability to choose my barber and see their portfolio first is amazing. I've found a barber
                        who really understands my style and I book him every time."</p>
                </div>
                <div class="testimonial-author">
                    <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="Sarah Johnson">
                    <div class="author-info">
                        <h4>Sarah Johnson</h4>
                        <p>New Customer</p>
                    </div>
                </div>
            </div>

            <div class="testimonial-card">
                <div class="testimonial-content">
                    <p>"The reminder notifications are a lifesaver! I used to always forget my appointments, but now I
                        get a text the day before to remind me."</p>
                </div>
                <div class="testimonial-author">
                    <img src="https://randomuser.me/api/portraits/men/67.jpg" alt="Michael Rodriguez">
                    <div class="author-info">
                        <h4>Michael Rodriguez</h4>
                        <p>Monthly Subscriber</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="testimonial-dots">
            <span class="dot active"></span>
            <span class="dot"></span>
            <span class="dot"></span>
        </div>
    </section>

    <section id="contact" class="contact">
        <div class="section-header">
            <h2>Get In Touch</h2>
            <div class="section-divider"></div>
            <p>Have questions? We're here to help</p>
        </div>

        <div class="contact-container">
            <div class="contact-info">
                <div class="contact-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <p>123 Barber Street, Suite 101<br>New York, NY 10001</p>
                </div>
                <div class="contact-item">
                    <i class="fas fa-phone"></i>
                    <p>+1 (555) 123-4567</p>
                </div>
                <div class="contact-item">
                    <i class="fas fa-envelope"></i>
                    <p>info@bsharpcuts.com</p>
                </div>
                <div class="social-links">
                    <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>

            <form class="contact-form" form-id="contact" route="{{ route('contact') }}"
                identifier="single-form-post-handler" http-request feedback success-toast>
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" placeholder="Enter your name" feedback-id="name-feedback" id="name"
                        name="name">
                    <div id="name-feedback" class="invalid-feedback text-danger fw-bold" style="color: red;"></div>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" placeholder="Enter your email" feedback-id="email-feedback" id="email"
                        name="email">
                    <div id="email-feedback" class="invalid-feedback text-danger fw-bold" style="color: red;"></div>
                </div>
                <div class="form-group">
                    <label for="subject">Subject</label>
                    <input type="text" placeholder="Enter your subject" feedback-id="subject-feedback"
                        id="subject" name="subject">
                    <div id="subject-feedback" class="invalid-feedback text-danger fw-bold" style="color: red;">
                    </div>
                </div>
                <div class="form-group">
                    <label for="message">Message</label>
                    <textarea placeholder="Enter your message" feedback-id="message-feedback" id="message" name="message"
                        rows="5"></textarea>
                    <div id="message-feedback" class="invalid-feedback text-danger fw-bold" style="color: red;">
                    </div>
                </div>
                <button type="submit" submit-form-id="contact" loading-text="Sending..."
                    class="btn btn-primary">Send
                    Message</button>
            </form>
        </div>
    </section>

    <section class="cta">
        <div class="cta-content">
            <h2>Ready for Your Next Great Haircut?</h2>
            <p>Book an appointment in seconds and experience the BSharp difference</p>
            <a href="{{ route('register') }}" class="btn btn-primary">Book Appointment Now</a>
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
                    <a href="#how-it-works">How It Works</a>
                    <a href="#app-features">Features</a>
                    <a href="#about">About</a>
                    <a href="{{ route('services') }}">Services</a>
                    <a href="#contact">Contact</a>
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
    <script src="{{ url('core/vendor/js/datatables.bundle.js') }}"></script>
    <script src="{{ asset('core/global/Launcher.js') }}" type="module"></script>

    <script>
        // Mobile menu toggle
        document.querySelector('.menu-toggle').addEventListener('click', function() {
            document.querySelector('.nav-links').classList.toggle('active');
            document.querySelector('.auth-buttons').classList.toggle('active');
        });

        // Testimonial slider
        const dots = document.querySelectorAll('.dot');
        const testimonials = document.querySelectorAll('.testimonial-card');

        dots.forEach((dot, index) => {
            dot.addEventListener('click', () => {
                // Remove active class from all dots and testimonials
                dots.forEach(d => d.classList.remove('active'));
                testimonials.forEach(t => t.classList.remove('active'));

                // Add active class to current dot and testimonial
                dot.classList.add('active');
                testimonials[index].classList.add('active');
            });
        });

        // Auto-rotate testimonials
        let currentTestimonial = 0;

        function rotateTestimonials() {
            testimonials.forEach(t => t.classList.remove('active'));
            dots.forEach(d => d.classList.remove('active'));

            currentTestimonial = (currentTestimonial + 1) % testimonials.length;

            testimonials[currentTestimonial].classList.add('active');
            dots[currentTestimonial].classList.add('active');
        }

        // Set first testimonial as active
        testimonials[0].classList.add('active');

        // Rotate testimonials every 5 seconds
        setInterval(rotateTestimonials, 5000);
    </script>

    <style>
        :root {
            --primary-color: #d4af37;
            /* Gold color */
            --secondary-color: #333;
            --accent-color: #d4af37;
            /* Gold color */
        }

        /* Hero section styling for welcome page */
        .hero {
            min-height: 100vh;
            background: linear-gradient(135deg, rgba(30, 30, 30, 0.9) 0%, rgba(30, 30, 30, 0.8) 100%), url('https://images.unsplash.com/photo-1503951914875-452162b0f3f1?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            color: var(--light-text);
            position: relative;
            display: flex;
            flex-direction: column;
        }

        /* Update navbar styling */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
        }

        .logo a {
            text-decoration: none;
        }

        .logo-highlight {
            color: var(--primary-color);
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: #c09c30;
            /* Slightly darker gold for hover */
            border-color: #c09c30;
        }

        .btn-outline {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-outline:hover {
            background-color: var(--primary-color);
            color: white;
        }

        /* Update other gold-colored elements */
        .section-divider {
            background-color: var(--primary-color);
        }

        .step-number {
            background-color: var(--primary-color);
        }

        .feature-icon {
            color: var(--primary-color);
        }

        .dot.active {
            background-color: var(--primary-color);
        }
    </style>
</body>

</html>
