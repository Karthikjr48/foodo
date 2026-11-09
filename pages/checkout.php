<?php
$root = '../';
$pageTitle = 'Checkout | Foodo';
require_once '../includes/db_connect.php';
require_once '../includes/header.php';
require_once '../includes/csrf.php';

if (!$isLoggedIn) {
    header('Location: login.php?error=login_required');
    exit();
}

$userId = $_SESSION['user_id'];

// ---- Fetch cart items ----
$cartItems = [];
$stmt = $conn->prepare("
    SELECT c.cart_id, c.quantity, f.food_id, f.food_name, f.price
    FROM cart c JOIN foods f ON c.food_id = f.food_id
    WHERE c.user_id = ?
");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) { $cartItems[] = $row; }
$stmt->close();

// Empty cart? Send back to cart page.
if (count($cartItems) === 0) {
    header('Location: cart.php');
    exit();
}

// ---- Fetch user details to pre-fill the form ----
$userStmt = $conn->prepare("SELECT full_name, email, phone, address, city, state, pincode FROM users WHERE user_id = ?");
$userStmt->bind_param("i", $userId);
$userStmt->execute();
$userData = $userStmt->get_result()->fetch_assoc();
$userStmt->close();

// ---- Calculate totals ----
$subtotal = 0;
foreach ($cartItems as $item) { $subtotal += $item['price'] * $item['quantity']; }
$deliveryFee = 40;
$tax = round($subtotal * 0.05, 2);
$grandTotal = $subtotal + $deliveryFee + $tax;

$conn->close();
?>

    <!-- ==================== PAGE HEADER ==================== -->
    <section class="page-header small">
        <div class="container">
            <span class="eyebrow">Step 2 of 2</span>
            <h1>Checkout</h1>
            <p>Enter your delivery details to place the order.</p>
        </div>
    </section>

    <!-- ==================== CHECKOUT SECTION ==================== -->
    <section class="section checkout-section">
        <div class="container checkout-layout">

            <form class="checkout-form" id="checkoutForm" action="../includes/checkout_process.php" method="post">
                <?php csrfInput(); ?>

                <div class="form-card">
                    <h3><i class="fa-solid fa-user"></i> Customer Details</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="fullName">Full Name</label>
                            <input type="text" id="fullName" name="fullName" value="<?php echo htmlspecialchars($userData['full_name']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($userData['phone']); ?>" pattern="[0-9]{10}" maxlength="10" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($userData['email']); ?>" required>
                    </div>
                </div>

                <div class="form-card">
                    <h3 style="display:flex; align-items:center; justify-content:space-between; width:100%; flex-wrap:wrap; gap:10px;">
                        <span><i class="fa-solid fa-location-dot"></i> Delivery Address</span>
                        <button type="button" id="getLocationBtn" class="btn btn-outline btn-sm" style="padding:6px 12px; font-size:0.75rem; gap:4px;">
                            <i class="fa-solid fa-location-crosshairs"></i> Get Live Location
                        </button>
                    </h3>
                    <div class="form-group" style="margin-top:15px;">
                        <label for="address">Full Address</label>
                        <textarea id="address" name="address" rows="3" required><?php echo htmlspecialchars($userData['address'] ?? ''); ?></textarea>
                    </div>
                    <div class="form-row three-col">
                        <div class="form-group">
                            <label for="city">City</label>
                            <select id="city" name="city" required>
                                <option value="">Select City</option>
                                <?php 
                                $cities = [
                                    'Chennai', 'Coimbatore', 'Madurai', 'Tiruchirappalli', 'Salem', 
                                    'Tirunelveli', 'Vellore', 'Thoothukudi', 'Thanjavur', 'Dindigul', 
                                    'Erode', 'Nagercoil', 'Karur', 'Kanchipuram', 'Tiruvannamalai'
                                ];
                                $currentCity = $userData['city'] ?? '';
                                foreach ($cities as $c) {
                                    $selected = (strcasecmp($currentCity, $c) === 0) ? 'selected' : '';
                                    echo "<option value=\"$c\" $selected>$c</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="state">State</label>
                            <input type="text" id="state" name="state" value="<?php echo htmlspecialchars($userData['state'] ?? 'Tamil Nadu'); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="pincode">Pincode</label>
                            <input type="text" id="pincode" name="pincode" value="<?php echo htmlspecialchars($userData['pincode'] ?? ''); ?>" pattern="[0-9]{6}" maxlength="6" required>
                        </div>
                    </div>
                </div>

                <script>
                document.addEventListener('DOMContentLoaded', () => {
                    const locationBtn = document.getElementById('getLocationBtn');
                    if (locationBtn) {
                        locationBtn.addEventListener('click', () => {
                            if (!navigator.geolocation) {
                                alert('Geolocation is not supported by your browser.');
                                return;
                            }
                            
                            locationBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Fetching...';
                            locationBtn.disabled = true;
                            
                            navigator.geolocation.getCurrentPosition(
                                (position) => {
                                    const lat = position.coords.latitude;
                                    const lon = position.coords.longitude;
                                    
                                    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}`)
                                        .then(res => res.json())
                                        .then(data => {
                                            if (data && data.address) {
                                                const addr = data.address;
                                                
                                                // Construct address string (door/house number first)
                                                const houseNumber = addr.house_number || addr.building || addr.amenity || 'Door No. ____';
                                                const road = addr.road || addr.pedestrian || '';
                                                const suburb = addr.suburb || addr.neighbourhood || addr.village || '';
                                                const county = addr.county || addr.district || '';
                                                const state = addr.state || 'Tamil Nadu';
                                                const postcode = addr.postcode || '';
                                                
                                                const streetAddress = road ? `${houseNumber}, ${road}` : houseNumber;
                                                const parts = [streetAddress, suburb, county].filter(Boolean);
                                                const fullAddress = parts.join(', ') || data.display_name;
                                                
                                                document.getElementById('address').value = fullAddress;
                                                document.getElementById('state').value = state;
                                                document.getElementById('pincode').value = postcode;
                                                
                                                // Try to match detected city in dropdown
                                                const citySelect = document.getElementById('city');
                                                const searchTerms = [
                                                    addr.city, 
                                                    addr.town, 
                                                    addr.municipality, 
                                                    addr.county, 
                                                    addr.district, 
                                                    addr.suburb, 
                                                    addr.village
                                                ].filter(Boolean).map(term => term.toLowerCase());
                                                
                                                let matched = false;
                                                for (let i = 0; i < citySelect.options.length; i++) {
                                                    const opt = citySelect.options[i];
                                                    if (opt.value === '') continue;
                                                    
                                                    const optVal = opt.value.toLowerCase();
                                                    const isMatch = searchTerms.some(term => 
                                                        term.includes(optVal) || optVal.includes(term)
                                                    );
                                                    
                                                    if (isMatch) {
                                                        citySelect.selectedIndex = i;
                                                        matched = true;
                                                        break;
                                                    }
                                                }
                                                if (!matched) {
                                                    // Fallback to select first option if not matched
                                                    citySelect.value = "";
                                                }
                                                
                                                locationBtn.innerHTML = '<i class="fa-solid fa-check"></i> Location Filled';
                                            } else {
                                                alert('Could not resolve location details.');
                                                locationBtn.innerHTML = '<i class="fa-solid fa-location-crosshairs"></i> Get Live Location';
                                            }
                                            locationBtn.disabled = false;
                                        })
                                        .catch(err => {
                                            console.error(err);
                                            alert('Error fetching reverse geocode.');
                                            locationBtn.innerHTML = '<i class="fa-solid fa-location-crosshairs"></i> Get Live Location';
                                            locationBtn.disabled = false;
                                        });
                                },
                                (err) => {
                                    console.error(err);
                                    alert('Could not access your location. Please check site permissions.');
                                    locationBtn.innerHTML = '<i class="fa-solid fa-location-crosshairs"></i> Get Live Location';
                                    locationBtn.disabled = false;
                                },
                                { enableHighAccuracy: true, timeout: 8000 }
                            );
                        });
                    }
                });
                </script>

                <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
                <script>
                document.addEventListener('DOMContentLoaded', () => {
                    const checkoutForm = document.getElementById('checkoutForm');
                    const submitBtn = document.querySelector('button[form="checkoutForm"]');
                    const originalSubmitBtnHTML = submitBtn.innerHTML;

                    // Coupon system variables & elements
                    let subtotal = <?php echo $subtotal; ?>;
                    let deliveryFee = <?php echo $deliveryFee; ?>;
                    let tax = <?php echo $tax; ?>;
                    let grandTotal = <?php echo $grandTotal; ?>;
                    let discount = 0;
                    let couponCode = '';

                    const promoCodeInput = document.getElementById('promoCodeInput');
                    const applyPromoBtn = document.getElementById('applyPromoBtn');
                    const promoMessage = document.getElementById('promoMessage');
                    const discountRow = document.getElementById('discountRow');
                    const appliedCodeText = document.getElementById('appliedCodeText');
                    const discountValText = document.getElementById('discountValText');
                    const summaryTotalText = document.querySelector('.summary-total span:last-child');
                    
                    const hiddenGrandTotal = document.getElementById('hiddenGrandTotal');
                    const hiddenDiscountAmount = document.getElementById('hiddenDiscountAmount');
                    const hiddenCouponCode = document.getElementById('hiddenCouponCode');

                    applyPromoBtn.addEventListener('click', () => {
                        const enteredCode = promoCodeInput.value.trim().toUpperCase();
                        if (enteredCode === '') {
                            showPromoMsg('Please enter a coupon code.', 'danger');
                            return;
                        }

                        if (enteredCode === 'FOODO10') {
                            discount = Math.round(subtotal * 0.10); // 10% off
                            couponCode = 'FOODO10';
                            showPromoMsg('Coupon applied! 10% off subtotal.', 'success');
                        } else if (enteredCode === 'LOYOLA') {
                            discount = 50; // Flat ₹50 off
                            if (subtotal < 100) {
                                showPromoMsg('Coupon requires subtotal of at least ₹100.', 'danger');
                                return;
                            }
                            couponCode = 'LOYOLA';
                            showPromoMsg('Coupon applied! Flat ₹50 off.', 'success');
                        } else {
                            showPromoMsg('Invalid coupon code.', 'danger');
                            resetCoupon();
                            return;
                        }

                        discountRow.style.display = 'flex';
                        appliedCodeText.textContent = couponCode;
                        discountValText.textContent = discount;
                        
                        const newGrandTotal = Math.max(0, subtotal + deliveryFee + tax - discount);
                        summaryTotalText.textContent = `₹${newGrandTotal.toLocaleString()}`;
                        
                        hiddenGrandTotal.value = newGrandTotal;
                        hiddenDiscountAmount.value = discount;
                        hiddenCouponCode.value = couponCode;
                    });

                    function showPromoMsg(msg, type) {
                        promoMessage.textContent = msg;
                        promoMessage.style.display = 'block';
                        if (type === 'success') {
                            promoMessage.style.color = 'var(--color-success)';
                        } else {
                            promoMessage.style.color = 'var(--color-danger)';
                        }
                    }

                    function resetCoupon() {
                        discount = 0;
                        couponCode = '';
                        discountRow.style.display = 'none';
                        summaryTotalText.textContent = `₹${(subtotal + deliveryFee + tax).toLocaleString()}`;
                        hiddenGrandTotal.value = subtotal + deliveryFee + tax;
                        hiddenDiscountAmount.value = 0;
                        hiddenCouponCode.value = '';
                    }

                    checkoutForm.addEventListener('submit', function (e) {
                        // Find which payment method is selected
                        const selectedPayment = document.querySelector('input[name="payment"]:checked').value;
                        
                        // COD runs standard flow
                        if (selectedPayment === 'cod') {
                            return;
                        }

                        // Online payment (UPI or Card)
                        e.preventDefault();

                        // Check HTML5 validation first
                        if (!checkoutForm.checkValidity()) {
                            checkoutForm.reportValidity();
                            return;
                        }

                        // Show spinner / loading status
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Processing Payment...';

                        // Get details to pass to backend / Razorpay prefill
                        const payload = {
                            fullName: document.getElementById('fullName').value,
                            phone: document.getElementById('phone').value,
                            email: document.getElementById('email').value,
                            address: document.getElementById('address').value,
                            city: document.getElementById('city').value,
                            state: document.getElementById('state').value,
                            pincode: document.getElementById('pincode').value,
                            coupon_code: hiddenCouponCode.value
                        };

                        // AJAX request to create order, passing the coupon code
                        fetch('../includes/create_razorpay_order.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify(payload)
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (!data.success) {
                                alert('Error creating payment order: ' + data.error);
                                resetSubmitBtn();
                                return;
                            }

                            if (data.mock) {
                                // Mock Mode (Keys not configured yet)
                                // We show a brief custom alert / modal overlay to make it look premium
                                showMockPaymentOverlay(data, () => {
                                    submitFormWithPayment(data.razorpay_order_id, 'pay_mock_' + Math.random().toString(36).substr(2, 9), 'mock_signature_approved');
                                });
                            } else {
                                // Real Mode (Keys configured)
                                const options = {
                                    "key": data.key_id,
                                    "amount": data.amount,
                                    "currency": data.currency,
                                    "name": "Foodo",
                                    "description": "Payment for Food Order",
                                    "order_id": data.razorpay_order_id,
                                    "handler": function (response) {
                                        submitFormWithPayment(response.razorpay_order_id, response.razorpay_payment_id, response.razorpay_signature);
                                    },
                                    "prefill": {
                                        "name": payload.fullName,
                                        "email": payload.email,
                                        "contact": payload.phone
                                    },
                                    "theme": {
                                        "color": "#ff4d4d"
                                    },
                                    "modal": {
                                        "ondismiss": function () {
                                            resetSubmitBtn();
                                        }
                                    }
                                };
                                const rzp1 = new Razorpay(options);
                                rzp1.open();
                            }
                        })
                        .catch(err => {
                            console.error('AJAX order creation error:', err);
                            alert('Failed to initiate payment. Please try again.');
                            resetSubmitBtn();
                        });
                    });

                    function resetSubmitBtn() {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalSubmitBtnHTML;
                    }

                    function submitFormWithPayment(orderId, paymentId, signature) {
                        // Append payment values as hidden inputs
                        appendHiddenInput('razorpay_order_id', orderId);
                        appendHiddenInput('razorpay_payment_id', paymentId);
                        appendHiddenInput('razorpay_signature', signature);
                        
                        // Submit form programmatically
                        checkoutForm.submit();
                    }

                    function appendHiddenInput(name, value) {
                        let input = checkoutForm.querySelector(`input[name="${name}"]`);
                        if (!input) {
                            input = document.createElement('input');
                            input.setAttribute('type', 'hidden');
                            input.setAttribute('name', name);
                            checkoutForm.appendChild(input);
                        }
                        input.value = value;
                    }

                    function showMockPaymentOverlay(data, onSuccess) {
                        // Create a beautiful premium overlay showing payment simulation
                        const overlay = document.createElement('div');
                        overlay.style.position = 'fixed';
                        overlay.style.top = '0';
                        overlay.style.left = '0';
                        overlay.style.width = '100vw';
                        overlay.style.height = '100vh';
                        overlay.style.backgroundColor = 'rgba(15, 23, 42, 0.9)'; // Sleek slate color
                        overlay.style.zIndex = '9999';
                        overlay.style.display = 'flex';
                        overlay.style.alignItems = 'center';
                        overlay.style.justifyContent = 'center';
                        overlay.style.backdropFilter = 'blur(8px)';
                        overlay.style.color = '#fff';
                        overlay.style.fontFamily = "'Outfit', sans-serif";

                        const amountFormatted = (data.amount / 100).toFixed(2);

                        overlay.innerHTML = `
                            <div style="background: linear-gradient(135deg, #1e293b, #0f172a); padding: 40px; border-radius: 20px; text-align: center; max-width: 400px; width: 90%; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5), 0 10px 10px -5px rgba(0, 0, 0, 0.4); border: 1px solid rgba(255,255,255,0.1);">
                                <div style="font-size: 3rem; margin-bottom: 20px;">🛡️</div>
                                <h3 style="margin-bottom: 10px; font-weight: 600;">Razorpay Sandbox Simulator</h3>
                                <p style="color: #94a3b8; font-size: 0.9rem; margin-bottom: 25px;">You are in Developer Demo Mode. Keys are not configured yet, simulating payment gateway...</p>
                                <div style="background: rgba(255,255,255,0.05); padding: 15px; border-radius: 12px; margin-bottom: 30px; border: 1px solid rgba(255,255,255,0.05);">
                                    <div style="font-size: 0.8rem; color: #94a3b8; margin-bottom: 5px;">Amount to Pay</div>
                                    <div style="font-size: 1.8rem; font-weight: 700; color: #ff4d4d;">₹${amountFormatted}</div>
                                </div>
                                <div style="display: flex; gap: 15px;">
                                    <button id="mockCancelBtn" style="flex: 1; padding: 12px 20px; border-radius: 10px; border: 1px solid rgba(255,255,255,0.1); background: transparent; color: #fff; font-weight: 500; cursor: pointer; transition: all 0.2s;">Cancel</button>
                                    <button id="mockSuccessBtn" style="flex: 1; padding: 12px 20px; border-radius: 10px; border: none; background: #ff4d4d; color: #fff; font-weight: 600; cursor: pointer; box-shadow: 0 4px 6px -1px rgba(255, 77, 77, 0.3); transition: all 0.2s;">Pay Successfully</button>
                                </div>
                            </div>
                        `;

                        document.body.appendChild(overlay);

                        document.getElementById('mockCancelBtn').addEventListener('click', () => {
                            document.body.removeChild(overlay);
                            resetSubmitBtn();
                        });

                        document.getElementById('mockSuccessBtn').addEventListener('click', () => {
                            // Show processing status
                            const container = overlay.querySelector('div');
                            container.innerHTML = `
                                <div style="font-size: 3rem; margin-bottom: 20px; animation: spin-loader 1.5s linear infinite;">🔄</div>
                                <h3 style="margin-bottom: 10px; font-weight: 600;">Processing Simulation...</h3>
                                <p style="color: #94a3b8; font-size: 0.9rem; margin-bottom: 0;">Authorizing secure mockup token...</p>
                                <style>
                                    @keyframes spin-loader { 100% { transform: rotate(360deg); } }
                                </style>
                            `;
                            setTimeout(() => {
                                document.body.removeChild(overlay);
                                onSuccess();
                            }, 1500);
                        });
                    }
                });
                </script>


                <div class="form-card">
                    <h3><i class="fa-solid fa-wallet"></i> Payment Method</h3>
                    <div class="payment-options">
                        <label class="payment-option">
                            <input type="radio" name="payment" value="cod" checked>
                            <span class="payment-radio"></span>
                            <i class="fa-solid fa-money-bill-wave"></i>
                            <div><strong>Cash on Delivery</strong><p>Pay when your order arrives</p></div>
                        </label>
                        <label class="payment-option">
                            <input type="radio" name="payment" value="upi">
                            <span class="payment-radio"></span>
                            <i class="fa-solid fa-mobile-screen-button"></i>
                            <div><strong>UPI</strong><p>Pay using Google Pay, PhonePe, Paytm</p></div>
                        </label>
                        <label class="payment-option">
                            <input type="radio" name="payment" value="card">
                            <span class="payment-radio"></span>
                            <i class="fa-solid fa-credit-card"></i>
                            <div><strong>Credit / Debit Card</strong><p>Visa, Mastercard, RuPay accepted</p></div>
                        </label>
                    </div>
                </div>

                <!-- Hidden totals, recomputed and re-verified server-side in checkout_process.php -->
                <input type="hidden" name="subtotal" value="<?php echo $subtotal; ?>">
                <input type="hidden" name="delivery_fee" value="<?php echo $deliveryFee; ?>">
                <input type="hidden" name="tax" value="<?php echo $tax; ?>">
                <input type="hidden" name="grand_total" id="hiddenGrandTotal" value="<?php echo $grandTotal; ?>">
                <input type="hidden" name="discount_amount" id="hiddenDiscountAmount" value="0">
                <input type="hidden" name="coupon_code" id="hiddenCouponCode" value="">
            </form>

            <aside class="cart-summary checkout-summary">
                <h3>Order Summary</h3>
                <?php foreach ($cartItems as $item): ?>
                    <div class="checkout-item">
                        <span><?php echo htmlspecialchars($item['food_name']); ?> <em>× <?php echo $item['quantity']; ?></em></span>
                        <span>₹<?php echo number_format($item['price'] * $item['quantity'], 0); ?></span>
                    </div>
                <?php endforeach; ?>

                <div class="summary-divider"></div>
                <div class="summary-row"><span>Subtotal</span><span>₹<?php echo number_format($subtotal, 0); ?></span></div>
                <div class="summary-row"><span>Delivery Fee</span><span>₹<?php echo number_format($deliveryFee, 0); ?></span></div>
                <div class="summary-row"><span>Taxes (5%)</span><span>₹<?php echo number_format($tax, 0); ?></span></div>
                
                <!-- Promo Code section -->
                <div class="promo-section" style="margin: 15px 0; border: 1px dashed rgba(255,255,255,0.15); border-radius: var(--radius-sm); padding: 12px; background: rgba(255,255,255,0.02);">
                    <div style="display:flex; gap:8px;">
                        <input type="text" id="promoCodeInput" placeholder="Coupon (FOODO10 / LOYOLA)" style="flex:1; padding:8px 12px; border-radius:6px; border:1px solid rgba(255,255,255,0.1); background:rgba(0,0,0,0.2); color:#fff; font-size:0.8rem; text-transform:uppercase;">
                        <button type="button" id="applyPromoBtn" class="btn btn-primary btn-sm" style="padding:8px 16px; font-size:0.8rem; border-radius:6px; font-weight:600; width:auto; height:auto; line-height:normal; margin:0;">Apply</button>
                    </div>
                    <p id="promoMessage" style="margin:8px 0 0 0; font-size:0.75rem; display:none; font-family:var(--font-body);"></p>
                </div>
                
                <div class="summary-row" id="discountRow" style="display:none; color: var(--color-success); font-weight:600;">
                    <span>Discount (<span id="appliedCodeText"></span>)</span>
                    <span>-₹<span id="discountValText">0</span></span>
                </div>
                
                <div class="summary-divider"></div>
                <div class="summary-row summary-total"><span>Grand Total</span><span>₹<?php echo number_format($grandTotal, 0); ?></span></div>

                <button type="submit" form="checkoutForm" class="btn btn-primary btn-block">Place Order <i class="fa-solid fa-check"></i></button>
                <a href="cart.php" class="btn btn-outline btn-block">Back to Cart</a>
            </aside>

        </div>
    </section>

<?php require_once '../includes/footer.php'; ?>
