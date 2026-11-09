<?php
$root = '../';
$pageTitle = 'Contact Us | Foodo';
require_once '../includes/header.php';
require_once '../includes/csrf.php';
?>

    <!-- ==================== PAGE HEADER ==================== -->
    <section class="page-header">
        <div class="container">
            <span class="eyebrow">Get In Touch</span>
            <h1>We'd Love to Hear From You</h1>
            <p>Questions, feedback, or a catering enquiry — reach out and our team will get back within 24 hours.</p>
        </div>
    </section>

    <!-- ==================== CONTACT SECTION ==================== -->
    <section class="section contact-section">
        <div class="container contact-layout">

            <!-- Contact Info Cards -->
            <div class="contact-info">
                <div class="contact-info-card">
                    <i class="fa-solid fa-phone"></i>
                    <div>
                        <h4>Call Us</h4>
                        <p>+91 9042655027</p>
                        <p>Mon - Sun, 9 AM - 11 PM</p>
                    </div>
                </div>
                <div class="contact-info-card">
                    <i class="fa-solid fa-envelope"></i>
                    <div>
                        <h4>Email Us</h4>
                        <p>support@foodo.cu.ma</p>
                        <p>We reply within 24 hours</p>
                    </div>
                </div>
                <div class="contact-info-card">
                    <i class="fa-solid fa-location-dot"></i>
                    <div>
                        <h4>Visit Us</h4>
                        <p>Loyola College, Nungambakkam,</p>
                        <p>Chennai - 600034</p>
                    </div>
                </div>

                <div class="social-block">
                    <h4>Follow Us</h4>
                    <div class="social-icons">
                        <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="https://www.instagram.com/karthikjr__11" target="_blank"><i class="fa-brands fa-instagram"></i></a>
                        <a href="https://x.com/karthikjr_11" target="_blank"><i class="fa-brands fa-twitter"></i></a>
                        <a href="#"><i class="fa-brands fa-youtube"></i></a>
                    </div>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="form-card contact-form-card">
                <h3><i class="fa-solid fa-message"></i> Send Us a Message</h3>

                <?php if (isset($_GET['sent'])): ?>
                    <div class="auth-note"><i class="fa-solid fa-circle-check"></i> Message sent successfully! We'll get back to you within 24 hours.</div>
                <?php endif; ?>

                <form id="contactForm" action="../includes/contact_process.php" method="post">
                    <?php csrfInput(); ?>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="contactName">Your Name</label>
                            <input type="text" id="contactName" name="name" placeholder="Enter your name" required>
                        </div>
                        <div class="form-group">
                            <label for="contactEmail">Your Email</label>
                            <input type="email" id="contactEmail" name="email" placeholder="you@example.com" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="contactSubject">Subject</label>
                        <input type="text" id="contactSubject" name="subject" placeholder="What is this about?" required>
                    </div>

                    <div class="form-group">
                        <label for="contactMessage">Message</label>
                        <textarea id="contactMessage" name="message" rows="5" placeholder="Write your message here..." required></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Send Message <i class="fa-solid fa-paper-plane"></i></button>
                </form>
            </div>

        </div>

        <!-- Google Map Placeholder -->
        <div class="container">
            <div class="map-placeholder">
                <img src="https://placehold.co/1200x350/EFE6DA/6B6470?text=Google+Map+Placeholder+%E2%80%94+Foodo+Location" alt="Map showing Foodo location">
            </div>
        </div>
    </section>


<?php
require_once '../includes/footer.php';
?>
