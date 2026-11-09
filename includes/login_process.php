<?php
/**
 * login_process.php
 * -------------------------------------------------------
 * Handles the Login form submission (pages/login.php).
 * Steps:
 *   1. Get form data
 *   2. Look up the user by email
 *   3. Verify the password against the stored hash
 *   4. Start a PHP session so the user stays logged in
 *   5. Redirect to the homepage
 * -------------------------------------------------------
 */

require_once 'db_connect.php';
require_once 'rate_limit.php';
require_once 'csrf.php';

// Verify CSRF Token
validateCSRFOrDie();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/login.php');
    exit();
}

$email = trim($_POST['email']);
$password = $_POST['password'];

if (empty($email) || empty($password)) {
    header('Location: ../pages/login.php?error=invalid');
    exit();
}

// ---- Check if it is an Admin credential first ----
$adminStmt = $conn->prepare("SELECT admin_id, username, password FROM admin WHERE username = ? OR email = ?");
$adminStmt->bind_param("ss", $email, $email);
$adminStmt->execute();
$adminResult = $adminStmt->get_result();

if ($adminResult->num_rows === 1) {
    $admin = $adminResult->fetch_assoc();
    if (password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['admin_id'];
        $_SESSION['admin_username'] = $admin['username'];
        $adminStmt->close();
        $conn->close();
        header('Location: ../admin/dashboard.php');
        exit();
    }
}
$adminStmt->close();

// ---- Look up the user by email (prepared statement = safe from SQL injection) ----
$stmt = $conn->prepare("SELECT user_id, full_name, email, password FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    // ---- Verify the submitted password against the stored hash ----
    if (password_verify($password, $user['password'])) {

        // ---- Correct password: start the session ----
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['email'] = $user['email'];

        $stmt->close();
        $conn->close();
        header('Location: ../index.php');
        exit();
    }
}

// ---- If we reach here, email or password was wrong ----
$stmt->close();
$conn->close();
header('Location: ../pages/login.php?error=invalid');
exit();
?>
