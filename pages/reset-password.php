<?php
/**
 * reset-password.php
 * -------------------------------------------------------
 * Foodo — Password Reset Completion Page
 * -------------------------------------------------------
 */
$root = '../';
$pageTitle = 'Reset Password | Foodo';
require_once '../includes/db_connect.php';
require_once '../includes/header.php';

$message = '';
$messageType = '';
$tokenValid = false;
$resetSuccess = false;

// Get URL parameters
$token = $_GET['token'] ?? '';
$email = $_GET['email'] ?? '';

if (empty($token) || empty($email)) {
    $message = 'Invalid password reset request. Missing token or email.';
    $messageType = 'error';
} else {
    // Verify token and expiry in DB
    $stmt = $conn->prepare("SELECT user_id, reset_expiry FROM users WHERE email = ? AND reset_token = ?");
    $stmt->bind_param("ss", $email, $token);
    $stmt->execute();
    $res = $stmt->get_result();
    $user = $res->fetch_assoc();
    $stmt->close();

    if ($user) {
        $expiryTime = strtotime($user['reset_expiry']);
        $currentTime = time();

        if ($currentTime < $expiryTime) {
            $tokenValid = true;
        } else {
            $message = 'This password reset link has expired. Please request a new one.';
            $messageType = 'error';
        }
    } else {
        $message = 'Invalid password reset link or token mismatch.';
        $messageType = 'error';
    }
}

// Handle form submission
if ($tokenValid && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (strlen($password) < 6) {
        $message = 'Password must be at least 6 characters long.';
        $messageType = 'error';
    } elseif ($password !== $confirmPassword) {
        $message = 'Passwords do not match.';
        $messageType = 'error';
    } else {
        // Hash the new password using BCRYPT
        $newHash = password_hash($password, PASSWORD_BCRYPT);

        // Update database and clear the token
        $updateStmt = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expiry = NULL WHERE email = ?");
        $updateStmt->bind_param("ss", $newHash, $email);
        $updateStmt->execute();
        $updateStmt->close();

        $resetSuccess = true;
        $tokenValid = false; // Don't show the form anymore
    }
}
?>

    <!-- ==================== AUTH SECTION ==================== -->
    <section class="section auth-section">
        <div class="container auth-container">
            <div class="auth-card">

                <?php if ($resetSuccess): ?>
                    <!-- Success Step -->
                    <div class="auth-header">
                        <i class="fa-solid fa-circle-check auth-icon success"></i>
                        <h1>Password Reset!</h1>
                        <p>Your password has been successfully updated. You can now log in with your new password.</p>
                        <p style="margin-top: 15px; font-size: 0.85rem; color: var(--color-text-muted);">
                            Redirecting to login page in <span id="countdown" style="font-weight:bold; color:var(--color-primary);">5</span> seconds...
                        </p>
                    </div>
                    <a href="login.php" class="btn btn-primary btn-block">Go to Login</a>

                    <script>
                        let count = 5;
                        const countdownEl = document.getElementById('countdown');
                        const timer = setInterval(() => {
                            count--;
                            countdownEl.textContent = count;
                            if (count <= 0) {
                                clearInterval(timer);
                                window.location.href = 'login.php';
                            }
                        }, 1000);
                    </script>

                <?php else: ?>
                    <div class="auth-header">
                        <i class="fa-solid fa-lock-open auth-icon"></i>
                        <h1>Reset Password</h1>
                        <p>Please enter and confirm your new password below.</p>
                    </div>

                    <?php if (!empty($message)): ?>
                        <div class="auth-note <?= $messageType ?>">
                            <i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($message) ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($tokenValid): ?>
                        <form class="auth-form" action="reset-password.php?token=<?= htmlspecialchars($token) ?>&email=<?= urlencode($email) ?>" method="post">
                            <div class="form-group">
                                <label for="password">New Password</label>
                                <div class="password-wrap">
                                    <input type="password" id="password" name="password" placeholder="At least 6 characters" required>
                                    <button type="button" class="toggle-password" aria-label="Show password">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="confirm_password">Confirm New Password</label>
                                <div class="password-wrap">
                                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Re-enter password" required>
                                    <button type="button" class="toggle-password" aria-label="Show password">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary btn-block">Update Password</button>
                        </form>
                    <?php endif; ?>

                    <p class="auth-switch"><a href="login.php">Cancel and return to Login</a></p>
                <?php endif; ?>

            </div>
        </div>
    </section>

<?php require_once '../includes/footer.php'; ?>
