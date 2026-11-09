<?php
/**
 * checkout_process.php
 * -------------------------------------------------------
 * Handles the Checkout form submission (pages/checkout.php).
 * Steps:
 *   1. Verify user is logged in
 *   2. Re-fetch the cart from the DB (never trust totals sent from the browser)
 *   3. Verify Razorpay signature if online payment method was used
 *   4. Insert into orders with transaction IDs
 *   5. Insert each cart item into order_items
 *   6. Clear the user's cart
 *   7. Redirect to order-success.php
 * -------------------------------------------------------
 */

require_once 'db_connect.php';
require_once 'config.php';
require_once 'rate_limit.php';
require_once 'csrf.php';

// Verify CSRF Token
validateCSRFOrDie();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: ../pages/login.php?error=login_required');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/checkout.php');
    exit();
}

$userId = $_SESSION['user_id'];

// ---- Get form data ----
$fullName = trim($_POST['fullName']);
$phone = trim($_POST['phone']);
$email = trim($_POST['email']);
$address = trim($_POST['address']);
$city = trim($_POST['city']);
$state = trim($_POST['state']);
$pincode = trim($_POST['pincode']);
$payment = $_POST['payment'] ?? 'cod';
$razorpayPaymentId = $_POST['razorpay_payment_id'] ?? null;
$razorpayOrderId = $_POST['razorpay_order_id'] ?? null;
$razorpaySignature = $_POST['razorpay_signature'] ?? null;
$couponCode = trim($_POST['coupon_code'] ?? '');

if (empty($fullName) || empty($phone) || empty($address) || empty($city) || empty($state) || empty($pincode)) {
    header('Location: ../pages/checkout.php?error=invalid');
    exit();
}

// ---- Razorpay Signature Verification ----
if ($payment === 'upi' || $payment === 'card') {
    if ($razorpayOrderId !== null && strpos($razorpayOrderId, 'order_mock_') === 0) {
        // Safe check: verify keys are indeed placeholders before accepting mock payments
        if (defined('RAZORPAY_KEY_ID') && RAZORPAY_KEY_ID !== 'rzp_test_YOUR_KEY_ID' && !empty(RAZORPAY_KEY_ID)) {
            header('Location: ../pages/checkout.php?error=invalid_payment');
            exit();
        }
    } else {
        if (empty($razorpayPaymentId) || empty($razorpayOrderId) || empty($razorpaySignature)) {
            header('Location: ../pages/checkout.php?error=missing_payment_details');
            exit();
        }
        
        $data_to_hash = $razorpayOrderId . "|" . $razorpayPaymentId;
        $expectedSignature = hash_hmac('sha256', $data_to_hash, RAZORPAY_KEY_SECRET);
        
        if ($expectedSignature !== $razorpaySignature) {
            header('Location: ../pages/checkout.php?error=signature_failed');
            exit();
        }
    }
}

// ---- Re-fetch cart from the database (never trust hidden form totals) ----
$cartItems = [];
$stmt = $conn->prepare("
    SELECT c.cart_id, c.quantity, f.food_id, f.food_name, f.price
    FROM cart c JOIN foods f ON c.food_id = f.food_id
    WHERE c.user_id = ?
");
if (!$stmt) {
    header('Location: ../pages/checkout.php?error=db_error');
    exit();
}
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) { $cartItems[] = $row; }
$stmt->close();

if (count($cartItems) === 0) {
    header('Location: ../pages/cart.php');
    exit();
}

// ---- Recalculate totals server-side ----
$subtotal = 0;
foreach ($cartItems as $item) { $subtotal += $item['price'] * $item['quantity']; }
$deliveryFee = 40;
$tax = round($subtotal * 0.05, 2);

$discount = 0.00;
if (!empty($couponCode)) {
    if ($couponCode === 'FOODO10') {
        $discount = round($subtotal * 0.10, 2);
    } elseif ($couponCode === 'LOYOLA') {
        if ($subtotal >= 100) {
            $discount = 50.00;
        }
    }
}
$grandTotal = $subtotal + $deliveryFee + $tax - $discount;

// ---- Insert the order ----
$orderStmt = $conn->prepare("
    INSERT INTO orders (user_id, full_name, phone, email, address, city, state, pincode, payment_method, subtotal, delivery_fee, tax, grand_total, discount_amount, coupon_code, order_status, razorpay_order_id, razorpay_payment_id)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'preparing', ?, ?)
");
if (!$orderStmt) {
    header('Location: ../pages/checkout.php?error=db_error');
    exit();
}
$orderStmt->bind_param(
    "isssssssssdddddss",
    $userId, $fullName, $phone, $email, $address, $city, $state, $pincode, $payment,
    $subtotal, $deliveryFee, $tax, $grandTotal, $discount, $couponCode, $razorpayOrderId, $razorpayPaymentId
);
$orderStmt->execute();
$orderId = $conn->insert_id;
$orderStmt->close();

// ---- Insert each order item ----
$itemStmt = $conn->prepare("
    INSERT INTO order_items (order_id, food_id, food_name, price, quantity, line_total)
    VALUES (?, ?, ?, ?, ?, ?)
");
if ($itemStmt) {
    foreach ($cartItems as $item) {
        $lineTotal = $item['price'] * $item['quantity'];
        $itemStmt->bind_param(
            "iisdid",
            $orderId, $item['food_id'], $item['food_name'], $item['price'], $item['quantity'], $lineTotal
        );
        $itemStmt->execute();
    }
    $itemStmt->close();
}

// ---- Clear the user's cart now that the order is placed ----
$clearStmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
if ($clearStmt) {
    $clearStmt->bind_param("i", $userId);
    $clearStmt->execute();
    $clearStmt->close();
}

$conn->close();

header('Location: ../pages/order-success.php?order_id=' . $orderId);
exit();
?>
