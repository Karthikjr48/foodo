<?php
/**
 * admin_check.php
 * -------------------------------------------------------
 * Included at the top of every admin page (except login.php).
 * Redirects to the admin login page if no admin session exists.
 * -------------------------------------------------------
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}
?>
