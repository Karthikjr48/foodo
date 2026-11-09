<?php
$root = '../';
$pageTitle = 'My Profile | Foodo';
require_once '../includes/db_connect.php';
require_once '../includes/header.php';

if (!$isLoggedIn) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user_id'];

// ---- Handle "Save Changes" ----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['fullName']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $address = trim($_POST['address']);

    $stmt = $conn->prepare("UPDATE users SET full_name=?, phone=?, email=?, address=? WHERE user_id=?");
    $stmt->bind_param("ssssi", $fullName, $phone, $email, $address, $userId);
    $stmt->execute();
    $stmt->close();

    // Keep the session name in sync with the update
    $_SESSION['full_name'] = $fullName;
    $_SESSION['email'] = $email;

    header('Location: profile.php?saved=1');
    exit();
}

// ---- Fetch current user data ----
$stmt = $conn->prepare("SELECT full_name, email, phone, address FROM users WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

// ---- Fetch 2 most recent orders for the preview ----
$recentOrders = [];
$orderStmt = $conn->prepare("SELECT order_id, grand_total, order_status FROM orders WHERE user_id = ? ORDER BY order_date DESC LIMIT 2");
$orderStmt->bind_param("i", $userId);
$orderStmt->execute();
$orderResult = $orderStmt->get_result();
while ($row = $orderResult->fetch_assoc()) { $recentOrders[] = $row; }
$orderStmt->close();

// Item count per order (for the "x items" line)
foreach ($recentOrders as &$order) {
    $countStmt = $conn->prepare("SELECT COALESCE(SUM(quantity),0) AS total_items FROM order_items WHERE order_id = ?");
    $countStmt->bind_param("i", $order['order_id']);
    $countStmt->execute();
    $order['item_count'] = $countStmt->get_result()->fetch_assoc()['total_items'];
    $countStmt->close();
}
unset($order);

$conn->close();
?>

    <!-- ==================== PAGE HEADER ==================== -->
    <section class="page-header small">
        <div class="container">
            <span class="eyebrow">My Account</span>
            <h1>Hello, <?php echo htmlspecialchars($firstName); ?> 👋</h1>
            <p>Manage your profile details and view your order history.</p>
        </div>
    </section>

    <!-- ==================== PROFILE SECTION ==================== -->
    <section class="section profile-section">
        <div class="container profile-layout">

            <aside class="profile-sidebar">
                <div class="profile-avatar-wrap">
                    <img src="https://placehold.co/100x100/FF4B3E/FFF8F0?text=<?php echo urlencode($avatarLetter); ?>" alt="Profile Picture" class="profile-avatar">
                </div>
                <h3><?php echo htmlspecialchars($user['full_name']); ?></h3>
                <p><?php echo htmlspecialchars($user['email']); ?></p>

                <nav class="profile-nav">
                    <a href="profile.php" class="active"><i class="fa-solid fa-user"></i> My Profile</a>
                    <a href="my-orders.php"><i class="fa-solid fa-receipt"></i> My Orders</a>
                    <a href="cart.php"><i class="fa-solid fa-cart-shopping"></i> My Cart</a>
                    <a href="../includes/logout.php" class="logout-link"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
                </nav>
            </aside>

            <div class="profile-main">
                <div class="form-card">
                    <h3><i class="fa-solid fa-id-card"></i> Personal Information</h3>

                    <?php if (isset($_GET['saved'])): ?>
                        <div class="auth-note"><i class="fa-solid fa-circle-check"></i> Profile updated successfully.</div>
                    <?php endif; ?>

                    <form method="post">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="profileName">Full Name</label>
                                <input type="text" id="profileName" name="fullName" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="profilePhone">Phone Number</label>
                                <input type="tel" id="profilePhone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" pattern="[0-9]{10}" maxlength="10" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="profileEmail">Email Address</label>
                            <input type="email" id="profileEmail" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="profileAddress">Delivery Address</label>
                            <textarea id="profileAddress" name="address" rows="3"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>

                <div class="form-card">
                    <h3><i class="fa-solid fa-clock-rotate-left"></i> Recent Orders</h3>

                    <?php if (count($recentOrders) === 0): ?>
                        <p style="color: var(--color-muted); font-size: 0.9rem;">No orders yet.</p>
                    <?php else: ?>
                        <?php foreach ($recentOrders as $order): ?>
                            <div class="order-preview-row">
                                <div>
                                    <strong>#TB<?php echo str_pad($order['order_id'], 5, '0', STR_PAD_LEFT); ?></strong>
                                    <span class="cat-tag"><?php echo $order['item_count']; ?> items · ₹<?php echo number_format($order['grand_total'], 0); ?></span>
                                </div>
                                <span class="order-status <?php echo $order['order_status']; ?>"><?php echo ucfirst($order['order_status']); ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <a href="my-orders.php" class="btn btn-outline btn-block">View All Orders</a>
                </div>
            </div>

        </div>
    </section>

<?php require_once '../includes/footer.php'; ?>
