<?php
$root = '../';
$pageTitle = 'Order Placed | Foodo';
require_once '../includes/db_connect.php';
require_once '../includes/header.php';

if (!$isLoggedIn) {
    header('Location: login.php');
    exit();
}

$orderId = (int) ($_GET['order_id'] ?? 0);
$userId = $_SESSION['user_id'];

// ---- Fetch the order, making sure it belongs to this logged-in user ----
$stmt = $conn->prepare("SELECT * FROM orders WHERE order_id = ? AND user_id = ?");
$stmt->bind_param("ii", $orderId, $userId);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$order) {
    // Either a bad order_id or it doesn't belong to this user — send them home safely.
    header('Location: ../index.php');
    exit();
}

// ---- Fetch the items in this order ----
$items = [];
$itemStmt = $conn->prepare("SELECT * FROM order_items WHERE order_id = ?");
$itemStmt->bind_param("i", $orderId);
$itemStmt->execute();
$itemResult = $itemStmt->get_result();
while ($row = $itemResult->fetch_assoc()) { $items[] = $row; }
$itemStmt->close();
$conn->close();

$paymentLabels = ['cod' => 'Cash on Delivery', 'upi' => 'UPI', 'card' => 'Credit / Debit Card'];
$paymentLabel = $paymentLabels[$order['payment_method']] ?? $order['payment_method'];

$firstName = explode(' ', $order['full_name'])[0];
?>

<style>
/* Scoped Confetti and Checkmark animations for order success */
.success-card {
    position: relative;
    overflow: hidden;
    padding: 60px 40px !important;
}

.success-checkmark-wrap {
    width: 80px;
    height: 80px;
    margin: 0 auto 24px;
}

.checkmark-svg {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: block;
    stroke-width: 4;
    stroke: #ffffff;
    stroke-miterlimit: 10;
    box-shadow: inset 0px 0px 0px #00D68F;
    animation: fill-checkmark .4s ease-in-out .4s forwards, scale-checkmark .3s ease-in-out .9s both;
}

.checkmark__circle {
    stroke-dasharray: 166;
    stroke-dashoffset: 166;
    stroke-width: 4;
    stroke-miterlimit: 10;
    stroke: #00D68F;
    fill: none;
    animation: stroke-checkmark .6s cubic-bezier(0.65, 0, 0.45, 1) forwards;
}

.checkmark__check {
    transform-origin: 50% 50%;
    stroke-dasharray: 48;
    stroke-dashoffset: 48;
    stroke: #ffffff;
    stroke-width: 4;
    stroke-linecap: round;
    animation: stroke-checkmark .3s cubic-bezier(0.65, 0, 0.45, 1) .8s forwards;
}

@keyframes stroke-checkmark {
    100% { stroke-dashoffset: 0; }
}

@keyframes scale-checkmark {
    0%, 100% { transform: none; }
    50% { transform: scale3d(1.1, 1.1, 1); }
}

@keyframes fill-checkmark {
    100% { box-shadow: inset 0px 0px 0px 40px #00D68F; }
}

/* Confetti particles */
.confetti-piece {
    position: absolute;
    width: 10px;
    height: 12px;
    background-color: #FF6B35;
    top: -20px;
    z-index: 1;
    opacity: 0;
    border-radius: 2px;
    animation: fall-confetti 4s linear forwards;
}

@keyframes fall-confetti {
    0% {
        top: -20px;
        opacity: 1;
        transform: rotate(0deg) translateX(0);
    }
    100% {
        top: 100%;
        opacity: 0;
        transform: rotate(720deg) translateX(50px);
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const card = document.querySelector('.success-card');
    const colors = ['#FF6B35', '#FFD166', '#00D68F', '#22a6ff', '#e040fb', '#ffffff'];
    
    // Spawn 80 confetti pieces with randomized offsets
    for (let i = 0; i < 80; i++) {
        const piece = document.createElement('div');
        piece.classList.add('confetti-piece');
        piece.style.left = (Math.random() * 100) + '%';
        piece.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
        piece.style.animationDelay = (Math.random() * 2.5) + 's';
        
        // Random dimensions
        const sizeW = Math.random() * 8 + 6;
        const sizeH = Math.random() * 12 + 6;
        piece.style.width = sizeW + 'px';
        piece.style.height = sizeH + 'px';
        
        // Add particle to success card background
        card.appendChild(piece);
    }

    // Trigger Email & SMS notifications asynchronously in the background
    // This allows the success page to load instantly without waiting for mail servers!
    const orderId = <?php echo $orderId; ?>;
    fetch(`../includes/send_notifications_ajax.php?order_id=${orderId}`)
        .then(res => res.json())
        .then(data => {
            console.log('Async notifications result:', data);
        })
        .catch(err => {
            console.error('Async notifications error:', err);
        });
});
</script>

    <!-- ==================== ORDER SUCCESS SECTION ==================== -->
    <section class="section success-section">
        <div class="container">
            <div class="success-card">

                <div class="success-checkmark-wrap">
                    <svg class="checkmark-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                        <circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none"/>
                        <path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
                    </svg>
                </div>

                <h1>Thank You, <?php echo htmlspecialchars($firstName); ?>! 🎉</h1>
                <p class="success-sub">Your order has been placed successfully and is being prepared with care.</p>

                <div class="order-id-badge">Order ID: <strong>#TB<?php echo str_pad($order['order_id'], 5, '0', STR_PAD_LEFT); ?></strong></div>

                <div class="success-details-grid">

                    <div class="success-detail-card">
                        <h3><i class="fa-solid fa-receipt"></i> Order Summary</h3>
                        <?php foreach ($items as $item): ?>
                            <div class="checkout-item">
                                <span><?php echo htmlspecialchars($item['food_name']); ?> <em>× <?php echo $item['quantity']; ?></em></span>
                                <span>₹<?php echo number_format($item['line_total'], 0); ?></span>
                            </div>
                        <?php endforeach; ?>
                        <?php if ($order['discount_amount'] > 0): ?>
                            <div class="summary-row" style="color: var(--color-success); font-size: 0.88rem; margin-bottom: 12px; font-weight: 500;">
                                <span>Discount (<?php echo htmlspecialchars($order['coupon_code']); ?>)</span>
                                <span>-₹<?php echo number_format($order['discount_amount'], 0); ?></span>
                            </div>
                        <?php endif; ?>
                        <div class="summary-divider"></div>
                        <div class="summary-row summary-total">
                            <span>Total Paid</span>
                            <span>₹<?php echo number_format($order['grand_total'], 0); ?></span>
                        </div>
                    </div>

                    <div class="success-detail-card">
                        <h3><i class="fa-solid fa-location-dot"></i> Delivery Details</h3>
                        <p class="delivery-address">
                            <strong><?php echo htmlspecialchars($order['full_name']); ?></strong><br>
                            <?php echo nl2br(htmlspecialchars($order['address'])); ?>,<br>
                            <?php echo htmlspecialchars($order['city']); ?>, <?php echo htmlspecialchars($order['state']); ?> - <?php echo htmlspecialchars($order['pincode']); ?><br>
                            +91 <?php echo htmlspecialchars($order['phone']); ?>
                        </p>

                        <div class="summary-divider"></div>

                        <div class="eta-box">
                            <i class="fa-solid fa-clock"></i>
                            <div>
                                <strong>Estimated Delivery</strong>
                                <span>30 - 40 minutes from order time</span>
                            </div>
                        </div>

                        <div class="payment-method-note">
                            <i class="fa-solid fa-money-bill-wave"></i> Payment Method: <?php echo htmlspecialchars($paymentLabel); ?>
                        </div>
                    </div>

                </div>

                <div class="success-actions">
                    <a href="track-order.php?order_id=<?php echo $orderId; ?>" class="btn btn-outline">Track Order</a>
                    <a href="menu.php" class="btn btn-primary">Continue Shopping <i class="fa-solid fa-arrow-right"></i></a>
                </div>

            </div>
        </div>
    </section>

<?php require_once '../includes/footer.php'; ?>
