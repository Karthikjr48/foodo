<?php
/**
 * refund.php
 * -------------------------------------------------------
 * Refund & Cancellation Policy Page
 * -------------------------------------------------------
 */
$root = '../';
$pageTitle = 'Refund Policy | Foodo';
require_once '../includes/header.php';
?>

    <!-- ==================== PAGE HEADER ==================== -->
    <section class="page-header small">
        <div class="container">
            <span class="eyebrow">Customer Support</span>
            <h1>Refund & Cancellation</h1>
            <p>Read about our policies on order cancellations, replacements, and refunds.</p>
        </div>
    </section>

    <!-- ==================== REFUND CONTENT ==================== -->
    <section class="section refund-section">
        <div class="container" style="max-width: 800px;">
            <div class="form-card" style="padding: 40px; color: var(--color-text); line-height: 1.6;">
                
                <h3 style="margin-bottom: 20px; color: var(--color-primary); border-bottom: 1px solid var(--color-glass-border); padding-bottom: 10px;">
                    1. Order Cancellation
                </h3>
                <p style="margin-bottom: 15px;">
                    - **Before preparation**: You can request order cancellation within 5 minutes of placing your order by contacting support.
                </p>
                <p style="margin-bottom: 25px;">
                    - **After preparation**: Once the kitchen begins preparing the food, we are unable to accept cancellations, as items are freshly prepared to order.
                </p>

                <h3 style="margin-bottom: 20px; color: var(--color-primary); border-bottom: 1px solid var(--color-glass-border); padding-bottom: 10px;">
                    2. Refund Eligibility
                </h3>
                <p style="margin-bottom: 15px;">
                    You are eligible for a replacement or full refund under the following conditions:
                </p>
                <p style="margin-bottom: 10px;">
                    - **Damaged Items**: The food arrives spilled, tampered, or damaged.
                </p>
                <p style="margin-bottom: 10px;">
                    - **Incorrect Orders**: Items delivered do not match the dishes ordered.
                </p>
                <p style="margin-bottom: 25px;">
                    - **Extremely Delayed**: Delivery takes significantly longer than the promised time (excluding weather conditions).
                </p>

                <h3 style="margin-bottom: 20px; color: var(--color-primary); border-bottom: 1px solid var(--color-glass-border); padding-bottom: 10px;">
                    3. Refund Process
                </h3>
                <p style="margin-bottom: 15px;">
                    - Refunds for payments made online (Card/UPI) will be processed back to the original payment source.
                </p>
                <p style="margin-bottom: 25px;">
                    - Refund processing takes **5 to 7 business days** to reflect in your bank account, depending on your bank's policies.
                </p>

                <h3 style="margin-bottom: 20px; color: var(--color-primary); border-bottom: 1px solid var(--color-glass-border); padding-bottom: 10px;">
                    4. Contact Us for Claims
                </h3>
                <p style="margin-bottom: 15px;">
                    To initiate a refund claim:
                </p>
                <p style="margin-bottom: 10px;">
                    - Go to our **Contact Page** and submit a message, or call our customer hotline at **+91 9042655027**.
                </p>
                <p style="margin-bottom: 15px;">
                    - Please provide your **Order ID** (shown in your order history receipt) and a description of the issue to speed up verification.
                </p>

            </div>
        </div>
    </section>

<?php require_once '../includes/footer.php'; ?>
