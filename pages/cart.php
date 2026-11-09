<?php
$root = '../';
$pageTitle = 'Your Cart | Foodo';
require_once '../includes/db_connect.php';
require_once '../includes/header.php';

// Cart requires login (header.php already redirects nowhere on its own,
// so we check again here for pages that show cart-specific content)
if (!$isLoggedIn) {
    require_once '../includes/footer.php';
    exit();
}

// ---- Fetch this user's cart items, joined with food details ----
$cartItems = [];
$stmt = $conn->prepare("
    SELECT c.cart_id, c.quantity, f.food_id, f.food_name, f.price, f.image
    FROM cart c
    JOIN foods f ON c.food_id = f.food_id
    WHERE c.user_id = ?
    ORDER BY c.added_at DESC
");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $cartItems[] = $row;
}
$stmt->close();

// ---- Calculate totals ----
$subtotal = 0;
foreach ($cartItems as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
$deliveryFee = count($cartItems) > 0 ? 40 : 0;
$tax = round($subtotal * 0.05, 2);
$grandTotal = $subtotal + $deliveryFee + $tax;

$conn->close();
require_once '../includes/helpers.php';

?>

    <!-- ==================== PAGE HEADER ==================== -->
    <section class="page-header small">
        <div class="container">
            <span class="eyebrow">Step 1 of 2</span>
            <h1>Your Cart</h1>
            <p>Review your items before checking out.</p>
        </div>
    </section>

    <!-- ==================== CART SECTION ==================== -->
    <section class="section cart-section">
        <div class="container cart-layout">

            <?php if (count($cartItems) === 0): ?>

                <!-- Empty Cart State -->
                <div class="empty-cart">
                    <i class="fa-solid fa-cart-shopping"></i>
                    <h3>Your cart is empty</h3>
                    <p>Looks like you haven't added anything yet.</p>
                    <a href="menu.php" class="btn btn-primary">Browse Menu</a>
                </div>

            <?php else: ?>

                <!-- Cart Items List (built from the database) -->
                <div class="cart-items">
                    <?php foreach ($cartItems as $item): ?>
                        <div class="cart-item">
                            <img src="<?php echo foodImageUrl($item['image'], $item['food_name'], '../'); ?>" alt="<?php echo htmlspecialchars($item['food_name']); ?>">
                            <div class="cart-item-info">
                                <h3><?php echo htmlspecialchars($item['food_name']); ?></h3>
                                <span class="item-price">₹<?php echo number_format($item['price'], 0); ?></span>
                            </div>
                            <div class="qty-control">
                                <a href="../includes/cart_process.php?action=decrease&cart_id=<?php echo $item['cart_id']; ?>&redirect=cart" class="qty-btn" aria-label="Decrease quantity">−</a>
                                <span class="qty-value"><?php echo $item['quantity']; ?></span>
                                <a href="../includes/cart_process.php?action=increase&cart_id=<?php echo $item['cart_id']; ?>&redirect=cart" class="qty-btn" aria-label="Increase quantity">+</a>
                            </div>
                            <div class="cart-item-subtotal">₹<?php echo number_format($item['price'] * $item['quantity'], 0); ?></div>
                            <a href="../includes/cart_process.php?action=remove&cart_id=<?php echo $item['cart_id']; ?>&redirect=cart" class="remove-item" aria-label="Remove item">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Order Summary (calculated from real cart data) -->
                <aside class="cart-summary">
                    <h3>Order Summary</h3>

                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span>₹<?php echo number_format($subtotal, 0); ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Delivery Fee</span>
                        <span>₹<?php echo number_format($deliveryFee, 0); ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Taxes (5%)</span>
                        <span>₹<?php echo number_format($tax, 0); ?></span>
                    </div>

                    <div class="summary-divider"></div>

                    <div class="summary-row summary-total">
                        <span>Grand Total</span>
                        <span>₹<?php echo number_format($grandTotal, 0); ?></span>
                    </div>

                    <a href="checkout.php" class="btn btn-primary btn-block">Proceed to Checkout <i class="fa-solid fa-arrow-right"></i></a>
                    <a href="menu.php" class="btn btn-outline btn-block">Add More Items</a>
                </aside>

            <?php endif; ?>

        </div>
    </section>

<?php require_once '../includes/footer.php'; ?>
