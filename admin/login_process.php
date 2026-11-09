<?php
require_once '../includes/db_connect.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit();
}

$username = trim($_POST['username']);
$password = $_POST['password'];

$stmt = $conn->prepare("SELECT admin_id, username, password FROM admin WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $admin = $result->fetch_assoc();
    if (password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['admin_id'];
        $_SESSION['admin_username'] = $admin['username'];
        $stmt->close();
        $conn->close();
        header('Location: dashboard.php');
        exit();
    }
}

$stmt->close();
$conn->close();
header('Location: login.php?error=1');
exit();
?>
