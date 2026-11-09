<?php
/**
 * header.php
 * -------------------------------------------------------
 * Shared navbar, included at the top of every PHP page.
 *
 * Before including this file, each page must set:
 *   $root      = '' if this page is in the project root, or '../' if inside /pages/
 *   $pageTitle = the <title> text for this page
 * -------------------------------------------------------
 */

// Start the session (must happen before ANY HTML output)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($root)) {
    $root = '';
}
if (!isset($pageTitle)) {
    $pageTitle = 'Foodo';
}

// Check login status from the session
$isLoggedIn = isset($_SESSION['user_id']);
$loggedInName = $isLoggedIn ? $_SESSION['full_name'] : '';
$firstName = $isLoggedIn ? explode(' ', $loggedInName)[0] : '';
$avatarLetter = $isLoggedIn ? strtoupper(substr($loggedInName, 0, 1)) : '';

// ---- Real cart count (used in the navbar badge) ----
// require_once is safe to call again even if the page already connected —
// PHP only actually includes a given file once.
require_once $root . 'includes/db_connect.php';

$cartCount = 0;
if ($isLoggedIn) {
    $cartCountStmt = $conn->prepare("SELECT SUM(quantity) AS total_qty FROM cart WHERE user_id = ?");
    $cartCountStmt->bind_param("i", $_SESSION['user_id']);
    $cartCountStmt->execute();
    $cartCountResult = $cartCountStmt->get_result()->fetch_assoc();
    $cartCount = $cartCountResult['total_qty'] ?? 0;
    $cartCountStmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <link rel="icon" type="image/svg+xml" href="<?php echo $root; ?>images/logo.svg">
    <link rel="stylesheet" href="<?php echo $root; ?>css/style.css?v=2.5">
    <link rel="stylesheet" href="<?php echo $root; ?>css/responsive.css?v=2.5">
</head>
<body>

    <!-- ==================== NAVIGATION BAR ==================== -->
    <header class="navbar" id="navbar">
        <div class="container navbar-inner">
            <a href="<?php echo $root; ?>index.php" class="logo">
                <img src="<?php echo $root; ?>images/logo.svg" alt="Foodo Logo" class="logo-svg"> Foo<span>do</span>
            </a>

            <nav class="nav-links" id="navLinks">
                <a href="<?php echo $root; ?>index.php">Home</a>
                <a href="<?php echo $root; ?>pages/menu.php">Menu</a>
                <a href="<?php echo $root; ?>pages/menu.php#categories">Categories</a>
                <a href="<?php echo $root; ?>pages/about.php">About</a>
                <a href="<?php echo $root; ?>pages/contact.php">Contact</a>
                
                <!-- Mobile only links -->
                <a href="<?php echo $root; ?>pages/wishlist.php" class="mobile-only-link wishlist-mobile">
                    <i class="fa-regular fa-heart" style="margin-right: 8px;"></i> Wishlist (<span class="wishlist-count">0</span>)
                </a>
                <?php if ($isLoggedIn): ?>
                    <a href="<?php echo $root; ?>pages/profile.php" class="mobile-only-link profile-mobile">
                        <i class="fa-regular fa-user" style="margin-right: 8px;"></i> My Profile (<?php echo htmlspecialchars($firstName); ?>)
                    </a>
                    <a href="<?php echo $root; ?>includes/logout.php" class="mobile-only-link logout-mobile">
                        <i class="fa-solid fa-arrow-right-from-bracket" style="margin-right: 8px;"></i> Logout
                    </a>
                <?php else: ?>
                    <a href="<?php echo $root; ?>pages/login.php" class="mobile-only-link login-mobile">
                        <i class="fa-solid fa-arrow-right-to-bracket" style="margin-right: 8px;"></i> Login
                    </a>
                    <a href="<?php echo $root; ?>pages/register.php" class="mobile-only-link signup-mobile">
                        <i class="fa-solid fa-user-plus" style="margin-right: 8px;"></i> Sign Up
                    </a>
                <?php endif; ?>
            </nav>

            <div class="navbar-actions">
                <a href="<?php echo $root; ?>pages/wishlist.php" class="wishlist-icon" title="Wishlist">
                    <i class="fa-regular fa-heart"></i>
                    <span class="wishlist-count">0</span>
                </a>
                <a href="<?php echo $root; ?>pages/cart.php" class="cart-icon">
                    <i class="fa-solid fa-cart-shopping"></i>
                    <span class="cart-count"><?php echo (int) $cartCount; ?></span>
                </a>

                <?php if ($isLoggedIn): ?>
                    <!-- Logged in: show profile chip instead of Login/Sign Up -->
                    <a href="<?php echo $root; ?>pages/profile.php" class="profile-chip">
                        <img src="https://placehold.co/32x32/FF4B3E/FFF8F0?text=<?php echo urlencode($avatarLetter); ?>" alt="Profile">
                        <span><?php echo htmlspecialchars($firstName); ?></span>
                    </a>
                <?php else: ?>
                    <a href="<?php echo $root; ?>pages/login.php" class="btn btn-outline">Login</a>
                    <a href="<?php echo $root; ?>pages/register.php" class="btn btn-primary">Sign Up</a>
                <?php endif; ?>

                <button class="hamburger" id="hamburger" aria-label="Toggle Menu">
                    <i class="fa-solid fa-bars"></i>
                </button>
            </div>
        </div>
    </header>
