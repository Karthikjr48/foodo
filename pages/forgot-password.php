<?php
/**
 * forgot-password.php
 * -------------------------------------------------------
 * Foodo — Password Reset Request Page
 * -------------------------------------------------------
 */
$root = '../';
$pageTitle = 'Forgot Password | Foodo';
require_once '../includes/db_connect.php';
require_once '../includes/header.php';
require_once '../includes/notify.php';

$message = '';
$messageType = '';
$emailSentSuccess = false;
$submittedEmail = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $submittedEmail = $email;

    if (empty($email)) {
        $message = 'Please enter your email address.';
        $messageType = 'error';
    } else {
        // Check if user exists
        $stmt = $conn->prepare("SELECT user_id, full_name, phone FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result();
        $user = $res->fetch_assoc();
        $stmt->close();

        if ($user) {
            // Generate a secure 64-character token
            $token = bin2hex(random_bytes(32));
            // Expire in 1 hour
            $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

            // Store in DB
            $updateStmt = $conn->prepare("UPDATE users SET reset_token = ?, reset_expiry = ? WHERE user_id = ?");
            $updateStmt->bind_param("ssi", $token, $expiry, $user['user_id']);
            $updateStmt->execute();
            $updateStmt->close();

            // Build reset link using the server host dynamically
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
            $domainName = $_SERVER['HTTP_HOST'];
            
            // Auto detect if path has /food-ordering/ subfolder (local) or root (live host)
            $subFolder = (strpos($_SERVER['REQUEST_URI'], '/food-ordering/') !== false) ? '/food-ordering' : '';
            $resetLink = $protocol . $domainName . $subFolder . "/pages/reset-password.php?token=" . $token . "&email=" . urlencode($email);

            // Send Email
            $emailSent = sendPasswordResetEmail($email, $user['full_name'], $resetLink);
            
            // Send SMS
            $smsSent = sendPasswordResetSMS($user['phone'], $resetLink);

            if ($emailSent || $smsSent) {
                $emailSentSuccess = true;
            } else {
                $message = 'Unable to send password reset link via Email or SMS.';
                $messageType = 'error';
            }
        } else {
            $message = 'This email address is not registered with us.';
            $messageType = 'error';
        }
    }
}
?>

    <!-- ==================== AUTH SECTION ==================== -->
    <section class="section auth-section">
        <div class="container auth-container">
            <div class="auth-card">

                <?php if (!$emailSentSuccess): ?>
                    <!-- STEP 1: Request Reset Link -->
                    <div id="requestStep">
                        <div class="auth-header">
                            <i class="fa-solid fa-key auth-icon"></i>
                            <h1>Forgot Password?</h1>
                            <p>Enter your registered email and we will send you a link to reset your password.</p>
                        </div>

                        <?php if (!empty($message)): ?>
                            <div class="auth-note <?= $messageType ?>">
                                <i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($message) ?>
                            </div>
                        <?php endif; ?>

                        <form class="auth-form" action="forgot-password.php" method="post">
                            <div class="form-group">
                                <label for="forgotEmail">Email Address</label>
                                <input type="email" id="forgotEmail" name="email" value="<?= htmlspecialchars($submittedEmail) ?>" placeholder="you@example.com" required>
                            </div>

                            <button type="submit" class="btn btn-primary btn-block">Send Reset Link</button>
                        </form>

                        <p class="auth-switch">Remembered your password? <a href="login.php">Back to Login</a></p>
                    </div>
                <?php else: ?>
                    <!-- STEP 2: Confirmation -->
                    <div id="confirmStep">
                        <div class="auth-header">
                            <i class="fa-solid fa-paper-plane auth-icon success"></i>
                            <h1>Reset Link Sent!</h1>
                            <p>We have sent a password reset link to your email <strong><?= htmlspecialchars($submittedEmail) ?></strong> and your registered mobile phone via SMS. Please check your inbox and messages.</p>
                        </div>

                        <a href="login.php" class="btn btn-primary btn-block">Back to Login</a>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </section>

<?php require_once '../includes/footer.php'; ?>
