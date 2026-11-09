<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (isset($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Foodo</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        /* ========== RESET & BASE ========== */
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #0A0A0F;
            background-image:
                radial-gradient(ellipse at 20% 50%, rgba(255,107,53,0.12) 0%, transparent 50%),
                radial-gradient(ellipse at 80% 20%, rgba(255,209,102,0.08) 0%, transparent 50%),
                radial-gradient(ellipse at 50% 80%, rgba(255,75,62,0.06) 0%, transparent 50%);
            overflow: hidden;
            position: relative;
        }

        /* ========== ANIMATED GRADIENT ORBS ========== */
        .orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.4;
            pointer-events: none;
            z-index: 0;
        }
        .orb-1 {
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(255,107,53,0.5), transparent 70%);
            top: -100px; left: -100px;
            animation: float1 8s ease-in-out infinite;
        }
        .orb-2 {
            width: 350px; height: 350px;
            background: radial-gradient(circle, rgba(255,209,102,0.4), transparent 70%);
            bottom: -80px; right: -80px;
            animation: float2 10s ease-in-out infinite;
        }
        .orb-3 {
            width: 250px; height: 250px;
            background: radial-gradient(circle, rgba(255,75,62,0.3), transparent 70%);
            top: 50%; left: 60%;
            animation: float3 12s ease-in-out infinite;
        }
        @keyframes float1 {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(60px, 40px) scale(1.15); }
        }
        @keyframes float2 {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(-50px, -30px) scale(1.1); }
        }
        @keyframes float3 {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(30px, -50px) scale(1.2); }
        }

        /* ========== SPLIT LAYOUT ========== */
        .admin-login-wrapper {
            display: flex;
            width: 900px;
            max-width: 95vw;
            min-height: 560px;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 25px 80px rgba(0,0,0,0.5), 0 0 0 1px rgba(255,255,255,0.06);
            position: relative;
            z-index: 1;
            animation: cardAppear 0.8s cubic-bezier(0.16,1,0.3,1) both;
        }
        @keyframes cardAppear {
            0% { opacity: 0; transform: translateY(30px) scale(0.96); }
            100% { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* Left: Image Side */
        .admin-login-image {
            flex: 1;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: flex-end;
        }
        .admin-login-image img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .admin-login-image::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(10,10,15,0.85) 0%, rgba(10,10,15,0.2) 60%, transparent 100%);
            z-index: 1;
        }
        .image-overlay-text {
            position: relative;
            z-index: 2;
            padding: 32px;
            color: #fff;
        }
        .image-overlay-text h2 {
            font-family: 'Poppins', sans-serif;
            font-size: 1.6rem;
            font-weight: 700;
            margin-bottom: 6px;
        }
        .image-overlay-text p {
            font-size: 0.85rem;
            opacity: 0.7;
            line-height: 1.5;
        }

        /* Right: Form Side (Glassmorphism) */
        .admin-login-form-side {
            flex: 1;
            background: rgba(18, 18, 28, 0.95);
            backdrop-filter: blur(40px);
            -webkit-backdrop-filter: blur(40px);
            border-left: 1px solid rgba(255,255,255,0.06);
            padding: 48px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
        }
        .admin-login-form-side::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #FF6B35, #FFD166, #FF4B3E);
            border-radius: 0 0 2px 2px;
        }

        /* ========== LOGO ========== */
        .admin-logo {
            text-align: center;
            margin-bottom: 8px;
        }
        .admin-logo .logo-icon {
            font-size: 2rem;
        }
        .admin-logo a {
            font-family: 'Poppins', sans-serif;
            font-size: 1.7rem;
            font-weight: 800;
            color: #fff;
            text-decoration: none;
            letter-spacing: -0.5px;
        }
        .admin-logo a span {
            color: #FF6B35;
        }

        /* ========== ADMIN HEADER ========== */
        .admin-header {
            text-align: center;
            margin-bottom: 28px;
        }
        .admin-shield {
            width: 52px;
            height: 52px;
            border-radius: 16px;
            background: linear-gradient(135deg, rgba(255,107,53,0.15), rgba(255,75,62,0.1));
            border: 1px solid rgba(255,107,53,0.2);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 14px;
        }
        .admin-shield i {
            font-size: 1.3rem;
            color: #FF6B35;
        }
        .admin-header h1 {
            font-family: 'Poppins', sans-serif;
            font-size: 1.5rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 4px;
        }
        .admin-header p {
            font-size: 0.85rem;
            color: rgba(255,255,255,0.45);
        }

        /* ========== ERROR MESSAGE ========== */
        .admin-error {
            background: rgba(255,75,62,0.1);
            border: 1px solid rgba(255,75,62,0.25);
            color: #FF6B6B;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 0.85rem;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideInError 0.4s cubic-bezier(0.16,1,0.3,1);
        }
        @keyframes slideInError {
            0% { opacity: 0; transform: translateY(-10px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        .admin-error i { font-size: 1rem; }

        /* ========== FORM ========== */
        .admin-form .form-group {
            margin-bottom: 18px;
        }
        .admin-form label {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            color: rgba(255,255,255,0.6);
            margin-bottom: 7px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .admin-form input[type="text"],
        .admin-form input[type="password"] {
            width: 100%;
            padding: 13px 16px;
            background: rgba(255,255,255,0.05);
            border: 1.5px solid rgba(255,255,255,0.1);
            border-radius: 12px;
            color: #fff;
            font-size: 0.95rem;
            font-family: 'Inter', sans-serif;
            transition: all 0.3s ease;
            outline: none;
        }
        .admin-form input:focus {
            border-color: #FF6B35;
            background: rgba(255,107,53,0.05);
            box-shadow: 0 0 0 4px rgba(255,107,53,0.1);
        }
        .admin-form input::placeholder {
            color: rgba(255,255,255,0.25);
        }

        /* Password wrapper */
        .password-wrap {
            position: relative;
        }
        .password-wrap input {
            padding-right: 48px;
        }
        .toggle-password {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: rgba(255,255,255,0.3);
            cursor: pointer;
            font-size: 1rem;
            padding: 4px;
            transition: color 0.2s;
        }
        .toggle-password:hover {
            color: #FF6B35;
        }

        /* ========== SUBMIT BUTTON ========== */
        .btn-admin-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #FF6B35, #FF4B3E);
            border: none;
            border-radius: 12px;
            color: #fff;
            font-family: 'Poppins', sans-serif;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            margin-top: 6px;
        }
        .btn-admin-login::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, #FF4B3E, #FF6B35);
            opacity: 0;
            transition: opacity 0.3s;
        }
        .btn-admin-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255,107,53,0.35);
        }
        .btn-admin-login:hover::before {
            opacity: 1;
        }
        .btn-admin-login:active {
            transform: translateY(0);
        }
        .btn-admin-login .btn-text {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .btn-admin-login .spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 2.5px solid rgba(255,255,255,0.3);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
        }
        .btn-admin-login.loading .btn-text { display: none; }
        .btn-admin-login.loading .spinner { display: block; margin: 0 auto; position: relative; z-index: 1; }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* ========== BACK LINK ========== */
        .back-link {
            text-align: center;
            margin-top: 24px;
        }
        .back-link a {
            color: rgba(255,255,255,0.4);
            text-decoration: none;
            font-size: 0.85rem;
            transition: color 0.2s;
        }
        .back-link a:hover {
            color: #FF6B35;
        }
        .back-link a i {
            margin-right: 4px;
        }

        /* ========== RESPONSIVE ========== */
        @media (max-width: 768px) {
            .admin-login-image {
                display: none;
            }
            .admin-login-wrapper {
                width: 100%;
                max-width: 420px;
                min-height: auto;
                border-radius: 20px;
            }
            .admin-login-form-side {
                padding: 36px 28px;
            }
        }
    </style>
</head>
<body>

    <!-- Animated Background Orbs -->
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    <div class="admin-login-wrapper">
        <!-- Left: Food Image -->
        <div class="admin-login-image">
            <img src="https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=1200&h=800&fit=crop" alt="Delicious food">
            <div class="image-overlay-text">
                <h2>Foodo Admin Panel</h2>
                <p>Manage orders, menus, and customers from your dashboard.</p>
            </div>
        </div>

        <!-- Right: Login Form -->
        <div class="admin-login-form-side">
            <div class="admin-logo">
                <a href="../index.php"><span class="logo-icon">🍽️</span> Foo<span>do</span></a>
            </div>

            <div class="admin-header">
                <div class="admin-shield">
                    <i class="fa-solid fa-user-shield"></i>
                </div>
                <h1>Admin Login</h1>
                <p>Restricted access — Foodo staff only.</p>
            </div>

            <?php if (isset($_GET['error'])): ?>
                <div class="admin-error">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    Incorrect username or password.
                </div>
            <?php endif; ?>

            <form class="admin-form" action="login_process.php" method="post" id="adminLoginForm">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" placeholder="Enter admin username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="password-wrap">
                        <input type="password" id="password" name="password" placeholder="Enter password" required>
                        <button type="button" class="toggle-password" aria-label="Show password" onclick="togglePass()">
                            <i class="fa-solid fa-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>
                <button type="submit" class="btn-admin-login" id="loginBtn">
                    <span class="btn-text"><i class="fa-solid fa-right-to-bracket"></i> Login to Admin Panel</span>
                    <div class="spinner"></div>
                </button>
            </form>

            <div class="back-link">
                <a href="../index.php"><i class="fa-solid fa-arrow-left"></i> Back to Foodo website</a>
            </div>
        </div>
    </div>

    <script>
        // Password toggle
        function togglePass() {
            const pwd = document.getElementById('password');
            const icon = document.getElementById('toggleIcon');
            if (pwd.type === 'password') {
                pwd.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                pwd.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }

        // Loading state on submit
        document.getElementById('adminLoginForm').addEventListener('submit', function() {
            document.getElementById('loginBtn').classList.add('loading');
        });
    </script>
</body>
</html>
