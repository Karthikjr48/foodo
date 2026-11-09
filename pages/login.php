<?php
$root = '../';
$pageTitle = 'Login | Foodo';
require_once '../includes/header.php';
require_once '../includes/csrf.php';
?>

    <!-- ==================== AUTH SECTION ==================== -->
    <section class="section auth-section">
        <div class="container auth-container">
            <div class="auth-card">

                <div class="auth-header" style="margin-bottom: 20px;">
                    <i class="fa-solid fa-utensils auth-icon"></i>
                    <h1>Welcome Back</h1>
                    <p>Log in to continue ordering your favorite food.</p>
                </div>

                <!-- Custom Switch Tabs -->
                <div class="auth-tabs" style="display: flex; border-bottom: 1px solid var(--color-glass-border); margin-bottom: 25px;">
                    <button type="button" class="auth-tab active" id="tabPassword" style="flex: 1; padding: 12px; background: none; border: none; border-bottom: 2px solid var(--color-primary); color: #fff; font-family: var(--font-heading); font-weight: 600; cursor: pointer; transition: all 0.3s; outline: none; font-size: 0.95rem;">Password Login</button>
                    <button type="button" class="auth-tab" id="tabOTP" style="flex: 1; padding: 12px; background: none; border: none; border-bottom: 2px solid transparent; color: var(--color-text-muted); font-family: var(--font-heading); font-weight: 600; cursor: pointer; transition: all 0.3s; outline: none; font-size: 0.95rem;">OTP Login</button>
                </div>

                <!-- Global Notifications -->
                <div class="auth-note error" id="authErrorMsg" style="display: none;">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span id="authErrorText"></span>
                </div>
                <div class="auth-note" id="authSuccessMsg" style="display: none; border-left: 4px solid var(--color-success); background: rgba(0, 214, 143, 0.05); color: var(--color-success);">
                    <i class="fa-solid fa-circle-check"></i>
                    <span id="authSuccessText"></span>
                </div>

                <?php if (isset($_GET['error'])): ?>
                    <div class="auth-note error" id="phpErrorMsg">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        <?php
                            if ($_GET['error'] === 'invalid') {
                                echo 'Incorrect email or password. Please try again.';
                            } elseif ($_GET['error'] === 'login_required') {
                                echo 'Please login to add items to your cart.';
                            } else {
                                echo 'Something went wrong. Please try again.';
                            }
                        ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['registered'])): ?>
                    <div class="auth-note" id="phpSuccessMsg">
                        <i class="fa-solid fa-circle-check"></i>
                        Account created successfully! Please log in below.
                    </div>
                <?php endif; ?>

                <form class="auth-form" id="loginForm" action="../includes/login_process.php" method="post">
                    <?php csrfInput(); ?>
                    <div class="form-group">
                        <label for="loginEmail">Email or Username</label>
                        <input type="text" id="loginEmail" name="email" placeholder="Enter email or username" required>
                    </div>

                    <div class="form-group">
                        <label for="loginPassword">Password</label>
                        <div class="password-wrap">
                            <input type="password" id="loginPassword" name="password" placeholder="Enter your password" required>
                            <button type="button" class="toggle-password" aria-label="Show password">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="auth-options">
                        <label class="checkbox-label">
                            <input type="checkbox" name="remember">
                            <span>Remember me</span>
                        </label>
                        <a href="forgot-password.php" class="forgot-link">Forgot Password?</a>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">Login</button>
                </form>

                <!-- Form 2: OTP Login Form (Hidden by default) -->
                <form class="auth-form" id="otpLoginForm" style="display: none;" onsubmit="return false;">
                    <div class="form-group">
                        <label for="otpEmail">Email Address</label>
                        <div style="display: flex; gap: 10px;">
                            <input type="email" id="otpEmail" placeholder="Enter your email" style="flex: 1;">
                            <button type="button" id="btnSendOtp" class="btn btn-primary" style="padding: 10px 20px; font-size: 0.85rem; border-radius: 6px; font-weight: 600; width: auto; margin: 0; white-space: nowrap;">Send OTP</button>
                        </div>
                    </div>

                    <!-- OTP Code input row, initially hidden, slides down on success -->
                    <div class="form-group" id="otpCodeGroup" style="display: none;">
                        <label for="otpCodeInput">Verification Code</label>
                        <input type="text" id="otpCodeInput" maxlength="6" placeholder="Enter 6-digit OTP code" style="font-family: monospace; font-size: 1.2rem; letter-spacing: 0.2em; text-align: center;">
                        <p style="margin: 8px 0 0 0; font-size: 0.8rem; color: var(--color-text-muted); text-align: right;" id="resendTimerContainer">
                            Resend code in <span id="resendCountdown" style="color: var(--color-primary); font-weight: 600;">30</span>s
                        </p>
                    </div>

                    <button type="button" id="btnVerifyOtp" class="btn btn-primary btn-block" style="display: none;">Verify & Login</button>
                </form>

                <p class="auth-switch">Don't have an account? <a href="register.php">Sign Up</a></p>

                <!-- JS logic for tab toggling and AJAX OTP integration -->
                <script>
                document.addEventListener('DOMContentLoaded', () => {
                    const tabPassword = document.getElementById('tabPassword');
                    const tabOTP = document.getElementById('tabOTP');
                    const loginForm = document.getElementById('loginForm');
                    const otpLoginForm = document.getElementById('otpLoginForm');
                    
                    const phpErrorMsg = document.getElementById('phpErrorMsg');
                    const phpSuccessMsg = document.getElementById('phpSuccessMsg');
                    const authErrorMsg = document.getElementById('authErrorMsg');
                    const authErrorText = document.getElementById('authErrorText');
                    const authSuccessMsg = document.getElementById('authSuccessMsg');
                    const authSuccessText = document.getElementById('authSuccessText');

                    const btnSendOtp = document.getElementById('btnSendOtp');
                    const btnVerifyOtp = document.getElementById('btnVerifyOtp');
                    const otpEmail = document.getElementById('otpEmail');
                    const otpCodeGroup = document.getElementById('otpCodeGroup');
                    const otpCodeInput = document.getElementById('otpCodeInput');
                    
                    const resendCountdown = document.getElementById('resendCountdown');
                    const resendTimerContainer = document.getElementById('resendTimerContainer');

                    let resendTimer = null;

                    // Tab Switching Logic
                    tabPassword.addEventListener('click', () => {
                        tabPassword.className = 'auth-tab active';
                        tabOTP.className = 'auth-tab';
                        tabPassword.style.borderBottomColor = 'var(--color-primary)';
                        tabPassword.style.color = '#fff';
                        tabOTP.style.borderBottomColor = 'transparent';
                        tabOTP.style.color = 'var(--color-text-muted)';
                        
                        loginForm.style.display = 'block';
                        otpLoginForm.style.display = 'none';
                        clearNotifications();
                    });

                    tabOTP.addEventListener('click', () => {
                        tabOTP.className = 'auth-tab active';
                        tabPassword.className = 'auth-tab';
                        tabOTP.style.borderBottomColor = 'var(--color-primary)';
                        tabOTP.style.color = '#fff';
                        tabPassword.style.borderBottomColor = 'transparent';
                        tabPassword.style.color = 'var(--color-text-muted)';
                        
                        otpLoginForm.style.display = 'block';
                        loginForm.style.display = 'none';
                        clearNotifications();
                    });

                    function clearNotifications() {
                        if (phpErrorMsg) phpErrorMsg.style.display = 'none';
                        if (phpSuccessMsg) phpSuccessMsg.style.display = 'none';
                        authErrorMsg.style.display = 'none';
                        authSuccessMsg.style.display = 'none';
                    }

                    // Send OTP logic
                    btnSendOtp.addEventListener('click', () => {
                        const emailVal = otpEmail.value.trim();
                        if (emailVal === '') {
                            showError('Please enter a valid email address.');
                            return;
                        }

                        clearNotifications();
                        btnSendOtp.disabled = true;
                        btnSendOtp.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Sending...';

                        fetch('../includes/send_otp.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ email: emailVal })
                        })
                        .then(res => res.json())
                        .then(data => {
                            btnSendOtp.innerHTML = 'Send OTP';
                            if (data.success) {
                                showSuccess(data.message);
                                otpCodeGroup.style.display = 'block';
                                btnVerifyOtp.style.display = 'block';
                                otpEmail.disabled = true;
                                startResendTimer();
                            } else {
                                btnSendOtp.disabled = false;
                                showError(data.error);
                            }
                        })
                        .catch(err => {
                            btnSendOtp.disabled = false;
                            btnSendOtp.innerHTML = 'Send OTP';
                            showError('An error occurred. Please try again.');
                            console.error(err);
                        });
                    });

                    // Verify OTP and Login
                    btnVerifyOtp.addEventListener('click', () => {
                        const otpVal = otpCodeInput.value.trim();
                        if (otpVal.length !== 6 || isNaN(otpVal)) {
                            showError('Please enter the 6-digit numeric OTP verification code.');
                            return;
                        }

                        clearNotifications();
                        btnVerifyOtp.disabled = true;
                        btnVerifyOtp.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Verifying...';

                        fetch('../includes/verify_otp.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ email: otpEmail.value.trim(), otp: otpVal })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                showSuccess(data.message);
                                setTimeout(() => {
                                    window.location.href = '../index.php';
                                }, 1500);
                            } else {
                                btnVerifyOtp.disabled = false;
                                btnVerifyOtp.innerHTML = 'Verify & Login';
                                showError(data.error);
                            }
                        })
                        .catch(err => {
                            btnVerifyOtp.disabled = false;
                            btnVerifyOtp.innerHTML = 'Verify & Login';
                            showError('Authentication failed. Please check your network and try again.');
                            console.error(err);
                        });
                    });

                    function startResendTimer() {
                        let count = 30;
                        resendTimerContainer.innerHTML = 'Resend code in <span id="resendCountdown" style="color:var(--color-primary); font-weight:600;">30</span>s';
                        
                        clearInterval(resendTimer);
                        resendTimer = setInterval(() => {
                            count--;
                            const timerSpan = document.getElementById('resendCountdown');
                            if (timerSpan) timerSpan.textContent = count;

                            if (count <= 0) {
                                clearInterval(resendTimer);
                                resendTimerContainer.innerHTML = '<a href="#" id="lnkResend" style="color: var(--color-primary); font-weight:600; text-decoration:none;">Resend OTP</a>';
                                document.getElementById('lnkResend').addEventListener('click', (e) => {
                                    e.preventDefault();
                                    btnSendOtp.disabled = false;
                                    btnSendOtp.click();
                                });
                            }
                        }, 1000);
                    }

                    function showError(msg) {
                        authErrorText.textContent = msg;
                        authErrorMsg.style.display = 'block';
                    }

                    function showSuccess(msg) {
                        authSuccessText.textContent = msg;
                        authSuccessMsg.style.display = 'block';
                    }
                });
                </script>
            </div>
        </div>
    </section>


<?php
require_once '../includes/footer.php';
?>
