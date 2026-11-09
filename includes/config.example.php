<?php
// ============================================================
// Foodo - Configuration Template
// ============================================================
// Copy this file to includes/config.local.php and insert your keys.
// ============================================================

// Email Configuration (Gmail SMTP with App Password)
define('MAIL_HOST',       'smtp.gmail.com');
define('MAIL_PORT',       587);
define('MAIL_USERNAME',   'your_email@gmail.com');
define('MAIL_PASSWORD',   'your_16_digit_app_password');
define('MAIL_FROM_EMAIL', 'your_email@gmail.com');
define('MAIL_FROM_NAME',  'Foodo');

// Fast2SMS Configuration
define('FAST2SMS_API_KEY', 'your_fast2sms_api_key_here');

// General
define('SITE_NAME', 'Foodo');
define('SITE_URL',  'http://localhost/food-ordering');

// Razorpay Configuration (Test or Live)
define('RAZORPAY_KEY_ID',     'rzp_test_your_key_id');
define('RAZORPAY_KEY_SECRET', 'your_razorpay_key_secret');
