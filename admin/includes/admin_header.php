<?php
/**
 * admin_header.php
 * -------------------------------------------------------
 * Shared layout for every admin page (after login).
 * Expects $pageTitle and $activePage to be set before including.
 * -------------------------------------------------------
 */
if (!isset($pageTitle)) { $pageTitle = 'Admin Panel'; }
if (!isset($activePage)) { $activePage = ''; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?> | Foodo Admin</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/responsive.css">
</head>
<body>

    <div class="admin-layout">

        <!-- ==================== ADMIN SIDEBAR ==================== -->
        <aside class="admin-sidebar">
            <a href="dashboard.php" class="logo admin-logo">
                <img src="../images/logo.svg" alt="Foodo Logo" class="logo-svg" style="width:28px; height:28px; vertical-align:middle; margin-right:6px;"> Foo<span>do</span>
            </a>
            <p class="admin-tag">Admin Panel</p>

            <nav class="admin-nav">
                <a href="dashboard.php" class="<?php echo $activePage === 'dashboard' ? 'active' : ''; ?>">
                    <i class="fa-solid fa-gauge"></i> Dashboard
                </a>
                <a href="foods.php" class="<?php echo $activePage === 'foods' ? 'active' : ''; ?>">
                    <i class="fa-solid fa-burger"></i> Manage Foods
                </a>
                <a href="categories.php" class="<?php echo $activePage === 'categories' ? 'active' : ''; ?>">
                    <i class="fa-solid fa-list"></i> Categories
                </a>
                <a href="orders.php" class="<?php echo $activePage === 'orders' ? 'active' : ''; ?>">
                    <i class="fa-solid fa-receipt"></i> Orders
                </a>
                <a href="messages.php" class="<?php echo $activePage === 'messages' ? 'active' : ''; ?>">
                    <i class="fa-solid fa-envelope"></i> Messages
                </a>
                <a href="logout.php" class="logout-link">
                    <i class="fa-solid fa-right-from-bracket"></i> Logout
                </a>
            </nav>
        </aside>

        <!-- ==================== ADMIN MAIN CONTENT ==================== -->
        <main class="admin-main">
