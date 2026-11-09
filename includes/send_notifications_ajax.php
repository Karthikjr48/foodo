<?php
/**
 * send_notifications_ajax.php
 * Triggers Email and SMS notifications asynchronously in the background.
 */
require_once 'db_connect.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$orderId = (int) ($_GET['order_id'] ?? 0);
$userId = $_SESSION['user_id'] ?? 0;

if ($orderId <= 0 || $userId <= 0) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Invalid parameters']);
    exit();
}

// Fetch the order to verify ownership
$stmt = $conn->prepare("SELECT * FROM orders WHERE order_id = ? AND user_id = ?");
$stmt->bind_param("ii", $orderId, $userId);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$order) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Order not found']);
    exit();
}

// Fetch items in order
$items = [];
$itemStmt = $conn->prepare("SELECT * FROM order_items WHERE order_id = ?");
$itemStmt->bind_param("i", $orderId);
$itemStmt->execute();
$itemResult = $itemStmt->get_result();
while ($row = $itemResult->fetch_assoc()) {
    $items[] = [
        'food_name' => $row['food_name'],
        'price' => $row['price'],
        'quantity' => $row['quantity']
    ];
}
$itemStmt->close();
$conn->close();

// Trigger Notifications
require_once 'notify.php';

$fullAddress = $order['address'] . ', ' . $order['city'] . ', ' . $order['state'] . ' - ' . $order['pincode'];

// Send Email to Customer
$emailSent = sendOrderEmail(
    $order['email'],
    $order['full_name'],
    $orderId,
    $items,
    $order['subtotal'],
    $order['delivery_fee'],
    $order['tax'],
    $order['grand_total'],
    $order['payment_method'],
    $fullAddress
);

// Send Email Alert to Admin
sendAdminOrderEmail(
    $orderId,
    $order['full_name'],
    $order['phone'],
    $order['email'],
    $items,
    $order['grand_total'],
    $order['payment_method'],
    $fullAddress
);

// Send SMS
$smsSent = sendOrderSMS($order['phone'], $orderId, $order['grand_total']);

header('Content-Type: application/json');
echo json_encode([
    'status' => 'success',
    'email_sent' => $emailSent,
    'sms_sent' => $smsSent
]);
exit();
?>
