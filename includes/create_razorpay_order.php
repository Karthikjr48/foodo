<?php
/**
 * create_razorpay_order.php
 * -------------------------------------------------------
 * AJAX endpoint to create a Razorpay Order.
 * Called when checkout.php form is submitted with online payment.
 */
require_once 'db_connect.php';
require_once 'config.php';

header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Login required']);
    exit();
}

$userId = $_SESSION['user_id'];

// ---- Re-fetch cart and calculate total ----
$cartItems = [];
$stmt = $conn->prepare("
    SELECT c.quantity, f.price
    FROM cart c JOIN foods f ON c.food_id = f.food_id
    WHERE c.user_id = ?
");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) { $cartItems[] = $row; }
$stmt->close();

if (count($cartItems) === 0) {
    echo json_encode(['success' => false, 'error' => 'Cart is empty']);
    exit();
}

// ---- Read JSON body ----
$input = json_decode(file_get_contents('php://input'), true);
$couponCode = trim($input['coupon_code'] ?? '');

$subtotal = 0;
foreach ($cartItems as $item) { $subtotal += $item['price'] * $item['quantity']; }
$deliveryFee = 40;
$tax = round($subtotal * 0.05, 2);

$discount = 0;
if ($couponCode === 'FOODO10') {
    $discount = (int) round($subtotal * 0.10);
} elseif ($couponCode === 'LOYOLA') {
    if ($subtotal >= 100) {
        $discount = 50;
    }
}

$grandTotal = $subtotal + $deliveryFee + $tax - $discount;
$amount_in_paise = (int)round($grandTotal * 100);

// Check if Razorpay keys are configured (if it's still the placeholder)
$isConfigured = (defined('RAZORPAY_KEY_ID') && RAZORPAY_KEY_ID !== 'rzp_test_YOUR_KEY_ID' && !empty(RAZORPAY_KEY_ID));

if (!$isConfigured) {
    // Return mock order details for local developer demo
    echo json_encode([
        'success' => true,
        'mock' => true,
        'key_id' => 'rzp_test_mock_key',
        'amount' => $amount_in_paise,
        'razorpay_order_id' => 'order_mock_' . bin2hex(random_bytes(6)),
        'currency' => 'INR'
    ]);
    exit();
}

// Call Razorpay API using cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.razorpay.com/v1/orders');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'amount' => $amount_in_paise,
    'currency' => 'INR',
    'receipt' => 'foodo_rcpt_' . time()
]));
curl_setopt($ch, CURLOPT_USERPWD, RAZORPAY_KEY_ID . ':' . RAZORPAY_KEY_SECRET);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
$response = curl_exec($ch);
$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$order_data = json_decode($response, true);

if ($http_status !== 200 || !isset($order_data['id'])) {
    echo json_encode([
        'success' => false,
        'error' => 'Razorpay Order Creation Failed: ' . ($order_data['error']['description'] ?? 'Unknown Error')
    ]);
    exit();
}

echo json_encode([
    'success' => true,
    'mock' => false,
    'key_id' => RAZORPAY_KEY_ID,
    'amount' => $order_data['amount'],
    'razorpay_order_id' => $order_data['id'],
    'currency' => $order_data['currency']
]);
?>
