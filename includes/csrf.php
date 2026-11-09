<?php
/**
 * csrf.php
 * -------------------------------------------------------
 * Reusable CSRF (Cross-Site Request Forgery) protection helper.
 * Generates, embeds, and verifies secure cryptographic tokens.
 * -------------------------------------------------------
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Generates a cryptographically secure token and stores it in the session.
 * @return string The generated CSRF token.
 */
function getCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        // Generate a 32-byte secure random string converted to hexadecimal
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Outputs a hidden input field containing the CSRF token.
 */
function csrfInput() {
    echo '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(getCSRFToken(), ENT_QUOTES, 'UTF-8') . '">';
}

/**
 * Verifies if the submitted CSRF token matches the session token.
 * Uses a timing-attack resistant comparison function (hash_equals).
 * @param string $token The token submitted via form.
 * @return bool True if valid, false otherwise.
 */
function verifyCSRFToken($token) {
    if (!isset($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Validates post requests immediately and halts execution on failure.
 */
function validateCSRFOrDie() {
    $token = $_POST['csrf_token'] ?? '';
    if (!verifyCSRFToken($token)) {
        http_response_code(403);
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <title>Forbidden | CSRF Security Verification Failed</title>
            <style>
                body { background-color: #0A0A0F; color: #F0F0F0; font-family: system-ui, sans-serif; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; }
                .card { background-color: #12121A; border: 1px solid rgba(255,255,255,0.08); border-radius: 16px; padding: 40px; max-width: 500px; text-align: center; box-shadow: 0 16px 48px rgba(0,0,0,0.4); }
                .icon { font-size: 3rem; color: #FF4757; margin-bottom: 20px; }
                h1 { margin: 0 0 10px 0; font-size: 1.6rem; color: #fff; }
                p { color: #8A8A9A; line-height: 1.5; font-size: 0.95rem; margin-bottom: 25px; }
                .btn { background: #FF6B35; color: #000; text-decoration: none; padding: 10px 20px; border-radius: 6px; font-weight: bold; font-size: 0.9rem; }
            </style>
        </head>
        <body>
            <div class="card">
                <div class="icon">⚠️</div>
                <h1>Security Verification Failed</h1>
                <p>The form submission was blocked because the secure request verification token (CSRF) was invalid or missing.</p>
                <a href="javascript:history.back()" class="btn">Go Back</a>
            </div>
        </body>
        </html>
        <?php
        exit();
    }
}
?>
