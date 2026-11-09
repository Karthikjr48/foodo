<?php
// Set default timezone to Indian Standard Time (IST)
date_default_timezone_set('Asia/Kolkata');

/**
 * db_connect.php
 * -------------------------------------------------------
 * Central database connection file.
 * Every PHP file that needs the database will do:
 *      require_once '../includes/db_connect.php';
 * (or 'includes/db_connect.php' if it's in the root folder)
 * -------------------------------------------------------
 * Auto-detects environment: works on both localhost (XAMPP)
 * and InfinityFree hosting without needing to edit this file
 * every time you switch between them.
 * -------------------------------------------------------
 */

// ---- Detect environment ----
// Works on localhost, GoogieHost (foodo.cu.ma), and Byethost (byethost22.com)
$serverName = $_SERVER['SERVER_NAME'] ?? '';
$httpHost = $_SERVER['HTTP_HOST'] ?? '';
$isLiveHost = (strpos($serverName, 'foodo.cu.ma') !== false) || 
              (strpos($httpHost, 'foodo.cu.ma') !== false) ||
              (strpos($serverName, 'byethost') !== false) || 
              (strpos($httpHost, 'byethost') !== false);

if ($isLiveHost) {
    // Enable error reporting on live server for debugging 500 Internal Server Error
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

if (!$isLiveHost) {
    // ---- Local XAMPP settings ----
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_NAME', 'tastybite_db');
} else {
    // ---- Live Hosting settings ----
    if (file_exists(__DIR__ . '/config.php')) {
        require_once __DIR__ . '/config.php';
    }

    if (strpos($serverName, 'byethost') !== false || strpos($httpHost, 'byethost') !== false) {
        // Byethost MySQL credentials
        define('DB_HOST', getenv('DB_HOST') ?: 'sql206.byethost22.com');
        define('DB_USER', getenv('DB_USER') ?: 'b22_42369437');
        define('DB_PASS', defined('DB_LIVE_BYET_PASS') ? DB_LIVE_BYET_PASS : (getenv('DB_PASS') ?: ''));
        define('DB_NAME', getenv('DB_NAME') ?: 'b22_42369437_foodo');
    } else {
        // GoogieHost / InfinityFree
        define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
        define('DB_USER', getenv('DB_USER') ?: 'vbbvbwuc_foodo');
        define('DB_PASS', defined('DB_LIVE_GOOGIE_PASS') ? DB_LIVE_GOOGIE_PASS : (getenv('DB_PASS') ?: ''));
        define('DB_NAME', getenv('DB_NAME') ?: 'vbbvbwuc_foodo');
    }
}

// Disable mysqli exceptions so we can catch database connection errors manually
mysqli_report(MYSQLI_REPORT_OFF);

// ---- Create Connection using MySQLi (Object-Oriented style) ----
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// ---- Check Connection ----
if ($conn->connect_error) {
    http_response_code(200);
    echo "<div style='padding: 20px; margin: 20px; background: #FFEBEB; border: 1px solid #FF4757; color: #FF4757; font-family: sans-serif; border-radius: 8px;'>";
    echo "<h3>Database Connection Error</h3>";
    echo "<p>" . htmlspecialchars($conn->connect_error) . "</p>";
    echo "<p>Please verify your MySQL Hostname, Username, Password, and Database Name in <code>includes/db_connect.php</code>.</p>";
    echo "</div>";
    exit;
}

// ---- Set charset to avoid encoding issues with special characters (₹, etc.) ----
$conn->set_charset("utf8mb4");

// Note: We do NOT close the connection here.
// It stays open for the rest of the script and PHP closes it automatically
// when the script finishes executing.
?>
