<?php
/**
 * cart_process.php
 * -------------------------------------------------------
 * Handles all cart actions: add, increase, decrease, remove.
 * Called via links like:
 *   cart_process.php?action=add&food_id=5&redirect=menu
 *   cart_process.php?action=increase&cart_id=12&redirect=cart
 *   cart_process.php?action=decrease&cart_id=12&redirect=cart
 *   cart_process.php?action=remove&cart_id=12&redirect=cart
 * -------------------------------------------------------
 */

require_once 'db_connect.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Where to send the user back to afterwards
$redirect = ($_GET['redirect'] ?? 'cart') === 'menu' ? '../pages/menu.php' : '../pages/cart.php';

// ---- Must be logged in to use the cart ----
if (!isset($_SESSION['user_id'])) {
    header('Location: ../pages/login.php?error=login_required');
    exit();
}

$userId = $_SESSION['user_id'];
$action = $_GET['action'] ?? '';

switch ($action) {

    case 'add':
        $foodId = (int) ($_GET['food_id'] ?? 0);

        if ($foodId > 0) {
            // Check if this food is already in the user's cart
            $checkStmt = $conn->prepare("SELECT cart_id, quantity FROM cart WHERE user_id = ? AND food_id = ?");
            $checkStmt->bind_param("ii", $userId, $foodId);
            $checkStmt->execute();
            $existing = $checkStmt->get_result()->fetch_assoc();
            $checkStmt->close();

            if ($existing) {
                // Already in cart: just increase the quantity
                $newQty = $existing['quantity'] + 1;
                $updateStmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE cart_id = ?");
                $updateStmt->bind_param("ii", $newQty, $existing['cart_id']);
                $updateStmt->execute();
                $updateStmt->close();
            } else {
                // Not in cart yet: insert a new row
                $insertStmt = $conn->prepare("INSERT INTO cart (user_id, food_id, quantity) VALUES (?, ?, 1)");
                $insertStmt->bind_param("ii", $userId, $foodId);
                $insertStmt->execute();
                $insertStmt->close();
            }
        }
        break;

    case 'increase':
        $cartId = (int) ($_GET['cart_id'] ?? 0);
        $stmt = $conn->prepare("UPDATE cart SET quantity = quantity + 1 WHERE cart_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $cartId, $userId);
        $stmt->execute();
        $stmt->close();
        break;

    case 'decrease':
        $cartId = (int) ($_GET['cart_id'] ?? 0);
        // Only decrease if quantity is above 1 (never go to 0 — use Remove for that)
        $stmt = $conn->prepare("UPDATE cart SET quantity = quantity - 1 WHERE cart_id = ? AND user_id = ? AND quantity > 1");
        $stmt->bind_param("ii", $cartId, $userId);
        $stmt->execute();
        $stmt->close();
        break;

    case 'remove':
        $cartId = (int) ($_GET['cart_id'] ?? 0);
        $stmt = $conn->prepare("DELETE FROM cart WHERE cart_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $cartId, $userId);
        $stmt->execute();
        $stmt->close();
        break;
}

if (isset($_GET['ajax']) && $_GET['ajax'] === '1') {
    $totalQty = 0;
    if (isset($_SESSION['user_id'])) {
        $countStmt = $conn->prepare("SELECT SUM(quantity) AS total_qty FROM cart WHERE user_id = ?");
        $countStmt->bind_param("i", $userId);
        $countStmt->execute();
        $countRes = $countStmt->get_result()->fetch_assoc();
        $totalQty = $countRes['total_qty'] ?? 0;
        $countStmt->close();
    }
    $conn->close();
    header('Content-Type: application/json');
    echo json_encode(['status' => 'success', 'cart_count' => $totalQty]);
    exit();
}

$conn->close();
header('Location: ' . $redirect);
exit();
?>
