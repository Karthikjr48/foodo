<?php
// Set default timezone to Indian Standard Time (IST)
date_default_timezone_set('Asia/Kolkata');

/**
 * config.php
 * -------------------------------------------------------
 * Foodo — Central Notification & Payment Configuration
 * Email (Gmail SMTP via PHPMailer) + SMS (Fast2SMS API) + Razorpay
 *
 * HOW TO SETUP:
 * -------------
 * 1. Copy includes/config.example.php to includes/config.local.php
 * 2. Add your private credentials inside config.local.php
 * 3. config.local.php is in .gitignore and will never be pushed to GitHub.
 * -------------------------------------------------------
 */

// Load local environment overrides if present
if (file_exists(__DIR__ . '/config.local.php')) {
    require_once __DIR__ . '/config.local.php';
}

// ==================== EMAIL CONFIG ====================
if (!defined('MAIL_HOST'))       define('MAIL_HOST',       getenv('MAIL_HOST') ?: 'smtp.gmail.com');
if (!defined('MAIL_PORT'))       define('MAIL_PORT',       getenv('MAIL_PORT') ?: 587);
if (!defined('MAIL_USERNAME'))   define('MAIL_USERNAME',   getenv('MAIL_USERNAME') ?: 'your_email@gmail.com');
if (!defined('MAIL_PASSWORD'))   define('MAIL_PASSWORD',   getenv('MAIL_PASSWORD') ?: 'your_app_password');
if (!defined('MAIL_FROM_EMAIL')) define('MAIL_FROM_EMAIL', getenv('MAIL_FROM_EMAIL') ?: 'your_email@gmail.com');
if (!defined('MAIL_FROM_NAME'))  define('MAIL_FROM_NAME',  getenv('MAIL_FROM_NAME') ?: 'Foodo');

// ==================== SMS CONFIG ====================
if (!defined('FAST2SMS_API_KEY')) define('FAST2SMS_API_KEY', getenv('FAST2SMS_API_KEY') ?: 'your_fast2sms_api_key');

// ==================== GENERAL ====================
if (!defined('SITE_NAME')) define('SITE_NAME', getenv('SITE_NAME') ?: 'Foodo');
if (!defined('SITE_URL'))  define('SITE_URL',  getenv('SITE_URL') ?: 'http://localhost/food-ordering');

// ==================== RAZORPAY CONFIG ====================
if (!defined('RAZORPAY_KEY_ID'))     define('RAZORPAY_KEY_ID',     getenv('RAZORPAY_KEY_ID') ?: 'rzp_test_your_key_id');
if (!defined('RAZORPAY_KEY_SECRET')) define('RAZORPAY_KEY_SECRET', getenv('RAZORPAY_KEY_SECRET') ?: 'your_razorpay_key_secret');
