<?php
/**
 * send_bulk_test.php
 * -------------------------------------------------------
 * Bulk Email & SMS Test Dispatch Tool
 * Open in browser to trigger notifications to all registered customers:
 *   localhost/food-ordering/includes/send_bulk_test.php
 * -------------------------------------------------------
 */
require_once 'db_connect.php';
require_once 'notify.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Fetch all registered customers from database
$query = "SELECT user_id, full_name, email, phone, address FROM users";
$result = $conn->query($query);
$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

$action = $_POST['action'] ?? '';
$statusLogs = [];

if ($action === 'send_all') {
    // Mock order details for the test campaign
    $mockOrderId = 77777;
    $mockSubtotal = 498;
    $mockDeliveryFee = 40;
    $mockTax = 25;
    $mockGrandTotal = 563;
    $mockPaymentMethod = 'cod';
    $mockAddress = 'Loyola College, Nungambakkam, Chennai - 600034';

    $mockCartItems = [
        [
            'food_name' => 'Premium Margherita Pizza',
            'price' => 299,
            'quantity' => 1
        ],
        [
            'food_name' => 'Chilli Garlic Hakka Noodles',
            'price' => 199,
            'quantity' => 1
        ]
    ];

    foreach ($users as $user) {
        $email = trim($user['email']);
        $name = trim($user['full_name']);
        
        $emailStatus = 'Skipped (No Email)';

        // 1. Dispatch HTML Order Email
        if (!empty($email)) {
            $mailSent = sendOrderEmail(
                $email,
                $name,
                $mockOrderId,
                $mockCartItems,
                $mockSubtotal,
                $mockDeliveryFee,
                $mockTax,
                $mockGrandTotal,
                $mockPaymentMethod,
                $mockAddress
            );
            $emailStatus = $mailSent ? '✓ Sent Successfully' : '✗ Failed';
        }

        $statusLogs[] = [
            'name' => $name,
            'email' => $email,
            'email_status' => $emailStatus
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bulk Test Dispatcher | Foodo</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #0A0A0F;
            color: #F0F0F0;
            margin: 0;
            padding: 40px;
            background-image: 
                radial-gradient(ellipse at 10% 20%, rgba(255,107,53,0.06) 0%, transparent 40%),
                radial-gradient(ellipse at 90% 80%, rgba(255,209,102,0.04) 0%, transparent 40%);
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            margin-bottom: 40px;
        }
        .header h1 {
            font-family: 'Poppins', sans-serif;
            font-size: 32px;
            font-weight: 800;
            background: linear-gradient(135deg, #FF6B35, #FFD166);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin: 0;
        }
        .header p {
            color: #8A8A9A;
            margin-top: 8px;
        }
        .card {
            background: #12121A;
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.4);
            backdrop-filter: blur(20px);
            margin-bottom: 30px;
        }
        .customer-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            text-align: left;
        }
        .customer-table th {
            color: #FFD166;
            border-bottom: 2px solid rgba(255,255,255,0.08);
            padding: 12px 10px;
            font-weight: 600;
        }
        .customer-table td {
            border-bottom: 1px solid rgba(255,255,255,0.05);
            padding: 14px 10px;
            font-size: 14px;
        }
        .email-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .email-gmail { background: rgba(219, 68, 85, 0.15); color: #db4437; }
        .email-outlook { background: rgba(0, 120, 215, 0.15); color: #0078d7; }
        .email-icloud { background: rgba(142, 142, 147, 0.15); color: #8e8e93; }
        .email-other { background: rgba(255, 107, 53, 0.15); color: #FF6B35; }
        
        .btn-action {
            background: linear-gradient(135deg, #FF6B35, #FFD166);
            border: none;
            padding: 14px 35px;
            color: #0A0A0F;
            font-weight: bold;
            border-radius: 30px;
            font-size: 15px;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(255,107,53,0.3);
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255,107,53,0.5);
        }
        .log-section {
            background: #0A0A0F;
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 8px;
            padding: 20px;
            font-family: monospace;
            max-height: 300px;
            overflow-y: auto;
            text-align: left;
        }
        .log-row {
            margin-bottom: 8px;
            font-size: 13px;
        }
        .log-success { color: #00D68F; }
        .log-failed { color: #FF4757; }
        a {
            color: #FF6B35;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🍽️ Foodo Test Dispatcher Center</h1>
            <p>Simulate order confirmation emails (Outlook, iCloud, Gmail) and Fast2SMS alerts for all customers.</p>
        </div>

        <div class="card">
            <h3>👥 Registered Customers (<?php echo count($users); ?>)</h3>
            <table class="customer-table">
                <thead>
                    <tr>
                        <th>Customer Name</th>
                        <th>Email Address</th>
                        <th>Provider</th>
                        <th>Phone Number</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): 
                        $email = strtolower(trim($user['email']));
                        $provider = 'Other';
                        $badgeClass = 'email-other';
                        if (strpos($email, 'gmail.com') !== false) {
                            $provider = 'Gmail';
                            $badgeClass = 'email-gmail';
                        } elseif (strpos($email, 'outlook.com') !== false || strpos($email, 'hotmail.com') !== false || strpos($email, 'live.com') !== false) {
                            $provider = 'Outlook';
                            $badgeClass = 'email-outlook';
                        } elseif (strpos($email, 'icloud.com') !== false || strpos($email, 'mac.com') !== false) {
                            $provider = 'iCloud';
                            $badgeClass = 'email-icloud';
                        }
                    ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($user['full_name']); ?></strong></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><span class="email-badge <?php echo $badgeClass; ?>"><?php echo $provider; ?></span></td>
                            <td><?php echo htmlspecialchars($user['phone'] ?? 'N/A'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <div style="margin-top: 30px; text-align: center;">
                <form method="post">
                    <input type="hidden" name="action" value="send_all">
                    <button type="submit" class="btn-action">
                        <i class="fa-solid fa-paper-plane" style="margin-right: 8px;"></i> Launch Test Email Campaign
                    </button>
                </form>
            </div>
        </div>

        <?php if (!empty($statusLogs)): ?>
            <div class="card">
                <h3>🚀 Live Campaign Dispatch Logs</h3>
                <div class="log-section">
                    <?php foreach ($statusLogs as $log): ?>
                        <div class="log-row">
                            🔑 <strong><?php echo htmlspecialchars($log['name']); ?></strong> 
                            &lt;<?php echo htmlspecialchars($log['email']); ?>&gt;:<br>
                            &nbsp;&nbsp;✉️ Email: 
                            <span class="<?php echo strpos($log['email_status'], '✓') !== false ? 'log-success' : 'log-failed'; ?>">
                                <?php echo htmlspecialchars($log['email_status']); ?>
                            </span>
                        </div>
                        <hr style="border: 0; border-top: 1px solid rgba(255,255,255,0.05); margin: 8px 0;">
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <p style="text-align: center;"><a href="../index.php"><i class="fa-solid fa-arrow-left"></i> Back to food ordering website</a></p>
    </div>
</body>
</html>
