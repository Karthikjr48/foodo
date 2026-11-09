<?php
/**
 * notify.php
 * -------------------------------------------------------
 * Foodo — Order Notification System
 * Handles Email (PHPMailer/Gmail SMTP) + SMS (Fast2SMS)
 * Called from checkout_process.php after order is placed.
 * -------------------------------------------------------
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/PHPMailer/Exception.php';
require_once __DIR__ . '/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// ============================================================
//  sendOrderEmail()
//  Sends a beautiful HTML order confirmation email.
// ============================================================
function sendOrderEmail($toEmail, $customerName, $orderId, $cartItems, $subtotal, $deliveryFee, $tax, $grandTotal, $paymentMethod, $address) {
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = MAIL_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = MAIL_USERNAME;
        $mail->Password   = MAIL_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = MAIL_PORT;

        // Recipients
        $mail->CharSet = 'UTF-8';
        $mail->setFrom(MAIL_FROM_EMAIL, MAIL_FROM_NAME);
        $mail->addAddress($toEmail, $customerName);

        // Content
        $mail->isHTML(true);
        $mail->Subject = '🍔 Order Confirmed! #' . str_pad($orderId, 5, '0', STR_PAD_LEFT) . ' - Foodo';
        $mail->Body    = buildEmailHTML($customerName, $orderId, $cartItems, $subtotal, $deliveryFee, $tax, $grandTotal, $paymentMethod, $address);
        $mail->AltBody = buildEmailPlain($customerName, $orderId, $grandTotal);

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log('Foodo Email Error: ' . $mail->ErrorInfo);
        
        // Fallback to PHP native mail() (useful if SMTP ports are blocked on hosting)
        $subject = '🍔 Order Confirmed! #' . str_pad($orderId, 5, '0', STR_PAD_LEFT) . ' - Foodo';
        $body    = buildEmailHTML($customerName, $orderId, $cartItems, $subtotal, $deliveryFee, $tax, $grandTotal, $paymentMethod, $address);
        
        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= "From: " . MAIL_FROM_NAME . " <" . MAIL_FROM_EMAIL . ">\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();
        
        return @mail($toEmail, $subject, $body, $headers);
    }
}

// ============================================================
//  sendPasswordResetEmail()
//  Sends a secure HTML password reset link email.
// ============================================================
function sendPasswordResetEmail($toEmail, $customerName, $resetLink) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = MAIL_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = MAIL_USERNAME;
        $mail->Password   = MAIL_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = MAIL_PORT;

        $mail->CharSet = 'UTF-8';
        $mail->setFrom(MAIL_FROM_EMAIL, MAIL_FROM_NAME);
        $mail->addAddress($toEmail, $customerName);

        $mail->isHTML(true);
        $mail->Subject = '🔒 Password Reset Request - Foodo';
        
        $mail->Body = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <title>Reset Password</title>
            <meta name='color-scheme' content='only dark'>
            <style>
                body, table, td, div, p, span, a {
                    background-color: #000000 !important;
                    color: #F0F0F0 !important;
                }
                .main-card {
                    background-color: #0F0F15 !important;
                    border: 1px solid rgba(255,255,255,0.08) !important;
                }
                .btn-grad {
                    background: linear-gradient(135deg, #FF6B35, #FFD166) !important;
                    color: #000000 !important;
                }
            </style>
        </head>
        <body style='margin:0; padding:0; background-color:#000000 !important; font-family: Arial, sans-serif; color:#F0F0F0 !important;'>
        <table width='100%' cellpadding='0' cellspacing='0' class='body-table' style='background-color:#000000 !important; padding: 40px 0;'>
          <tr>
            <td align='center' class='outer-cell' style='background-color:#000000 !important;'>
              <table width='600' cellpadding='0' cellspacing='0' class='main-card' style='background-color:#0F0F15 !important; border: 1px solid rgba(255,255,255,0.08) !important; border-radius:16px; overflow:hidden; box-shadow:0 8px 32px rgba(0,0,0,0.5);'>
                <tr>
                  <td style='background: linear-gradient(135deg, #FF6B35, #E85A2A) !important; padding: 40px; text-align:center;'>
                    <h1 style='margin:0; color:#ffffff !important; font-size:28px; font-weight:800; letter-spacing:1px; background:none !important;'>🍽️ Foodo</h1>
                    <p style='margin:6px 0 0; color:rgba(255,255,255,0.9) !important; font-size:14px; background:none !important;'>Premium Food Delivery</p>
                  </td>
                </tr>
                <tr>
                  <td style='padding:40px; color:#F0F0F0 !important; line-height:1.6; background-color:#0F0F15 !important;'>
                    <h2 style='margin-top:0; color:#FFD166 !important; font-size:22px; font-weight:700; background:none !important;'>Reset Your Password 🔒</h2>
                    <p style='font-size:15px; background:none !important;'>Hello <strong>$customerName</strong>,</p>
                    <p style='font-size:15px; background:none !important;'>We received a request to reset the password for your account on Foodo. Click the button below to choose a new password. This link is valid for 1 hour.</p>
                    <div style='text-align:center; margin:35px 0; background:none !important;'>
                      <a href='$resetLink' class='btn-grad' style='background: linear-gradient(135deg, #FF6B35, #FFD166) !important; color:#000000 !important; padding:14px 35px; text-decoration:none; font-weight:bold; border-radius:30px; display:inline-block; box-shadow: 0 4px 15px rgba(255,107,53,0.4); text-transform:uppercase; font-size:13px; letter-spacing:1px;'>Reset Password</a>
                    </div>
                    <p style='color:#8A8A9A !important; font-size:14px; background:none !important;'>If you didn't request a password reset, you can safely ignore this email. Your password won't change.</p>
                    <hr style='border:0; border-top:1px solid rgba(255,255,255,0.08); margin:30px 0; background:none !important;'>
                    <p style='font-size:11px; color:#8A8A9A !important; word-break:break-all; background:none !important;'>If the button doesn't work, copy and paste this URL into your browser:<br><a href='$resetLink' style='color:#FF6B35 !important; text-decoration:none; background:none !important;'>$resetLink</a></p>
                  </td>
                </tr>
                <tr>
                  <td style='background-color:#000000 !important; padding:25px; text-align:center; color:#8A8A9A !important; font-size:12px; border-top:1px solid rgba(255,255,255,0.08) !important;'>
                    <p style='margin:0; font-weight:600; background:none !important;'>Foodo Corporation</p>
                    <p style='margin:4px 0 0; background:none !important;'><a href='mailto:support@foodo.cu.ma' style='color:#FF6B35 !important; text-decoration:none; background:none !important;'>support@foodo.cu.ma</a></p>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
        </body>
        </html>";

        $mail->AltBody = "Hello $customerName,\n\nWe received a request to reset your password. Please copy and paste this link into your browser to reset it:\n$resetLink\n\nThis link is valid for 1 hour.";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log('Foodo Password Reset Email Error: ' . $mail->ErrorInfo);
        
        // Fallback to PHP native mail() (useful if SMTP ports are blocked on hosting)
        $subject = '🔒 Password Reset Request - Foodo';
        
        $body = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <title>Reset Password</title>
            <meta name='color-scheme' content='only dark'>
            <style>
                body, table, td, div, p, span, a {
                    background-color: #000000 !important;
                    color: #F0F0F0 !important;
                }
                .main-card {
                    background-color: #0F0F15 !important;
                    border: 1px solid rgba(255,255,255,0.08) !important;
                }
                .btn-grad {
                    background: linear-gradient(135deg, #FF6B35, #FFD166) !important;
                    color: #000000 !important;
                }
            </style>
        </head>
        <body style='margin:0; padding:0; background-color:#000000 !important; font-family: Arial, sans-serif; color:#F0F0F0 !important;'>
        <table width='100%' cellpadding='0' cellspacing='0' class='body-table' style='background-color:#000000 !important; padding: 40px 0;'>
          <tr>
            <td align='center' class='outer-cell' style='background-color:#000000 !important;'>
              <table width='600' cellpadding='0' cellspacing='0' class='main-card' style='background-color:#0F0F15 !important; border: 1px solid rgba(255,255,255,0.08) !important; border-radius:16px; overflow:hidden; box-shadow:0 8px 32px rgba(0,0,0,0.5);'>
                <tr>
                  <td style='background: linear-gradient(135deg, #FF6B35, #E85A2A) !important; padding: 40px; text-align:center;'>
                    <h1 style='margin:0; color:#ffffff !important; font-size:28px; font-weight:800; letter-spacing:1px; background:none !important;'>🍽️ Foodo</h1>
                    <p style='margin:6px 0 0; color:rgba(255,255,255,0.9) !important; font-size:14px; background:none !important;'>Premium Food Delivery</p>
                  </td>
                </tr>
                <tr>
                  <td style='padding:40px; color:#F0F0F0 !important; line-height:1.6; background-color:#0F0F15 !important;'>
                    <h2 style='margin-top:0; color:#FFD166 !important; font-size:22px; font-weight:700; background:none !important;'>Reset Your Password 🔒</h2>
                    <p style='font-size:15px; background:none !important;'>Hello <strong>$customerName</strong>,</p>
                    <p style='font-size:15px; background:none !important;'>We received a request to reset the password for your account on Foodo. Click the button below to choose a new password. This link is valid for 1 hour.</p>
                    <div style='text-align:center; margin:35px 0; background:none !important;'>
                      <a href='$resetLink' class='btn-grad' style='background: linear-gradient(135deg, #FF6B35, #FFD166) !important; color:#000000 !important; padding:14px 35px; text-decoration:none; font-weight:bold; border-radius:30px; display:inline-block; box-shadow: 0 4px 15px rgba(255,107,53,0.4); text-transform:uppercase; font-size:13px; letter-spacing:1px;'>Reset Password</a>
                    </div>
                    <p style='color:#8A8A9A !important; font-size:14px; background:none !important;'>If you didn't request a password reset, you can safely ignore this email. Your password won't change.</p>
                    <hr style='border:0; border-top:1px solid rgba(255,255,255,0.08); margin:30px 0; background:none !important;'>
                    <p style='font-size:11px; color:#8A8A9A !important; word-break:break-all; background:none !important;'>If the button doesn't work, copy and paste this URL into your browser:<br><a href='$resetLink' style='color:#FF6B35 !important; text-decoration:none; background:none !important;'>$resetLink</a></p>
                  </td>
                </tr>
                <tr>
                  <td style='background-color:#000000 !important; padding:25px; text-align:center; color:#8A8A9A !important; font-size:12px; border-top:1px solid rgba(255,255,255,0.08) !important;'>
                    <p style='margin:0; font-weight:600; background:none !important;'>Foodo Corporation</p>
                    <p style='margin:4px 0 0; background:none !important;'><a href='mailto:support@foodo.cu.ma' style='color:#FF6B35 !important; text-decoration:none; background:none !important;'>support@foodo.cu.ma</a></p>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
        </body>
        </html>";

        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= "From: " . MAIL_FROM_NAME . " <" . MAIL_FROM_EMAIL . ">\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();
        
        return @mail($toEmail, $subject, $body, $headers);
    }
}

// ============================================================
//  sendPasswordResetSMS()
//  Sends a secure password reset link via Fast2SMS API.
// ============================================================
function sendPasswordResetSMS($phone, $resetLink) {
    $message = "Foodo: Reset your password using this link: $resetLink (Valid for 1 hour)";

    $url = 'https://www.fast2sms.com/dev/bulkV2';
    $data = [
        'route'   => 'q',
        'message' => $message,
        'numbers' => $phone,
    ];

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $url,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode($data),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => [
            'authorization: ' . FAST2SMS_API_KEY,
            'Content-Type: application/json',
        ],
        CURLOPT_TIMEOUT        => 10,
    ]);

    $response = curl_exec($ch);
    $curlError = curl_error($ch);
    curl_close($ch);

    $logData = date('[Y-m-d H:i:s] ') . "Password Reset Phone: " . $phone . ", Response: " . $response . ", cURL Error: " . $curlError . "\n";
    file_put_contents(dirname(__DIR__) . '/sms_debug.log', $logData, FILE_APPEND);

    if ($curlError) {
        error_log('Foodo Password Reset SMS cURL Error: ' . $curlError);
        return false;
    }

    $result = json_decode($response, true);
    if (isset($result['return']) && $result['return'] === true) {
        return true;
    } else {
        error_log('Foodo Password Reset SMS Error: ' . $response);
        return false;
    }
}

// ============================================================
//  sendOrderSMS()
//  Sends a short SMS via Fast2SMS API.
// ============================================================
function sendOrderSMS($phone, $orderId, $grandTotal) {
    $orderIdFormatted = str_pad($orderId, 5, '0', STR_PAD_LEFT);
    $message = "Hi! Your Foodo order #$orderIdFormatted has been confirmed. Total: Rs.$grandTotal. We are preparing your food. Thank you!";

    $url = 'https://www.fast2sms.com/dev/bulkV2';
    $data = [
        'route'   => 'q',
        'message' => $message,
        'numbers' => $phone,
    ];

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $url,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode($data),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => [
            'authorization: ' . FAST2SMS_API_KEY,
            'Content-Type: application/json',
        ],
        CURLOPT_TIMEOUT        => 10,
    ]);

    $response = curl_exec($ch);
    $curlError = curl_error($ch);
    curl_close($ch);

    // Save debug logs inside the project folder
    $logData = date('[Y-m-d H:i:s] ') . "Phone: " . $phone . ", Order: " . $orderId . ", Total: " . $grandTotal . ", Response: " . $response . ", cURL Error: " . $curlError . "\n";
    file_put_contents(dirname(__DIR__) . '/sms_debug.log', $logData, FILE_APPEND);

    if ($curlError) {
        error_log('Foodo SMS cURL Error: ' . $curlError);
        return false;
    }

    $result = json_decode($response, true);
    if (isset($result['return']) && $result['return'] === true) {
        return true;
    } else {
        error_log('Foodo SMS Error: ' . $response);
        return false;
    }
}

// ============================================================
//  buildEmailHTML()
//  Builds a beautiful HTML email body.
// ============================================================
function buildEmailHTML($name, $orderId, $cartItems, $subtotal, $deliveryFee, $tax, $grandTotal, $payment, $address) {
    $orderIdFormatted = str_pad($orderId, 5, '0', STR_PAD_LEFT);
    $firstName = explode(' ', $name)[0];
    $paymentLabel = [
        'cod'  => '💵 Cash on Delivery',
        'upi'  => '📱 UPI',
        'card' => '💳 Credit / Debit Card',
    ][$payment] ?? $payment;

    // Build items rows
    $itemRows = '';
    foreach ($cartItems as $item) {
        $lineTotal = number_format($item['price'] * $item['quantity'], 0);
        $itemRows .= "
        <tr>
            <td style='padding:14px 0; border-bottom:1px solid rgba(255,255,255,0.08); color:#F0F0F0; font-size:14px; font-weight:500;'>
                {$item['food_name']}
            </td>
            <td style='padding:14px 0; border-bottom:1px solid rgba(255,255,255,0.08); color:#8A8A9A; font-size:14px; text-align:center;'>
                × {$item['quantity']}
            </td>
            <td style='padding:14px 0; border-bottom:1px solid rgba(255,255,255,0.08); color:#FF6B35; font-size:14px; font-weight:600; text-align:right;'>
                ₹{$lineTotal}
            </td>
        </tr>";
    }

    return "
<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Order Confirmation</title>
    <meta name='color-scheme' content='only dark'>
    <style>
        body, table, td, div, p, span, a {
            background-color: #000000 !important;
            color: #F0F0F0 !important;
        }
        .main-card {
            background-color: #0F0F15 !important;
            border: 1px solid rgba(255,255,255,0.08) !important;
        }
        .inner-card {
            background-color: #161622 !important;
            border: 1px solid rgba(255,255,255,0.05) !important;
        }
        .header-banner {
            background: linear-gradient(135deg, #FF6B35, #E85A2A) !important;
        }
        .header-banner * {
            background: none !important;
            color: #ffffff !important;
        }
    </style>
</head>
<body style='margin:0; padding:0; background-color:#000000 !important; font-family: system-ui, -apple-system, BlinkMacSystemFont, Arial, sans-serif; color:#F0F0F0 !important;'>

<table width='100%' cellpadding='0' cellspacing='0' class='body-table' style='background-color:#000000 !important; padding: 40px 0;'>
  <tr>
    <td align='center' class='outer-cell' style='background-color:#000000 !important;'>
      <table width='600' cellpadding='0' cellspacing='0' class='main-card' style='background-color:#0F0F15 !important; border-radius:16px; overflow:hidden; border: 1px solid rgba(255,255,255,0.08) !important; box-shadow:0 8px 32px rgba(0,0,0,0.5);'>

        <!-- Header Banner -->
        <tr>
          <td align='center' class='header-banner' style='background: linear-gradient(135deg, #FF6B35, #E85A2A) !important; padding: 40px 20px; text-align:center;'>
            <table cellpadding='0' cellspacing='0' style='margin: 0 auto; background:none !important;'>
              <tr style='background:none !important;'>
                <td style='font-size:32px; vertical-align:middle; padding-right:10px; background:none !important;'>🍔</td>
                <td style='color:#ffffff !important; font-size:32px; font-weight:800; letter-spacing:1px; vertical-align:middle; background:none !important;'>Foodo</td>
              </tr>
            </table>
            <p style='margin:8px 0 0; color:rgba(255,255,255,0.9) !important; font-size:14px; font-weight:500; background:none !important;'>Your Favorite Food, Delivered Fast</p>
          </td>
        </tr>

        <!-- Success Message -->
        <tr>
          <td style='padding:40px 40px 10px; text-align:center; background-color:#0F0F15 !important;'>
            <div style='display:inline-block; background-color:rgba(0, 214, 143, 0.1) !important; border: 1px solid #00D68F !important; border-radius:30px; padding:8px 20px; margin-bottom: 20px;'>
              <span style='color:#00D68F !important; font-size:14px; font-weight:700; background:none !important;'>✓ ORDER CONFIRMED</span>
            </div>
            <h2 style='margin:0 0 8px; color:#FFD166 !important; font-size:24px; font-weight:800; background:none !important;'>Hey $firstName,</h2>
            <p style='margin:0; color:#8A8A9A !important; font-size:16px; line-height:1.5; background:none !important;'>We are preparing your delicious meal. Here are your order details:</p>
          </td>
        </tr>

        <!-- Order ID Card -->
        <tr>
          <td style='padding:15px 40px; background-color:#0F0F15 !important;'>
            <table width='100%' cellpadding='0' cellspacing='0' class='inner-card' style='background-color:#161622 !important; border-left:4px solid #FF6B35 !important; border-radius:4px; padding:16px; border-top: 1px solid rgba(255,255,255,0.05) !important; border-right: 1px solid rgba(255,255,255,0.05) !important; border-bottom: 1px solid rgba(255,255,255,0.05) !important;'>
              <tr style='background:none !important;'>
                <td style='background:none !important;'>
                  <p style='margin:0; color:#8A8A9A !important; font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:1px; background:none !important;'>Order Reference</p>
                  <p style='margin:4px 0 0; color:#FF6B35 !important; font-size:22px; font-weight:800; background:none !important;'>#$orderIdFormatted</p>
                </td>
              </tr>
            </table>
          </td>
        </tr>

        <!-- Items Table -->
        <tr>
          <td style='padding:20px 40px; background-color:#0F0F15 !important;'>
            <h3 style='color:#FFD166 !important; font-size:16px; font-weight:700; margin:0 0 15px; border-bottom:1px solid rgba(255,255,255,0.08) !important; padding-bottom:8px; background:none !important;'>🛒 Items Ordered</h3>
            <table width='100%' cellpadding='0' cellspacing='0' style='background:none !important;'>
              $itemRows
            </table>
          </td>
        </tr>

        <!-- Summary Totals -->
        <tr>
          <td style='padding:0 40px 30px; background-color:#0F0F15 !important;'>
            <table width='100%' cellpadding='0' cellspacing='0' class='inner-card' style='background-color:#161622 !important; border-radius:8px; padding:20px; border: 1px solid rgba(255,255,255,0.05) !important;'>
              <tr style='background:none !important;'>
                <td style='padding:6px 0; color:#8A8A9A !important; font-size:14px; background:none !important;'>Subtotal</td>
                <td style='padding:6px 0; text-align:right; color:#F0F0F0 !important; font-size:14px; font-weight:600; background:none !important;'>₹" . number_format($subtotal, 0) . "</td>
              </tr>
              <tr style='background:none !important;'>
                <td style='padding:6px 0; color:#8A8A9A !important; font-size:14px; background:none !important;'>Delivery Partner Fee</td>
                <td style='padding:6px 0; text-align:right; color:#F0F0F0 !important; font-size:14px; font-weight:600; background:none !important;'>₹" . number_format($deliveryFee, 0) . "</td>
              </tr>
              <tr style='background:none !important;'>
                <td style='padding:6px 0; color:#8A8A9A !important; font-size:14px; background:none !important;'>Taxes & Charges (5%)</td>
                <td style='padding:6px 0; text-align:right; color:#F0F0F0 !important; font-size:14px; font-weight:600; background:none !important;'>₹" . number_format($tax, 0) . "</td>
              </tr>
              <tr style='background:none !important;'>
                <td colspan='2' style='padding:15px 0 0; border-top:1px solid rgba(255,255,255,0.08) !important; margin-top:10px; background:none !important;'>
                  <table width='100%' cellpadding='0' cellspacing='0' style='background:none !important;'>
                    <tr style='background:none !important;'>
                      <td style='color:#FFD166 !important; font-size:18px; font-weight:800; background:none !important;'>Grand Total</td>
                      <td style='text-align:right; color:#FF6B35 !important; font-size:20px; font-weight:800; background:none !important;'>₹" . number_format($grandTotal, 0) . "</td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>
          </td>
        </tr>

        <!-- Split Info Details -->
        <tr>
          <td style='padding:0 40px 40px; background-color:#0F0F15 !important;'>
            <table width='100%' cellpadding='0' cellspacing='0' style='background:none !important;'>
              <tr style='background:none !important;'>
                <td width='48%' class='inner-card' style='background-color:#161622 !important; border-radius:8px; padding:20px; vertical-align:top; border: 1px solid rgba(255,255,255,0.05) !important;'>
                  <p style='margin:0 0 8px; color:#8A8A9A !important; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:1px; background:none !important;'>📍 Delivery Address</p>
                  <p style='margin:0; color:#F0F0F0 !important; font-size:14px; line-height:1.5; font-weight:500; background:none !important;'>$address</p>
                </td>
                <td width='4%' style='background:none !important;'></td>
                <td width='48%' class='inner-card' style='background-color:#161622 !important; border-radius:8px; padding:20px; vertical-align:top; border: 1px solid rgba(255,255,255,0.05) !important;'>
                  <p style='margin:0 0 8px; color:#8A8A9A !important; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:1px; background:none !important;'>💳 Payment Details</p>
                  <p style='margin:0; color:#F0F0F0 !important; font-size:14px; font-weight:600; background:none !important;'>$paymentLabel</p>
                  <p style='margin:8px 0 0; color:#8A8A9A !important; font-size:12px; background:none !important;'>Delivery expected in 25-30 mins</p>
                </td>
              </tr>
            </table>
          </td>
        </tr>

        <!-- Footer -->
        <tr>
          <td style='background-color:#000000 !important; padding:30px 40px; text-align:center; border-top: 1px solid rgba(255,255,255,0.08) !important;'>
            <p style='margin:0; color:#ffffff !important; font-size:14px; font-weight:600; background:none !important;'>Thank you for choosing Foodo! 🧡</p>
            <p style='margin:6px 0 0; color:#8A8A9A !important; font-size:12px; background:none !important;'>Need help? Contact us at <a href='mailto:support@foodo.cu.ma' style='color:#FF6B35 !important; text-decoration:none; font-weight:600; background:none !important;'>support@foodo.cu.ma</a></p>
          </td>
        </tr>

      </table>
    </td>
  </tr>
</table>

</body>
</html>";
}

// ============================================================
//  buildEmailPlain()
//  Plain text fallback for email.
// ============================================================
function buildEmailPlain($name, $orderId, $grandTotal) {
    $orderIdFormatted = str_pad($orderId, 5, '0', STR_PAD_LEFT);
    return "Hi $name! Your Foodo order #$orderIdFormatted has been confirmed. Total: Rs.$grandTotal. Estimated delivery: 25-30 minutes. Thank you!";
}

// ============================================================
//  sendAdminOrderEmail()
//  Sends a notification email to the admin for a new order.
// ============================================================
function sendAdminOrderEmail($orderId, $customerName, $phone, $email, $cartItems, $grandTotal, $paymentMethod, $address) {
    $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = MAIL_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = MAIL_USERNAME;
        $mail->Password   = MAIL_PASSWORD;
        $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = MAIL_PORT;

        $mail->CharSet = 'UTF-8';
        $mail->setFrom(MAIL_FROM_EMAIL, MAIL_FROM_NAME);
        $mail->addAddress(MAIL_USERNAME, 'Foodo Admin'); // Sent to admin email (MAIL_USERNAME)

        $mail->isHTML(true);
        $orderIdFormatted = str_pad($orderId, 5, '0', STR_PAD_LEFT);
        $mail->Subject = '🚨 New Order Received! #' . $orderIdFormatted . ' - Foodo Admin';
        
        $itemRows = '';
        foreach ($cartItems as $item) {
            $itemRows .= "<li>{$item['food_name']} × {$item['quantity']} - ₹" . number_format($item['price'] * $item['quantity'], 0) . "</li>";
        }

        $mail->Body = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <title>New Order Alert</title>
            <style>
                body { background-color: #000000; color: #F0F0F0; font-family: sans-serif; padding: 20px; }
                .card { background-color: #0F0F15; border: 1px solid rgba(255,255,255,0.08); border-radius: 12px; padding: 30px; max-width: 600px; margin: 0 auto; }
                .header { background: linear-gradient(135deg, #FF6B35, #FF4B3E); padding: 20px; border-radius: 8px; text-align: center; color: #fff; font-weight: bold; font-size: 20px; margin-bottom: 25px; }
                .details { border-bottom: 1px solid rgba(255,255,255,0.08); padding-bottom: 20px; margin-bottom: 20px; }
                .details p { margin: 8px 0; }
                .btn { background: #FF6B35; color: #000000 !important; text-decoration: none; padding: 12px 25px; border-radius: 6px; display: inline-block; font-weight: bold; margin-top: 15px; }
            </style>
        </head>
        <body>
            <div class='card'>
                <div class='header'>🚨 New Order Alert #$orderIdFormatted</div>
                <div class='details'>
                    <h3>Customer Information</h3>
                    <p><strong>Name:</strong> $customerName</p>
                    <p><strong>Phone:</strong> $phone</p>
                    <p><strong>Email:</strong> $email</p>
                    <p><strong>Address:</strong> $address</p>
                </div>
                <div class='details'>
                    <h3>Order Summary</h3>
                    <ul>$itemRows</ul>
                    <p><strong>Total Paid:</strong> ₹" . number_format($grandTotal, 0) . " ($paymentMethod)</p>
                </div>
                <div style='text-align: center;'>
                    <a href='http://localhost/food-ordering/admin/orders.php' class='btn'>View in Admin Panel</a>
                </div>
            </div>
        </body>
        </html>";

        $mail->send();
        return true;
    } catch (\Exception $e) {
        error_log('Admin New Order Email Error: ' . $mail->ErrorInfo);
        return false;
    }
}

// ============================================================
//  sendOrderStatusUpdateEmail()
//  Sends a premium status update email to the customer.
// ============================================================
function sendOrderStatusUpdateEmail($toEmail, $customerName, $orderId, $status, $grandTotal) {
    $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = MAIL_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = MAIL_USERNAME;
        $mail->Password   = MAIL_PASSWORD;
        $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = MAIL_PORT;

        $mail->CharSet = 'UTF-8';
        $mail->setFrom(MAIL_FROM_EMAIL, MAIL_FROM_NAME);
        $mail->addAddress($toEmail, $customerName);

        $mail->isHTML(true);
        $orderIdFormatted = str_pad($orderId, 5, '0', STR_PAD_LEFT);
        
        // Define status specific values
        $statusLabels = [
            'preparing'  => ['label' => 'Preparing Food 🍳', 'color' => '#FF8C00', 'desc' => 'Our Chef is preparing your delicious meal with fresh ingredients.'],
            'picked_up'  => ['label' => 'Picked Up 📦', 'color' => '#1E90FF', 'desc' => 'Our delivery partner has picked up your food package.'],
            'on_the_way' => ['label' => 'On The Way 🛵', 'color' => '#FF6B35', 'desc' => 'Our rider is zooming towards your address. Get ready!'],
            'delivered'  => ['label' => 'Delivered 🎉', 'color' => '#00D68F', 'desc' => 'Your food has been delivered successfully. Enjoy your hot meal!'],
            'cancelled'  => ['label' => 'Cancelled ❌', 'color' => '#FF4757', 'desc' => 'Your order has been cancelled. Please contact support if you have questions.']
        ];
        
        $info = $statusLabels[$status] ?? ['label' => ucfirst($status), 'color' => '#FF6B35', 'desc' => 'Your order status has changed.'];
        
        $mail->Subject = "🛵 Order #$orderIdFormatted Update: {$info['label']}";
        
        $mail->Body = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <title>Order Status Update</title>
            <style>
                body { background-color: #000000; color: #F0F0F0; font-family: system-ui, sans-serif; padding: 20px; margin: 0; }
                .card { background-color: #0F0F15; border: 1px solid rgba(255,255,255,0.08); border-radius: 16px; padding: 40px; max-width: 550px; margin: 40px auto; text-align: center; box-shadow: 0 8px 32px rgba(0,0,0,0.5); }
                .status-badge { display: inline-block; padding: 8px 24px; border-radius: 30px; font-weight: 700; color: #fff; background-color: {$info['color']}; margin-bottom: 25px; text-transform: uppercase; font-size: 13px; letter-spacing: 1px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); }
                h2 { color: #FFD166; font-size: 22px; font-weight: 800; margin: 0 0 15px 0; }
                .description { color: #8A8A9A; font-size: 15px; line-height: 1.6; margin-bottom: 30px; }
                .btn { background: linear-gradient(135deg, #FF6B35, #FFD166); color: #000000 !important; text-decoration: none; padding: 14px 35px; border-radius: 30px; display: inline-block; font-weight: bold; box-shadow: 0 4px 15px rgba(255,107,53,0.4); text-transform: uppercase; font-size: 13px; letter-spacing: 1px; }
                .footer { border-top: 1px solid rgba(255,255,255,0.08); padding-top: 25px; margin-top: 35px; color: #8A8A9A; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class='card'>
                <div class='status-badge'>{$info['label']}</div>
                <h2>Hi $customerName,</h2>
                <p class='description'>{$info['desc']}</p>
                
                <div style='margin-bottom: 35px;'>
                    <a href='http://localhost/food-ordering/pages/track-order.php?order_id=$orderId' class='btn'>Track Your Order Live</a>
                </div>
                
                <div class='footer'>
                    <p>Order Reference: <strong>#$orderIdFormatted</strong> &bull; Total: <strong>₹" . number_format($grandTotal, 0) . "</strong></p>
                    <p>Foodo Corporation &bull; support@foodo.cu.ma</p>
                </div>
            </div>
        </body>
        </html>";

        $mail->send();
        return true;
    } catch (\Exception $e) {
        error_log('Customer Status Update Email Error: ' . $mail->ErrorInfo);
        return false;
    }
}
