<?php
/**
 * about.php
 * -------------------------------------------------------
 * About Us Page
 * Renders mission, vision, services, and team members.
 * -------------------------------------------------------
 */
$root = '../';
$pageTitle = 'About Us | Foodo';
require_once '../includes/header.php';
?>

    <!-- ==================== COMPANY INTRODUCTION ==================== -->
    <section class="hero about-hero">
        <div class="container hero-inner">
            <div class="hero-text fade-in-left">
                <span class="eyebrow">About Foodo</span>
                <h1>Serving happiness, <br>one <span>order</span> at a time.</h1>
                <p>Foodo started with a simple idea — great food shouldn't take forever to reach you. Since then, we've grown into a platform connecting food lovers with the dishes they crave, from home-style comfort food to restaurant favorites, delivered fresh and fast.</p>
            </div>
            <div class="hero-image fade-in-right">
                <img src="https://images.unsplash.com/photo-1556910103-1c02745aae4d?w=560&h=420&fit=crop" alt="Foodo kitchen">
            </div>
        </div>
    </section>

    <!-- ==================== MISSION & VISION ==================== -->
    <section class="section">
        <div class="container">
            <div class="mv-grid">
                <div class="mv-card fade-in-up">
                    <i class="fa-solid fa-bullseye"></i>
                    <h3>Our Mission</h3>
                    <p>To make quality food accessible to everyone, everywhere — by connecting local kitchens with hungry customers through fast, reliable delivery and a seamless ordering experience.</p>
                </div>
                <div class="mv-card fade-in-up" style="animation-delay: 0.15s;">
                    <i class="fa-solid fa-eye"></i>
                    <h3>Our Vision</h3>
                    <p>To become the most trusted food delivery platform, known for freshness, speed, and putting our customers and delivery partners first in everything we build.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ==================== SERVICES ==================== -->
    <section class="section services-section">
        <div class="container">
            <div class="section-heading fade-in-up">
                <h2>What We Offer</h2>
                <p>More than just delivery — a complete food experience.</p>
            </div>

            <div class="features-grid">
                <div class="feature-card fade-in-up">
                    <i class="fa-solid fa-motorcycle"></i>
                    <h3>Food Delivery</h3>
                    <p>Fast doorstep delivery from 500+ dishes across 18 categories.</p>
                </div>
                <div class="feature-card fade-in-up" style="animation-delay: 0.1s;">
                    <i class="fa-solid fa-store"></i>
                    <h3>Restaurant Partners</h3>
                    <p>We partner with trusted local kitchens to bring you authentic flavors.</p>
                </div>
                <div class="feature-card fade-in-up" style="animation-delay: 0.2s;">
                    <i class="fa-solid fa-champagne-glasses"></i>
                    <h3>Catering & Events</h3>
                    <p>Bulk orders and catering for parties, offices, and celebrations.</p>
                </div>
                <div class="feature-card fade-in-up" style="animation-delay: 0.3s;">
                    <i class="fa-solid fa-mobile-screen"></i>
                    <h3>Easy Ordering</h3>
                    <p>A simple, intuitive platform to browse, order, and track your food.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ==================== OUR TEAM ==================== -->
    <section class="section team-section">
        <div class="container">
            <div class="section-heading fade-in-up">
                <h2>Meet Our Team</h2>
                <p>The people working behind the scenes to feed you well.</p>
            </div>

            <div class="team-grid">
                <!-- Team Card 1 -->
                <div class="team-card fade-in-up">
                    <img src="https://placehold.co/240x240/FF4B3E/FFF8F0?text=K" alt="Karthik Jr">
                    <h4>Karthik Jr (Admin)</h4>
                    <span>24-UCS-047</span>
                    <div class="social-icons small">
                        <a href="https://www.instagram.com/karthikjr__11" target="_blank"><i class="fa-brands fa-instagram"></i></a>
                        <a href="https://x.com/karthikjr_11" target="_blank"><i class="fa-brands fa-twitter"></i></a>
                    </div>
                </div>

                <!-- Team Card 2 -->
                <div class="team-card fade-in-up" style="animation-delay: 0.1s;">
                    <img src="https://placehold.co/240x240/FFC107/1F1B24?text=A" alt="Ashwin">
                    <h4>Ashwin</h4>
                    <span>24-UCS-046</span>
                    <div class="social-icons small">
                        <a href="https://www.instagram.com/karthikjr__11" target="_blank"><i class="fa-brands fa-instagram"></i></a>
                        <a href="https://x.com/karthikjr_11" target="_blank"><i class="fa-brands fa-twitter"></i></a>
                    </div>
                </div>

                <!-- Team Card 3 -->
                <div class="team-card fade-in-up" style="animation-delay: 0.2s;">
                    <img src="https://placehold.co/240x240/2ECC71/FFF8F0?text=S" alt="Shalo">
                    <h4>Shalo</h4>
                    <span>24-UCS-045</span>
                    <div class="social-icons small">
                        <a href="https://www.instagram.com/karthikjr__11" target="_blank"><i class="fa-brands fa-instagram"></i></a>
                        <a href="https://x.com/karthikjr_11" target="_blank"><i class="fa-brands fa-twitter"></i></a>
                    </div>
                </div>

                <!-- Team Card 4 -->
                <div class="team-card fade-in-up" style="animation-delay: 0.3s;">
                    <img src="https://placehold.co/240x240/6B6470/FFF8F0?text=S" alt="Santhosh">
                    <h4>Santhosh</h4>
                    <span>24-UCS-048</span>
                    <div class="social-icons small">
                        <a href="https://www.instagram.com/karthikjr__11" target="_blank"><i class="fa-brands fa-instagram"></i></a>
                        <a href="https://x.com/karthikjr_11" target="_blank"><i class="fa-brands fa-twitter"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php require_once '../includes/footer.php'; ?>
