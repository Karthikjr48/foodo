<?php
/**
 * logout.php
 * -------------------------------------------------------
 * Destroys the session (logs the user out) and redirects home.
 * Linked from the profile sidebar "Logout" button.
 * -------------------------------------------------------
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Clear all session variables
$_SESSION = [];

// Destroy the session completely
session_destroy();

// Redirect to the homepage
header('Location: ../index.php');
exit();
?>
