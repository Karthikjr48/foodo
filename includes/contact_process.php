<?php
require_once 'db_connect.php';
require_once 'rate_limit.php';
require_once 'csrf.php';

// Verify CSRF Token
validateCSRFOrDie();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/contact.php');
    exit();
}

$name = trim($_POST['name']);
$email = trim($_POST['email']);
$subject = trim($_POST['subject']);
$message = trim($_POST['message']);

if (empty($name) || empty($email) || empty($subject) || empty($message)) {
    header('Location: ../pages/contact.php?error=1');
    exit();
}

$stmt = $conn->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $name, $email, $subject, $message);
$stmt->execute();
$stmt->close();
$conn->close();

header('Location: ../pages/contact.php?sent=1');
exit();
?>
