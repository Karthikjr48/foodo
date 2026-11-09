<?php
/**
 * register_process.php
 * -------------------------------------------------------
 * Handles the Register form submission (pages/register.php).
 * Steps:
 *   1. Get form data safely
 *   2. Check if the email is already registered
 *   3. Hash the password (never store plain text passwords!)
 *   4. Insert the new user into the database
 *   5. Redirect to login page with a success message
 * -------------------------------------------------------
 */

require_once 'db_connect.php';
require_once 'rate_limit.php';
require_once 'csrf.php';

// Verify CSRF Token
validateCSRFOrDie();

// Only allow POST requests (form submissions), not direct URL visits
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/register.php');
    exit();
}

// ---- Get form data (trim removes extra spaces) ----
$fullName = trim($_POST['fullName']);
$email = trim($_POST['email']);
$phone = trim($_POST['phone']);
$password = $_POST['password'];
$confirmPassword = $_POST['confirmPassword'];

// ---- Basic server-side validation ----
// (The HTML form already validates most of this, but we NEVER trust
//  the browser alone — server-side checks are required for real security.)
if (empty($fullName) || empty($email) || empty($phone) || empty($password)) {
    header('Location: ../pages/register.php?error=invalid');
    exit();
}

if ($password !== $confirmPassword) {
    header('Location: ../pages/register.php?error=invalid');
    exit();
}

// ---- Check if email already exists ----
$checkStmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
$checkStmt->bind_param("s", $email);
$checkStmt->execute();
$checkStmt->store_result();

if ($checkStmt->num_rows > 0) {
    // Email already registered
    $checkStmt->close();
    $conn->close();
    header('Location: ../pages/register.php?error=exists');
    exit();
}
$checkStmt->close();

// ---- Hash the password securely ----
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// ---- Insert new user (using a prepared statement to prevent SQL injection) ----
$insertStmt = $conn->prepare(
    "INSERT INTO users (full_name, email, phone, password) VALUES (?, ?, ?, ?)"
);
$insertStmt->bind_param("ssss", $fullName, $email, $phone, $hashedPassword);

if ($insertStmt->execute()) {
    $insertStmt->close();
    $conn->close();
    header('Location: ../pages/login.php?registered=1');
    exit();
} else {
    $insertStmt->close();
    $conn->close();
    header('Location: ../pages/register.php?error=invalid');
    exit();
}
?>
