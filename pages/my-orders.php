<?php
$root = '../';
$pageTitle = 'My Orders | Foodo';
require_once '../includes/db_connect.php';
require_once '../includes/header.php';

if (!$isLoggedIn) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user_id'];

// ---- Fetch this user's orders ----
$orders = [];
$stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) { $orders[] = $row; }
$stmt->close();

// ---- Fetch items for each order (grouped by order_id) ----
$itemsByOrder = [];
if (count($orders) > 0) {
    $orderIds = array_column($orders, 'order_id');
    $placeholders = implode(',', array_fill(0, count($orderIds), '?'));
    $types = str_repeat('i', count($orderIds));

    $itemStmt = $conn->prepare("SELECT * FROM order_items WHERE order_id IN ($placeholders)");
    $itemStmt->bind_param($types, ...$orderIds);
    $itemStmt->execute();
    $itemResult = $itemStmt->get_result();
    while ($row = $itemResult->fetch_assoc()) {
        $itemsByOrder[$row['order_id']][] = $row;
    }
    $itemStmt->close();
}

$conn->close();

function placeholderImage($text, $bg = 'FF4B3E', $fg = 'FFF8F0') {
    return "https://placehold.co/50x50/{$bg}/{$fg}?text=" . urlencode(substr($text, 0, 1));
}
?>

    <!-- ==================== PAGE HEADER ==================== -->
    <section class="page-header small">
        <div class="container">
            <span class="eyebrow">My Account</span>
            <h1>My Orders</h1>
            <p>Track and review all your past orders.</p>
        </div>
    </section>

    <!-- ==================== ORDERS SECTION ==================== -->
    <section class="section profile-section">
        <div class="container profile-layout">

            <aside class="profile-sidebar">
                <div class="profile-avatar-wrap">
                    <img src="https://placehold.co/100x100/FF4B3E/FFF8F0?text=<?php echo urlencode($avatarLetter); ?>" alt="Profile Picture" class="profile-avatar">
                </div>
                <h3><?php echo htmlspecialchars($loggedInName); ?></h3>
                <p><?php echo htmlspecialchars($_SESSION['email']); ?></p>

                <nav class="profile-nav">
                    <a href="profile.php"><i class="fa-solid fa-user"></i> My Profile</a>
                    <a href="my-orders.php" class="active"><i class="fa-solid fa-receipt"></i> My Orders</a>
                    <a href="cart.php"><i class="fa-solid fa-cart-shopping"></i> My Cart</a>
                    <a href="../includes/logout.php" class="logout-link"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
                </nav>
            </aside>

            <div class="profile-main">

                <?php if (count($orders) === 0): ?>
                    <div class="empty-cart">
                        <i class="fa-solid fa-receipt"></i>
                        <h3>No orders yet</h3>
                        <p>Your placed orders will show up here.</p>
                        <a href="menu.php" class="btn btn-primary">Browse Menu</a>
                    </div>
                <?php else: ?>

                    <div class="filter-bar orders-filter" id="ordersFilter">
                        <button class="filter-chip active" data-status="all">All Orders</button>
                        <button class="filter-chip" data-status="preparing">Preparing</button>
                        <button class="filter-chip" data-status="delivered">Delivered</button>
                        <button class="filter-chip" data-status="cancelled">Cancelled</button>
                    </div>

                    <div class="orders-list" id="ordersList">
                        <?php foreach ($orders as $order):
                            $items = $itemsByOrder[$order['order_id']] ?? [];
                            $firstItem = $items[0]['food_name'] ?? '';
                            $extraCount = count($items) - 1;
                            $itemSummary = htmlspecialchars($firstItem) . ($extraCount > 0 ? " + $extraCount more" : '');
                        ?>
                            <div class="order-card" data-status="<?php echo htmlspecialchars($order['order_status']); ?>">
                                <div class="order-card-top">
                                    <div>
                                        <strong>Order #TB<?php echo str_pad($order['order_id'], 5, '0', STR_PAD_LEFT); ?></strong>
                                        <span class="cat-tag">Placed on <?php echo date('j F Y, g:i A', strtotime($order['order_date'])); ?></span>
                                    </div>
                                    <span class="order-status <?php echo htmlspecialchars($order['order_status']); ?>">
                                        <?php echo ucfirst($order['order_status']); ?>
                                    </span>
                                </div>
                                <div class="order-card-items">
                                    <?php foreach (array_slice($items, 0, 2) as $it): ?>
                                        <img src="<?php echo placeholderImage($it['food_name']); ?>" alt="item">
                                    <?php endforeach; ?>
                                    <span><?php echo $itemSummary; ?></span>
                                </div>
                                <div class="order-card-bottom">
                                    <span class="price">₹<?php echo number_format($order['grand_total'], 0); ?></span>
                                    <div class="order-actions">
                                        <?php if ($order['order_status'] === 'preparing' || $order['order_status'] === 'picked_up' || $order['order_status'] === 'on_the_way'): ?>
                                            <a href="track-order.php?order_id=<?php echo $order['order_id']; ?>" class="btn btn-outline btn-sm">Track Order</a>
                                        <?php else: ?>
                                            <a href="menu.php" class="btn btn-primary btn-sm">Reorder</a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <p class="no-results" id="ordersNoResults" style="display:none;">No orders found in this category.</p>

                <?php endif; ?>

            </div>

        </div>
    </section>

<?php require_once '../includes/footer.php'; ?>
