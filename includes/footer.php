<?php
/**
 * footer.php
 * -------------------------------------------------------
 * Shared footer, included at the bottom of every PHP page.
 * Uses the same $root variable set before header.php was included.
 * -------------------------------------------------------
 */
if (!isset($root)) {
    $root = '';
}
?>
    <!-- ==================== FOOTER ==================== -->
    <footer class="footer">
        <div class="container footer-grid">
            <div class="footer-col">
                <a href="<?php echo $root; ?>index.php" class="logo">
                    <img src="<?php echo $root; ?>images/logo.svg" alt="Foodo Logo" class="logo-svg"> Foo<span>do</span>
                </a>
                <p>Bringing your favorite meals to your doorstep, fresh and fast, every single day.</p>
                <p><i class="fa-solid fa-location-dot"></i> Loyola College, Nungambakkam, Chennai - 600034</p>
                <p><i class="fa-solid fa-phone"></i> +91 9042655027</p>
                <p><i class="fa-solid fa-envelope"></i> support@foodo.cu.ma</p>
                <div class="social-icons">
                    <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="https://www.instagram.com/karthikjr__11" target="_blank"><i class="fa-brands fa-instagram"></i></a>
                    <a href="https://x.com/karthikjr_11" target="_blank"><i class="fa-brands fa-twitter"></i></a>
                    <a href="#"><i class="fa-brands fa-youtube"></i></a>
                </div>
            </div>
            <div class="footer-col">
                <h4>Quick Links</h4>
                <a href="<?php echo $root; ?>index.php">Home</a>
                <a href="<?php echo $root; ?>pages/menu.php">Menu</a>
                <a href="<?php echo $root; ?>pages/about.php">About Us</a>
                <a href="<?php echo $root; ?>pages/contact.php">Contact Us</a>
                <a href="<?php echo $root; ?>pages/terms.php">Terms of Service</a>
                <a href="<?php echo $root; ?>pages/privacy.php">Privacy Policy</a>
                <a href="<?php echo $root; ?>pages/refund.php">Refund Policy</a>
            </div>
            <div class="footer-col">
                <h4>Food Categories</h4>
                <a href="<?php echo $root; ?>pages/menu.php?category=pizza">Pizza</a>
                <a href="<?php echo $root; ?>pages/menu.php?category=biryani">Biryani</a>
                <a href="<?php echo $root; ?>pages/menu.php?category=chinese">Chinese</a>
                <a href="<?php echo $root; ?>pages/menu.php?category=desserts">Desserts</a>
                <a href="<?php echo $root; ?>pages/menu.php?category=soft-drinks">Beverages</a>
            </div>
            <div class="footer-col">
                <h4>Stay Updated</h4>
                <p>Get the latest offers and deals delivered to your inbox.</p>
                <form class="footer-newsletter" onsubmit="event.preventDefault(); this.querySelector('button').textContent='Subscribed! ✓'">
                    <input type="email" placeholder="Your email" required>
                    <button type="submit" class="btn btn-primary">Subscribe</button>
                </form>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2026 Foodo. All rights reserved. | B.Sc. Computer Science Project</p>
        </div>
    </footer>

    <script src="<?php echo $root; ?>js/main.js?v=2.4"></script>
</body>
</html>
