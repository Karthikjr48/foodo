<?php
/**
 * send_test_mail.php
 * -------------------------------------------------------
 * Email Dispatch Test Helper Tool
 * Open in browser to trigger a real premium test email:
 *   localhost/food-ordering/includes/send_test_mail.php?email=yourname@gmail.com
 * -------------------------------------------------------
 */
require_once 'db_connect.php';
require_once 'notify.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Extract email from query params or logged in session
$toEmail = $_GET['email'] ?? $_SESSION['email'] ?? '';

if (empty($toEmail)) {
    echo "<!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <title>Foodo Email Test Tool</title>
        <style>
            body { font-family: 'Inter', sans-serif; background: #0A0A0F; color: #F0F0F0; padding: 40px; text-align: center; }
            .card { background: #12121A; border: 1px solid rgba(255,255,255,0.08); border-radius: 12px; padding: 30px; max-width: 500px; margin: 0 auto; box-shadow: 0 4px 20px rgba(0,0,0,0.3); }
            input[type='email'] { width: 80%; padding: 10px; border-radius: 6px; border: 1px solid rgba(255,255,255,0.1); background: #1A1A2E; color: #fff; margin-bottom: 20px; font-size: 14px; text-align: center; }
            button { background: linear-gradient(135deg, #FF6B35, #FFD166); border: none; padding: 10px 24px; font-weight: bold; border-radius: 20px; cursor: pointer; color: #0A0A0F; font-size: 14px; }
            button:hover { opacity: 0.9; }
            a { color: #FF6B35; text-decoration: none; }
        </style>
    </head>
    <body>
        <div class='card'>
            <h2>🍽️ Foodo Email Test Tool</h2>
            <p style='color: #8A8A9A; margin-bottom: 20px;'>Enter your email address to receive a live sample of the premium order invoice template.</p>
            <form method='get'>
                <input type='email' name='email' placeholder='Enter your email address' required><br>
                <button type='submit'>Send Test Invoice Email</button>
            </form>
            <p style='margin-top: 25px; font-size: 12px;'><a href='../index.php'>Back to website</a></p>
        </div>
    </body>
    </html>";
    exit();
}

$customerName = $_SESSION['full_name'] ?? 'Tester Karthik';

// Mock order details
$orderId = 98765;
$subtotal = 737;
$deliveryFee = 40;
$tax = 37;
$grandTotal = 814;
$paymentMethod = 'upi';
$address = 'Loyola College, Nungambakkam, Chennai - 600034';

$cartItems = [
    [
        'food_name' => 'Chicken Dum Biryani',
        'price' => 289,
        'quantity' => 2
    ],
    [
        'food_name' => 'Chilli Garlic Fried Rice',
        'price' => 159,
        'quantity' => 1
    ]
];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sending Test Mail...</title>
    <style>
        body { font-family: 'Inter', sans-serif; background: #0A0A0F; color: #F0F0F0; padding: 50px; text-align: center; }
        .card { background: #12121A; border: 1px solid rgba(255,255,255,0.08); border-radius: 12px; padding: 40px; max-width: 600px; margin: 0 auto; box-shadow: 0 4px 20px rgba(0,0,0,0.3); }
        .status-success { color: #00D68F; font-size: 20px; font-weight: bold; margin: 20px 0; }
        .status-fail { color: #FF4757; font-size: 20px; font-weight: bold; margin: 20px 0; }
        a { color: #FF6B35; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Email Sending Status</h2>
        <p>Attempting to send premium HTML invoice to: <strong><?php echo htmlspecialchars($toEmail); ?></strong>...</p>
        
        <?php
        $result = sendOrderEmail(
            $toEmail,
            $customerName,
            $orderId,
            $cartItems,
            $subtotal,
            $deliveryFee,
            $tax,
            $grandTotal,
            $paymentMethod,
            $address
        );

        if ($result) {
            echo "<div class='status-success'>✓ Premium Test Invoice Sent!</div>";
            echo "<p>The order receipt was successfully dispatched. Please check your Inbox and Spam folders for the verification mail.</p>";
        } else {
            echo "<div class='status-fail'>✗ Dispatch Failed</div>";
            echo "<p>Please ensure SMTP credentials in <code>includes/config.php</code> are valid or check PHP error logs.</p>";
        }
        ?>
        <p style="margin-top: 30px;"><a href="send_test_mail.php">Send another test</a> | <a href="../index.php">Back to homepage</a></p>
    </div>
</body>
</html>
