<?php
/**
 * terms.php
 * -------------------------------------------------------
 * Terms & Conditions Page
 * -------------------------------------------------------
 */
$root = '../';
$pageTitle = 'Terms & Conditions | Foodo';
require_once '../includes/header.php';
?>

    <!-- ==================== PAGE HEADER ==================== -->
    <section class="page-header small">
        <div class="container">
            <span class="eyebrow">Legal & Compliance</span>
            <h1>Terms & Conditions</h1>
            <p>Please read these terms carefully before placing your order on Foodo.</p>
        </div>
    </section>

    <!-- ==================== TERMS CONTENT ==================== -->
    <section class="section terms-section">
        <div class="container" style="max-width: 800px;">
            <div class="form-card" style="padding: 40px; color: var(--color-text); line-height: 1.6;">
                
                <h3 style="margin-bottom: 20px; color: var(--color-primary); border-bottom: 1px solid var(--color-glass-border); padding-bottom: 10px;">
                    1. Acceptance of Terms
                </h3>
                <p style="margin-bottom: 25px;">
                    By accessing and using Foodo (via our website or local server), you agree to be bound by these Terms and Conditions. If you do not agree, please do not use our services.
                </p>

                <h3 style="margin-bottom: 20px; color: var(--color-primary); border-bottom: 1px solid var(--color-glass-border); padding-bottom: 10px;">
                    2. User Eligibility & Accounts
                </h3>
                <p style="margin-bottom: 15px;">
                    - You must be at least 18 years old or accessing the site under parent/guardian supervision.
                </p>
                <p style="margin-bottom: 25px;">
                    - You are responsible for keeping your login credentials confidential. Any activity under your account is your sole responsibility.
                </p>

                <h3 style="margin-bottom: 20px; color: var(--color-primary); border-bottom: 1px solid var(--color-glass-border); padding-bottom: 10px;">
                    3. Ordering & Pricing
                </h3>
                <p style="margin-bottom: 15px;">
                    - All orders placed are subject to kitchen availability. We reserve the right to decline or limit orders at our discretion.
                </p>
                <p style="margin-bottom: 25px;">
                    - Prices listed include standard taxes. Delivery charges are calculated dynamically and shown clearly in the cart summary before checkout.
                </p>

                <h3 style="margin-bottom: 20px; color: var(--color-primary); border-bottom: 1px solid var(--color-glass-border); padding-bottom: 10px;">
                    4. Delivery & Live Tracking
                </h3>
                <p style="margin-bottom: 15px;">
                    - We strive to deliver within our estimated delivery window (usually 25-35 minutes). However, traffic delays, weather conditions, or surge hours may affect timelines.
                </p>
                <p style="margin-bottom: 25px;">
                    - Accurate location information (using our Get Live Location API) is essential. Delivery failures due to wrong addresses will not be eligible for instant cancellation.
                </p>

                <h3 style="margin-bottom: 20px; color: var(--color-primary); border-bottom: 1px solid var(--color-glass-border); padding-bottom: 10px;">
                    5. Liability & Governing Law
                </h3>
                <p style="margin-bottom: 15px;">
                    Foodo is not liable for indirect damages arising from website disruptions or third-party delivery partners. 
                </p>
                <p style="margin-bottom: 20px;">
                    These terms shall be governed by and construed in accordance with the laws of Tamil Nadu, India.
                </p>

            </div>
        </div>
    </section>

<?php require_once '../includes/footer.php'; ?>
