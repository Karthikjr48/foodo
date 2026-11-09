<?php
require_once 'includes/admin_check.php';
require_once '../includes/db_connect.php';

$pageTitle = 'Manage Orders';
$activePage = 'orders';

// ---- Handle status update ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['status'])) {
    $orderId = (int) $_POST['order_id'];
    $status = $_POST['status'];
    $allowed = ['preparing', 'picked_up', 'on_the_way', 'delivered', 'cancelled'];

    if (in_array($status, $allowed)) {
        $stmt = $conn->prepare("UPDATE orders SET order_status = ? WHERE order_id = ?");
        $stmt->bind_param("si", $status, $orderId);
        $stmt->execute();
        $stmt->close();

        // Fetch order details for notification
        $notifyStmt = $conn->prepare("SELECT email, full_name, grand_total FROM orders WHERE order_id = ?");
        $notifyStmt->bind_param("i", $orderId);
        $notifyStmt->execute();
        $orderInfo = $notifyStmt->get_result()->fetch_assoc();
        $notifyStmt->close();

        if ($orderInfo) {
            require_once '../includes/notify.php';
            sendOrderStatusUpdateEmail($orderInfo['email'], $orderInfo['full_name'], $orderId, $status, $orderInfo['grand_total']);
        }
    }
    header('Location: orders.php?updated=1');
    exit();
}

$orders = [];
$res = $conn->query("SELECT * FROM orders ORDER BY order_date DESC");
while ($row = $res->fetch_assoc()) { $orders[] = $row; }
$conn->close();

require_once 'includes/admin_header.php';
?>

<div class="admin-topbar">
    <h1>Manage Orders</h1>
</div>

<?php if (isset($_GET['updated'])): ?>
    <div class="auth-note" style="max-width: 100%;"><i class="fa-solid fa-circle-check"></i> Order status updated.</div>
<?php endif; ?>

<div class="admin-card">
    <table class="admin-table">
        <thead>
            <tr><th>Order ID</th><th>Customer</th><th>Phone</th><th>Total</th><th>Payment</th><th>Status</th><th>Date</th><th>Update</th></tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td>#TB<?php echo str_pad($order['order_id'], 5, '0', STR_PAD_LEFT); ?></td>
                    <td><?php echo htmlspecialchars($order['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($order['phone']); ?></td>
                    <td>₹<?php echo number_format($order['grand_total'], 0); ?></td>
                    <td><?php echo strtoupper($order['payment_method']); ?></td>
                    <td><span class="order-status <?php echo $order['order_status']; ?>"><?php echo ucfirst($order['order_status']); ?></span></td>
                    <td><?php echo date('j M, g:i A', strtotime($order['order_date'])); ?></td>
                    <td>
                        <form action="orders.php" method="post" class="status-form">
                            <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                            <select name="status" onchange="this.form.submit()">
                                <option value="preparing" <?php echo $order['order_status'] === 'preparing' ? 'selected' : ''; ?>>Preparing</option>
                                <option value="picked_up" <?php echo $order['order_status'] === 'picked_up' ? 'selected' : ''; ?>>Picked Up</option>
                                <option value="on_the_way" <?php echo $order['order_status'] === 'on_the_way' ? 'selected' : ''; ?>>On The Way</option>
                                <option value="delivered" <?php echo $order['order_status'] === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                <option value="cancelled" <?php echo $order['order_status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                            </select>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (count($orders) === 0): ?>
                <tr><td colspan="8" style="text-align:center; color: var(--color-muted);">No orders placed yet.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once 'includes/admin_footer.php'; ?>
