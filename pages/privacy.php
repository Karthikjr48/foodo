<?php
/**
 * privacy.php
 * -------------------------------------------------------
 * Privacy Policy Page
 * -------------------------------------------------------
 */
$root = '../';
$pageTitle = 'Privacy Policy | Foodo';
require_once '../includes/header.php';
?>

    <!-- ==================== PAGE HEADER ==================== -->
    <section class="page-header small">
        <div class="container">
            <span class="eyebrow">Data Protection</span>
            <h1>Privacy Policy</h1>
            <p>Your privacy matters to us. Learn how we handle your personal data.</p>
        </div>
    </section>

    <!-- ==================== PRIVACY CONTENT ==================== -->
    <section class="section privacy-section">
        <div class="container" style="max-width: 800px;">
            <div class="form-card" style="padding: 40px; color: var(--color-text); line-height: 1.6;">
                
                <h3 style="margin-bottom: 20px; color: var(--color-primary); border-bottom: 1px solid var(--color-glass-border); padding-bottom: 10px;">
                    1. Information We Collect
                </h3>
                <p style="margin-bottom: 15px;">
                    We collect personal information necessary to deliver your orders:
                </p>
                <p style="margin-bottom: 10px;">
                    - **Account Details**: Name, email address, phone number, and password hashes.
                </p>
                <p style="margin-bottom: 10px;">
                    - **Delivery Info**: Address, city, state, postal code, and geocoding coordinates (optional, when using the Live Location feature).
                </p>
                <p style="margin-bottom: 25px;">
                    - **Session Data**: IP addresses, browser info, and ordering history.
                </p>

                <h3 style="margin-bottom: 20px; color: var(--color-primary); border-bottom: 1px solid var(--color-glass-border); padding-bottom: 10px;">
                    2. How We Use Your Data
                </h3>
                <p style="margin-bottom: 10px;">
                    - To process and ship your orders.
                </p>
                <p style="margin-bottom: 10px;">
                    - To notify you about order statuses via Email and Mobile SMS.
                </p>
                <p style="margin-bottom: 25px;">
                    - To improve website loading speeds, customize layouts, and enhance the security of the admin portal.
                </p>

                <h3 style="margin-bottom: 20px; color: var(--color-primary); border-bottom: 1px solid var(--color-glass-border); padding-bottom: 10px;">
                    3. Cookies & Session Storage
                </h3>
                <p style="margin-bottom: 15px;">
                    We use cookies and browser storage (such as `LocalStorage` for your Wishlist / Liked products) to keep track of your shopping cart and favorites. You can disable cookies in browser options, but it may affect website login sessions.
                </p>
                <p style="margin-bottom: 25px;">
                    Your liked items list is stored client-side in your browser's local cache.
                </p>

                <h3 style="margin-bottom: 20px; color: var(--color-primary); border-bottom: 1px solid var(--color-glass-border); padding-bottom: 10px;">
                    4. Data Sharing & Third Parties
                </h3>
                <p style="margin-bottom: 15px;">
                    We do not sell, rent, or trade your data to third parties. We share details strictly with:
                </p>
                <p style="margin-bottom: 10px;">
                    - **SMS Service Gateway**: Fast2SMS for OTP/forgot password and order dispatches.
                </p>
                <p style="margin-bottom: 20px;">
                    - **Email Dispatch Servers**: PHPMailer SMTP routing to deliver order receipts.
                </p>

            </div>
        </div>
    </section>

<?php require_once '../includes/footer.php'; ?>
