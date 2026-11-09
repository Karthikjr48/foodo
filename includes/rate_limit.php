<?php
/**
 * rate_limit.php
 * -------------------------------------------------------
 * Reusable session-based Rate Limiter.
 * Prevents spamming and brute force attempts on sensitive forms.
 * -------------------------------------------------------
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$timeWindow = 30;  // Time window in seconds
$maxRequests = 5;  // Max requests allowed within the window

$currentScript = $_SERVER['SCRIPT_NAME'];
$currentTime = time();

// Initialize session tracking for the current script
if (!isset($_SESSION['rate_limit'][$currentScript])) {
    $_SESSION['rate_limit'][$currentScript] = [
        'start_time' => $currentTime,
        'count' => 1
    ];
} else {
    $sessionData = &$_SESSION['rate_limit'][$currentScript];
    $timePassed = $currentTime - $sessionData['start_time'];

    if ($timePassed < $timeWindow) {
        $sessionData['count']++;
        
        if ($sessionData['count'] > $maxRequests) {
            $retryAfter = $timeWindow - $timePassed;
            
            // Check if it is an AJAX request
            $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') 
                      || (isset($_SERVER['CONTENT_TYPE']) && strpos(strtolower($_SERVER['CONTENT_TYPE']), 'application/json') !== false)
                      || (isset($_SERVER['HTTP_ACCEPT']) && strpos(strtolower($_SERVER['HTTP_ACCEPT']), 'application/json') !== false);

            if ($isAjax) {
                // Return a clean JSON response
                http_response_code(429);
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'error' => "Too many requests. Please wait $retryAfter seconds before trying again."
                ]);
            } else {
                // Return a premium styled HTML error page matching Foodo theme
                http_response_code(429);
                ?>
                <!DOCTYPE html>
                <html lang="en">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Too Many Requests | Foodo Security</title>
                    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700&family=Inter:wght@400;500&display=swap" rel="stylesheet">
                    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
                    <style>
                        :root {
                            --color-danger: #FF4757;
                            --color-bg: #0A0A0F;
                            --color-surface: #12121A;
                            --color-glass-border: rgba(255, 255, 255, 0.08);
                            --font-heading: 'Poppins', sans-serif;
                            --font-body: 'Inter', sans-serif;
                        }
                        body {
                            background-color: var(--color-bg);
                            color: #F0F0F0;
                            font-family: var(--font-body);
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            min-height: 100vh;
                            margin: 0;
                            padding: 20px;
                            box-sizing: border-box;
                        }
                        .security-card {
                            background-color: var(--color-surface);
                            border: 1px solid var(--color-glass-border);
                            border-radius: 16px;
                            padding: 40px;
                            max-width: 500px;
                            width: 100%;
                            text-align: center;
                            box-shadow: 0 16px 48px rgba(0, 0, 0, 0.45);
                            backdrop-filter: blur(10px);
                        }
                        .icon-wrap {
                            font-size: 3.5rem;
                            color: var(--color-danger);
                            margin-bottom: 20px;
                            animation: shake 0.5s ease-in-out;
                        }
                        h1 {
                            font-family: var(--font-heading);
                            margin: 0 0 10px 0;
                            font-size: 1.8rem;
                            color: #fff;
                        }
                        p {
                            color: #8A8A9A;
                            font-size: 0.95rem;
                            line-height: 1.6;
                            margin: 0 0 25px 0;
                        }
                        .countdown-badge {
                            display: inline-block;
                            background: rgba(255, 71, 87, 0.1);
                            border: 1px solid rgba(255, 71, 87, 0.2);
                            color: var(--color-danger);
                            padding: 8px 20px;
                            border-radius: 20px;
                            font-weight: 600;
                            font-size: 0.9rem;
                        }
                        @keyframes shake {
                            0%, 100% { transform: translateX(0); }
                            25% { transform: translateX(-8px); }
                            75% { transform: translateX(8px); }
                        }
                    </style>
                </head>
                <body>
                    <div class="security-card">
                        <div class="icon-wrap"><i class="fa-solid fa-shield-halved"></i></div>
                        <h1>Security Threshold Limit Exceeded</h1>
                        <p>We detected multiple rapid requests from your session. To protect our servers, request throttling is active.</p>
                        <div class="countdown-badge">Please retry in <?php echo $retryAfter; ?> seconds</div>
                    </div>
                </body>
                </html>
                <?php
            }
            exit();
        }
    } else {
        // Reset the window timer and request count
        $_SESSION['rate_limit'][$currentScript] = [
            'start_time' => $currentTime,
            'count' => 1
        ];
    }
}
?>
