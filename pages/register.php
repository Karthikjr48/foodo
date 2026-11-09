<?php
$root = '../';
$pageTitle = 'Create Account | Foodo';
require_once '../includes/header.php';
require_once '../includes/csrf.php';
?>

    <!-- ==================== AUTH SECTION ==================== -->
    <section class="section auth-section">
        <div class="container auth-container">
            <div class="auth-card">

                <div class="auth-header">
                    <i class="fa-solid fa-user-plus auth-icon"></i>
                    <h1>Create Your Account</h1>
                    <p>Sign up to start ordering delicious food.</p>
                </div>

                <?php if (isset($_GET['error'])): ?>
                    <div class="auth-note error">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        <?php
                            if ($_GET['error'] === 'exists') {
                                echo 'An account with this email already exists. Please login instead.';
                            } else {
                                echo 'Something went wrong. Please check your details and try again.';
                            }
                        ?>
                    </div>
                <?php endif; ?>

                <form class="auth-form" id="registerForm" action="../includes/register_process.php" method="post">
                    <?php csrfInput(); ?>

                    <div class="form-group">
                        <label for="regName">Full Name</label>
                        <input type="text" id="regName" name="fullName" placeholder="Enter your full name" required>
                    </div>

                    <div class="form-group">
                        <label for="regEmail">Email Address</label>
                        <input type="email" id="regEmail" name="email" placeholder="you@example.com" required>
                    </div>

                    <div class="form-group">
                        <label for="regPhone">Phone Number</label>
                        <input type="tel" id="regPhone" name="phone" placeholder="10-digit mobile number" pattern="[0-9]{10}" maxlength="10" required>
                    </div>

                    <div class="form-group">
                        <label for="regPassword">Password</label>
                        <div class="password-wrap">
                            <input type="password" id="regPassword" name="password" placeholder="Create a password" minlength="6" required>
                            <button type="button" class="toggle-password" aria-label="Show password">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="regConfirmPassword">Confirm Password</label>
                        <div class="password-wrap">
                            <input type="password" id="regConfirmPassword" name="confirmPassword" placeholder="Re-enter your password" minlength="6" required>
                            <button type="button" class="toggle-password" aria-label="Show password">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                        <span class="field-error" id="passwordError">Passwords do not match</span>
                    </div>

                    <div class="auth-options single">
                        <label class="checkbox-label">
                            <input type="checkbox" name="terms" required>
                            <span>I agree to the <a href="#">Terms &amp; Conditions</a></span>
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">Create Account</button>
                </form>

                <p class="auth-switch">Already have an account? <a href="login.php">Login</a></p>
            </div>
        </div>
    </section>


<?php
require_once '../includes/footer.php';
?>
