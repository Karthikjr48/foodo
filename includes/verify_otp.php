<?php
/**
 * verify_otp.php
 * -------------------------------------------------------
 * AJAX endpoint to verify OTP code and authenticate the user session.
 * -------------------------------------------------------
 */

header('Content-Type: application/json');

require_once 'db_connect.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Read input (support JSON or standard POST)
$input = json_decode(file_get_contents('php://input'), true);
$email = trim($input['email'] ?? $_POST['email'] ?? $_SESSION['otp_pending_email'] ?? '');
$otpCode = trim($input['otp'] ?? $_POST['otp'] ?? '');

if (empty($email) || empty($otpCode)) {
    echo json_encode(['success' => false, 'error' => 'Please enter the verification code.']);
    exit();
}

// Fetch user and verify active OTP code with valid expiry date
$stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND otp_code = ? AND otp_expiry > NOW()");
$stmt->bind_param("ss", $email, $otpCode);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user) {
    echo json_encode(['success' => false, 'error' => 'Invalid or expired verification code.']);
    exit();
}

$userId = $user['user_id'];
$customerName = $user['full_name'];

// Clear the OTP fields in the database to prevent reuse
$clearStmt = $conn->prepare("UPDATE users SET otp_code = NULL, otp_expiry = NULL WHERE user_id = ?");
$clearStmt->bind_param("i", $userId);
$clearStmt->execute();
$clearStmt->close();

// Clear pending session variables
unset($_SESSION['otp_pending_email']);

// Authenticate session
$_SESSION['user_id'] = $userId;
$_SESSION['full_name'] = $customerName;
$_SESSION['email'] = $user['email'];

echo json_encode(['success' => true, 'message' => 'Login successful! Redirecting...']);

$conn->close();
?>
