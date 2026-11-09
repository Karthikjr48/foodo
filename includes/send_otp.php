<?php
/**
 * send_otp.php
 * -------------------------------------------------------
 * AJAX endpoint to generate and send an OTP code to a user.
 * -------------------------------------------------------
 */

header('Content-Type: application/json');

require_once 'db_connect.php';
require_once 'config.php';
require_once 'notify.php'; // imports PHPMailer and SMTP configurations

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Read input (support JSON or standard POST)
$input = json_decode(file_get_contents('php://input'), true);
$emailOrUsername = trim($input['email'] ?? $_POST['email'] ?? '');

if (empty($emailOrUsername)) {
    echo json_encode(['success' => false, 'error' => 'Please enter your email or username.']);
    exit();
}

// Find user by email or username
$stmt = $conn->prepare("SELECT user_id, full_name, email FROM users WHERE email = ? OR phone = ?");
$stmt->bind_param("ss", $emailOrUsername, $emailOrUsername);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user) {
    echo json_encode(['success' => false, 'error' => 'No account found with this email.']);
    exit();
}

$userId = $user['user_id'];
$customerName = $user['full_name'];
$userEmail = $user['email'];

// Generate 6-digit OTP code
$otpCode = sprintf("%06d", mt_rand(100000, 999999));

// Store OTP in database with 5-minute expiry
$updateStmt = $conn->prepare("UPDATE users SET otp_code = ?, otp_expiry = DATE_ADD(NOW(), INTERVAL 5 MINUTE) WHERE user_id = ?");
$updateStmt->bind_param("si", $otpCode, $userId);
$updateStmt->execute();
$updateStmt->close();

// Send OTP via PHPMailer
$mail = new \PHPMailer\PHPMailer\PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host       = MAIL_HOST;
    $mail->SMTPAuth   = true;
    $mail->Username   = MAIL_USERNAME;
    $mail->Password   = MAIL_PASSWORD;
    $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = MAIL_PORT;

    $mail->CharSet = 'UTF-8';
    $mail->setFrom(MAIL_FROM_EMAIL, MAIL_FROM_NAME);
    $mail->addAddress($userEmail, $customerName);

    $mail->isHTML(true);
    $mail->Subject = '🔑 Login OTP Verification Code - Foodo';
    
    $mail->Body = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <title>Login OTP Code</title>
        <style>
            body { background-color: #000000; color: #F0F0F0; font-family: system-ui, sans-serif; padding: 20px; margin: 0; }
            .card { background-color: #0F0F15; border: 1px solid rgba(255,255,255,0.08); border-radius: 16px; padding: 40px; max-width: 500px; margin: 40px auto; text-align: center; box-shadow: 0 8px 32px rgba(0,0,0,0.5); }
            h2 { color: #FFD166; font-size: 22px; font-weight: 800; margin: 0 0 15px 0; }
            .description { color: #8A8A9A; font-size: 15px; line-height: 1.6; margin-bottom: 25px; }
            .otp-box { background: rgba(255, 107, 53, 0.08); border: 2px dashed #FF6B35; border-radius: 12px; font-size: 2.2rem; font-weight: 800; color: #FF6B35; letter-spacing: 0.15em; padding: 15px; margin: 25px auto; max-width: 250px; font-family: monospace; }
            .footer { border-top: 1px solid rgba(255,255,255,0.08); padding-top: 25px; margin-top: 35px; color: #8A8A9A; font-size: 12px; }
        </style>
    </head>
    <body>
        <div class='card'>
            <h2>Hi $customerName,</h2>
            <p class='description'>Use the verification code below to login to your Foodo account. This code is valid for 5 minutes.</p>
            
            <div class='otp-box'>$otpCode</div>
            
            <p style='color: #8A8A9A; font-size: 0.85rem; margin-top: 20px;'>If you did not request this login code, you can safely ignore this email.</p>
            
            <div class='footer'>
                <p>Foodo Corporation &bull; support@foodo.cu.ma</p>
            </div>
        </div>
    </body>
    </html>";

    $mail->send();
    
    // Save email in session temporarily for verifying later
    $_SESSION['otp_pending_email'] = $userEmail;

    echo json_encode(['success' => true, 'message' => 'OTP sent successfully! Please check your email.']);
} catch (\Exception $e) {
    error_log('Login OTP Email Error: ' . $mail->ErrorInfo);
    
    // Fallback to PHP native mail() (useful if SMTP ports are blocked on hosting)
    $subject = '🔑 Login OTP Verification Code - Foodo';
    $body = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <title>Login OTP Code</title>
        <style>
            body { background-color: #000000; color: #F0F0F0; font-family: system-ui, sans-serif; padding: 20px; margin: 0; }
            .card { background-color: #0F0F15; border: 1px solid rgba(255,255,255,0.08); border-radius: 16px; padding: 40px; max-width: 500px; margin: 40px auto; text-align: center; box-shadow: 0 8px 32px rgba(0,0,0,0.5); }
            h2 { color: #FFD166; font-size: 22px; font-weight: 800; margin: 0 0 15px 0; }
            .description { color: #8A8A9A; font-size: 15px; line-height: 1.6; margin-bottom: 25px; }
            .otp-box { background: rgba(255, 107, 53, 0.08); border: 2px dashed #FF6B35; border-radius: 12px; font-size: 2.2rem; font-weight: 800; color: #FF6B35; letter-spacing: 0.15em; padding: 15px; margin: 25px auto; max-width: 250px; font-family: monospace; }
            .footer { border-top: 1px solid rgba(255,255,255,0.08); padding-top: 25px; margin-top: 35px; color: #8A8A9A; font-size: 12px; }
        </style>
    </head>
    <body>
        <div class='card'>
            <h2>Hi $customerName,</h2>
            <p class='description'>Use the verification code below to login to your Foodo account. This code is valid for 5 minutes.</p>
            
            <div class='otp-box'>$otpCode</div>
            
            <p style='color: #8A8A9A; font-size: 0.85rem; margin-top: 20px;'>If you did not request this login code, you can safely ignore this email.</p>
            
            <div class='footer'>
                <p>Foodo Corporation &bull; support@foodo.cu.ma</p>
            </div>
        </div>
    </body>
    </html>";

    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= "From: " . MAIL_FROM_NAME . " <" . MAIL_FROM_EMAIL . ">\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();
    
    if (@mail($userEmail, $subject, $body, $headers)) {
        $_SESSION['otp_pending_email'] = $userEmail;
        echo json_encode(['success' => true, 'message' => 'OTP sent successfully! Please check your email.']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to send verification email: ' . $mail->ErrorInfo]);
    }
}

$conn->close();
?>
