<?php
$root = '../';
$pageTitle = 'Track Order | Foodo';
require_once '../includes/db_connect.php';
require_once '../includes/header.php';

if (!$isLoggedIn) {
    header('Location: login.php');
    exit();
}

$orderId = (int) ($_GET['order_id'] ?? 0);
$userId = $_SESSION['user_id'];

// ---- Fetch the order, making sure it belongs to this logged-in user ----
$stmt = $conn->prepare("SELECT * FROM orders WHERE order_id = ? AND user_id = ?");
$stmt->bind_param("ii", $orderId, $userId);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$order) {
    header('Location: ../index.php');
    exit();
}

// ---- Fetch order items ----
$items = [];
$itemStmt = $conn->prepare("SELECT * FROM order_items WHERE order_id = ?");
$itemStmt->bind_param("i", $orderId);
$itemStmt->execute();
$itemResult = $itemStmt->get_result();
while ($row = $itemResult->fetch_assoc()) { $items[] = $row; }
$itemStmt->close();
$conn->close();

// Setup status mapping
$statusSteps = [
    'preparing' => 1,
    'picked_up' => 2,
    'on_the_way' => 3,
    'delivered' => 4
];
$currentStep = $statusSteps[$order['order_status']] ?? 1;
?>

<!-- Leaflet.js Mapping Library CSS & JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<!-- Leaflet Routing Machine CSS & JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css" />
<script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js"></script>

<style>
.track-layout {
    display: grid;
    grid-template-columns: 1fr 1.5fr;
    gap: 30px;
    margin-top: 20px;
}

@media (max-width: 992px) {
    .track-layout {
        grid-template-columns: 1fr;
    }
}

/* Status Tracker Styles */
.track-status-card {
    background: var(--color-bg-secondary);
    border: 1px solid var(--color-glass-border);
    border-radius: var(--radius-md);
    padding: 30px;
    backdrop-filter: blur(10px);
}

.status-timeline {
    position: relative;
    padding-left: 30px;
    margin: 30px 0;
}

.status-timeline::before {
    content: '';
    position: absolute;
    left: 9px;
    top: 5px;
    bottom: 5px;
    width: 2px;
    background: rgba(255, 255, 255, 0.1);
}

.status-timeline-progress {
    position: absolute;
    left: 9px;
    top: 5px;
    width: 2px;
    background: var(--color-primary);
    transition: height 0.5s ease;
    height: <?php echo (($currentStep - 1) / 3) * 100; ?>%;
}

.timeline-step {
    position: relative;
    margin-bottom: 25px;
    opacity: 0.5;
    transition: opacity 0.3s;
}

.timeline-step:last-child {
    margin-bottom: 0;
}

.timeline-step.active, .timeline-step.completed {
    opacity: 1;
}

.timeline-dot {
    position: absolute;
    left: -30px;
    top: 3px;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: var(--color-bg-secondary);
    border: 2px solid rgba(255, 255, 255, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.6rem;
    color: transparent;
    z-index: 2;
    transition: all 0.3s;
}

.timeline-step.active .timeline-dot {
    border-color: var(--color-primary);
    background: var(--color-primary);
    box-shadow: 0 0 10px var(--color-primary);
}

.timeline-step.completed .timeline-dot {
    border-color: var(--color-success);
    background: var(--color-success);
    color: white;
}

.timeline-content h4 {
    margin: 0;
    font-size: 1.05rem;
    font-weight: 600;
}

.timeline-content p {
    margin: 5px 0 0;
    font-size: 0.85rem;
    color: var(--color-text-muted);
}

/* Rider Info Panel */
.rider-card {
    display: flex;
    align-items: center;
    gap: 15px;
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid var(--color-glass-border);
    padding: 15px 20px;
    border-radius: var(--radius-sm);
    margin-top: 25px;
}

.rider-photo {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid var(--color-primary);
}

.rider-info {
    flex: 1;
}

.rider-info h5 {
    margin: 0 0 3px 0;
    font-size: 0.95rem;
}

.rider-info p {
    margin: 0;
    font-size: 0.8rem;
    color: var(--color-text-muted);
}

.rider-call-btn {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: var(--color-primary-light);
    color: var(--color-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    border: none;
    cursor: pointer;
    font-size: 1rem;
    transition: all 0.3s;
}

.rider-call-btn:hover {
    background: var(--color-primary);
    color: white;
    transform: scale(1.05);
}

/* Map Card */
.map-card {
    background: var(--color-bg-secondary);
    border: 1px solid var(--color-glass-border);
    border-radius: var(--radius-md);
    padding: 15px;
    backdrop-filter: blur(10px);
    display: flex;
    flex-direction: column;
}

.map-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    padding: 0 10px;
}

.map-header h3 {
    margin: 0;
    font-size: 1.15rem;
    font-weight: 600;
}

.map-header .eta-badge {
    background: var(--color-primary-light);
    color: var(--color-primary);
    padding: 6px 14px;
    border-radius: var(--radius-full);
    font-size: 0.8rem;
    font-weight: 600;
}

#map {
    height: 480px;
    width: 100%;
    border-radius: var(--radius-sm);
    border: 1px solid var(--color-glass-border);
    background: #0f172a;
    z-index: 1;
}

/* Leaflet Layout tweaks for Dark Theme */
.leaflet-container {
    background: #0f172a !important;
}
.leaflet-bar {
    border: 1px solid var(--color-glass-border) !important;
    box-shadow: none !important;
}
.leaflet-bar a {
    background-color: var(--color-bg-secondary) !important;
    color: var(--color-text) !important;
    border-bottom: 1px solid var(--color-glass-border) !important;
}
.leaflet-bar a:hover {
    background-color: var(--color-glass-hover) !important;
    color: var(--color-primary) !important;
}

/* Hide leaflet-routing-machine info box */
.leaflet-routing-container {
    display: none !important;
}

/* Custom Pulsing Markers */
.custom-pulse-marker {
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
}

.pulse-core {
    width: 14px;
    height: 14px;
    border-radius: 50%;
    background-color: var(--color-primary);
    border: 2px solid white;
    z-index: 10;
}

.pulse-ring {
    position: absolute;
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background-color: rgba(255, 107, 53, 0.4);
    animation: markerPulse 1.8s infinite ease-out;
}

@keyframes markerPulse {
    0% { transform: scale(0.3); opacity: 1; }
    100% { transform: scale(1.2); opacity: 0; }
}

.scooter-icon-marker {
    background: var(--color-primary);
    color: white;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid white;
    box-shadow: 0 4px 10px rgba(0,0,0,0.5);
    font-size: 0.9rem;
    transition: transform 0.2s linear;
}
</style>

<!-- ==================== PAGE HEADER ==================== -->
<section class="page-header small">
    <div class="container" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:20px;">
        <div>
            <span class="eyebrow">Order ID: #TB<?php echo str_pad($order['order_id'], 5, '0', STR_PAD_LEFT); ?></span>
            <h1>Live Order Tracking</h1>
            <p>Your delicious food is on its way to your destination.</p>
        </div>
        <a href="my-orders.php" class="btn btn-outline"><i class="fa-solid fa-arrow-left"></i> My Orders</a>
    </div>
</section>

<!-- ==================== TRACKING CONTENT ==================== -->
<section class="section">
    <div class="container">
        <div class="track-layout">
            
            <!-- Left Side: Status Steps -->
            <div class="track-status-card">
                <h3>Delivery Status</h3>
                
                <div class="status-timeline">
                    <div class="status-timeline-progress" id="timelineProgress"></div>
                    
                    <div class="timeline-step <?php echo $currentStep >= 1 ? ($currentStep > 1 ? 'completed' : 'active') : ''; ?>" data-step="1">
                        <div class="timeline-dot"><i class="fa-solid fa-check"></i></div>
                        <div class="timeline-content">
                            <h4>Order Confirmed</h4>
                            <p>We have received your order at Loyola Kitchen.</p>
                        </div>
                    </div>
                    
                    <div class="timeline-step <?php echo $currentStep >= 2 ? ($currentStep > 2 ? 'completed' : 'active') : ''; ?>" data-step="2">
                        <div class="timeline-dot"><i class="fa-solid fa-check"></i></div>
                        <div class="timeline-content">
                            <h4>Preparing Food</h4>
                            <p>Chef is freshly cooking your ordered items.</p>
                        </div>
                    </div>
                    
                    <div class="timeline-step <?php echo $currentStep >= 3 ? ($currentStep > 3 ? 'completed' : 'active') : ''; ?>" data-step="3">
                        <div class="timeline-dot"><i class="fa-solid fa-check"></i></div>
                        <div class="timeline-content">
                            <h4>On the Way</h4>
                            <p>Our delivery partner is riding to your address.</p>
                        </div>
                    </div>
                    
                    <div class="timeline-step <?php echo $currentStep >= 4 ? 'completed' : ''; ?>" data-step="4">
                        <div class="timeline-dot"><i class="fa-solid fa-check"></i></div>
                        <div class="timeline-content">
                            <h4>Delivered</h4>
                            <p>Enjoy your hot meal!</p>
                        </div>
                    </div>
                </div>

                <!-- Countdown Timer Card -->
                <div class="countdown-timer-card" style="background: var(--color-primary-light); border: 1px solid rgba(255,107,53,0.2); padding: 15px 20px; border-radius: var(--radius-sm); margin-top: 20px; display: flex; align-items: center; justify-content: space-between; gap: 15px;">
                    <div style="display:flex; align-items:center; gap:12px;">
                        <i class="fa-solid fa-hourglass-half" style="font-size:1.4rem; color:var(--color-primary); animation: hourglassSpin 3s infinite linear;"></i>
                        <div>
                            <span style="font-size:0.75rem; text-transform:uppercase; font-weight:700; color:var(--color-primary); letter-spacing:0.05em; display:block;">Estimated Delivery</span>
                            <span id="countdownText" style="font-size:1.35rem; font-weight:800; font-family:monospace; color:white;">30:00</span>
                        </div>
                    </div>
                    <span id="countdownStatus" style="font-size:0.8rem; background:rgba(255,255,255,0.06); padding:4px 10px; border-radius:20px; font-weight:600; color:#fff;">Ticking...</span>
                </div>
                
                <style>
                @keyframes hourglassSpin {
                    0% { transform: rotate(0deg); }
                    40%, 60% { transform: rotate(180deg); }
                    100% { transform: rotate(360deg); }
                }
                </style>

                <!-- Rider Card Info -->
                <div class="rider-card" style="margin-top: 20px;">
                    <img src="https://placehold.co/100x100/FF4B3E/FFF8F0?text=Rider" alt="Rider Photo" class="rider-photo">
                    <div class="rider-info">
                        <h5>Ramesh Kumar</h5>
                        <p><i class="fa-solid fa-star" style="color:var(--color-gold);"></i> 4.9 Rating &bull; Foodo Partner</p>
                    </div>
                    <button class="rider-call-btn" onclick="alert('Calling rider Ramesh (+91 9876543210)...')" title="Call Rider">
                        <i class="fa-solid fa-phone"></i>
                    </button>
                </div>
            </div>
            
            <!-- Right Side: Interactive Map -->
            <div class="map-card">
                <div class="map-header">
                    <h3>Delivery Route</h3>
                    <span class="eta-badge" id="etaBadge">Calculating ETA...</span>
                </div>
                <div id="map"></div>
            </div>

        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Loyola College, Chennai (Restaurant Source)
    const restaurantCoords = [13.0622, 80.2339];
    
    // User Delivery Address variables passed from PHP
    const customerAddress = <?php echo json_encode($order['address'] . ', ' . $order['city'] . ', ' . $order['pincode']); ?>;
    const fallbackCoords = [12.8687, 80.0827]; // Urapakkam coordinates fallback
    
    // Setup Leaflet map, initial view set on Loyola College
    const map = L.map('map', {
        zoomControl: true,
        attributionControl: false
    }).setView(restaurantCoords, 13);
    
    // Modern Sleek Dark Map tiles from CartoDB (perfectly fits Foodo Dark Theme!)
    L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
        maxZoom: 20
    }).addTo(map);

    // Custom Icon Definitions
    const restaurantIcon = L.divIcon({
        className: 'custom-restaurant-marker',
        html: `<div style="background:#FF6B35; color:white; width:34px; height:34px; border-radius:50%; display:flex; align-items:center; justify-content:center; border:2px solid white; box-shadow:0 2px 8px rgba(0,0,0,0.5);"><i class="fa-solid fa-utensils"></i></div>`,
        iconSize: [34, 34],
        iconAnchor: [17, 17]
    });

    const customerIcon = L.divIcon({
        className: 'custom-customer-marker',
        html: `<div class="custom-pulse-marker"><div class="pulse-core"></div><div class="pulse-ring"></div></div>`,
        iconSize: [34, 34],
        iconAnchor: [17, 17]
    });

    const scooterIcon = L.divIcon({
        className: 'scooter-marker',
        html: `<div class="scooter-icon-marker"><i class="fa-solid fa-motorcycle"></i></div>`,
        iconSize: [32, 32],
        iconAnchor: [16, 16]
    });

    // Add Restaurant Marker
    L.marker(restaurantCoords, { icon: restaurantIcon })
        .addTo(map)
        .bindPopup("<b>Loyola Kitchen (Source)</b><br>Loyola College, Nungambakkam")
        .openPopup();

    let customerMarker;
    let scooterMarker;
    let routingControl;

    // Resolve address to coordinates using OpenStreetMap Nominatim Search API
    const geocodeUrl = `https://nominatim.openstreetmap.org/search?format=json&limit=1&q=${encodeURIComponent(customerAddress)}`;
    
    fetch(geocodeUrl)
        .then(res => res.json())
        .then(data => {
            let customerCoords = fallbackCoords;
            if (data && data.length > 0) {
                customerCoords = [parseFloat(data[0].lat), parseFloat(data[0].lon)];
            } else {
                console.log("Nominatim geocoding failed. Using Urapakkam fallback coordinates.");
            }
            
            initializeRoute(customerCoords);
        })
        .catch(err => {
            console.error("Geocoding API error:", err);
            // Fallback
            initializeRoute(fallbackCoords);
        });

    function initializeRoute(customerCoords) {
        // Add Customer Location Marker
        customerMarker = L.marker(customerCoords, { icon: customerIcon })
            .addTo(map)
            .bindPopup("<b>Your Delivery Address</b><br>" + customerAddress);

        // Fit map bounds to show both restaurant and customer
        const bounds = L.latLngBounds([restaurantCoords, customerCoords]);
        map.fitBounds(bounds, { padding: [50, 50] });

        // Initialize Scooter Marker at Restaurant
        scooterMarker = L.marker(restaurantCoords, { icon: scooterIcon }).addTo(map);

        // Initialize Leaflet Routing Machine
        routingControl = L.Routing.control({
            waypoints: [
                L.latLng(restaurantCoords[0], restaurantCoords[1]),
                L.latLng(customerCoords[0], customerCoords[1])
            ],
            createMarker: function() { return null; }, // Hide default markers
            lineOptions: {
                styles: [{ color: '#FF6B35', opacity: 0.8, weight: 5 }]
            },
            router: L.Routing.osrmv1({
                serviceUrl: 'https://router.project-osrm.org/route/v1'
            })
        }).addTo(map);

        routingControl.on('routesfound', function(e) {
            const routes = e.routes;
            const summary = routes[0].summary;
            
            // Format ETA (summary.totalTime is in seconds, distance in meters)
            const durationMins = Math.ceil(summary.totalTime / 60);
            const distanceKm = (summary.totalDistance / 1000).toFixed(1);
            
            document.getElementById('etaBadge').innerHTML = `ETA: ${durationMins} mins (${distanceKm} km)`;

            // Start scooter animation along the route coordinates
            const routeCoordinates = routes[0].coordinates;
            animateScooter(routeCoordinates);
        });
    }

    function animateScooter(coords) {
        let index = 0;
        const totalSteps = coords.length;
        const animationSpeed = 300; // time in ms between steps

        // Timeline elements to update dynamically during simulation
        const timelineProgress = document.getElementById('timelineProgress');
        const steps = document.querySelectorAll('.timeline-step');

        function move() {
            if (index < totalSteps) {
                const currentCoord = coords[index];
                scooterMarker.setLatLng([currentCoord.lat, currentCoord.lng]);
                
                // Keep the camera centered on the rider during active ride
                if (index % 10 === 0) {
                    map.panTo([currentCoord.lat, currentCoord.lng]);
                }

                // Simulate order status changes as the scooter travels
                const percentage = (index / totalSteps) * 100;
                
                if (percentage < 15) {
                    updateTimelineStep(1); // Confirmed
                } else if (percentage >= 15 && percentage < 40) {
                    updateTimelineStep(2); // Preparing
                } else if (percentage >= 40 && percentage < 98) {
                    updateTimelineStep(3); // On the way
                } else if (percentage >= 98) {
                    updateTimelineStep(4); // Delivered
                }

                // Update countdown text based on scooter progress (starting from 30 mins)
                const remainingProgress = (totalSteps - index) / totalSteps;
                const timeLeftSecs = Math.max(0, Math.ceil(remainingProgress * 30 * 60));
                const mins = Math.floor(timeLeftSecs / 60);
                const secs = timeLeftSecs % 60;
                
                if (percentage < 98) {
                    document.getElementById('countdownText').textContent = 
                        `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
                } else {
                    document.getElementById('countdownText').textContent = "Arrived! 🎉";
                    document.getElementById('countdownStatus').textContent = "Delivered";
                    document.getElementById('countdownStatus').style.color = "var(--color-success)";
                }

                index++;
                setTimeout(move, animationSpeed);
            } else {
                // Completed path
                scooterMarker.setLatLng(scooterMarker.getLatLng());
                updateTimelineStep(4);
                document.getElementById('countdownText').textContent = "Arrived! 🎉";
                document.getElementById('countdownStatus').textContent = "Delivered";
                document.getElementById('countdownStatus').style.color = "var(--color-success)";
                scooterMarker.bindPopup("<b>Rider has arrived!</b>").openPopup();
            }
        }

        // Start animation
        setTimeout(move, 2000); // 2 second delay before rider starts moving
    }

    function updateTimelineStep(activeStepNumber) {
        const steps = document.querySelectorAll('.timeline-step');
        const progress = document.getElementById('timelineProgress');
        
        progress.style.height = `${((activeStepNumber - 1) / 3) * 100}%`;

        steps.forEach(step => {
            const stepNum = parseInt(step.getAttribute('data-step'));
            if (stepNum < activeStepNumber) {
                step.className = 'timeline-step completed';
            } else if (stepNum === activeStepNumber) {
                step.className = 'timeline-step active';
            } else {
                step.className = 'timeline-step';
            }
        });
    }
});
</script>

<?php require_once '../includes/footer.php'; ?>
