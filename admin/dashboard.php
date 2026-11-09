<?php
require_once 'includes/admin_check.php';
require_once '../includes/db_connect.php';

$pageTitle = 'Dashboard';
$activePage = 'dashboard';

$totalOrders = $conn->query("SELECT COUNT(*) AS c FROM orders")->fetch_assoc()['c'];
$totalUsers = $conn->query("SELECT COUNT(*) AS c FROM users")->fetch_assoc()['c'];
$totalFoods = $conn->query("SELECT COUNT(*) AS c FROM foods")->fetch_assoc()['c'];
$totalRevenue = $conn->query("SELECT SUM(grand_total) AS s FROM orders WHERE order_status != 'cancelled'")->fetch_assoc()['s'] ?? 0;
$pendingOrders = $conn->query("SELECT COUNT(*) AS c FROM orders WHERE order_status = 'preparing'")->fetch_assoc()['c'];

$recentOrders = [];
$res = $conn->query("SELECT * FROM orders ORDER BY order_date DESC LIMIT 5");
while ($row = $res->fetch_assoc()) { $recentOrders[] = $row; }

// ---- Fetch last 7 days of sales for Chart.js ----
$salesData = [];
$salesQuery = "
    SELECT DATE(order_date) AS o_date, SUM(grand_total) AS daily_total 
    FROM orders 
    WHERE order_status != 'cancelled' 
      AND order_date >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
    GROUP BY DATE(order_date) 
    ORDER BY DATE(order_date) ASC
";
$salesRes = $conn->query($salesQuery);

$chartLabels = [];
$chartValues = [];

$last7Days = [];
for ($i = 6; $i >= 0; $i--) {
    $dateStr = date('Y-m-d', strtotime("-$i days"));
    $last7Days[$dateStr] = 0;
}

if ($salesRes) {
    while ($row = $salesRes->fetch_assoc()) {
        $last7Days[$row['o_date']] = (float)$row['daily_total'];
    }
}

foreach ($last7Days as $date => $total) {
    $chartLabels[] = date('D, j M', strtotime($date));
    $chartValues[] = $total;
}

// ---- Fetch category sales for Doughnut Chart ----
$categoryLabels = [];
$categoryValues = [];
$catQuery = "
    SELECT c.category_name, SUM(oi.line_total) AS cat_total 
    FROM order_items oi 
    JOIN foods f ON oi.food_id = f.food_id 
    JOIN food_categories c ON f.category_id = c.category_id 
    JOIN orders o ON oi.order_id = o.order_id 
    WHERE o.order_status != 'cancelled' 
    GROUP BY c.category_id 
    ORDER BY cat_total DESC 
    LIMIT 5
";
$catRes = $conn->query($catQuery);
if ($catRes) {
    while ($row = $catRes->fetch_assoc()) {
        $categoryLabels[] = $row['category_name'];
        $categoryValues[] = (float)$row['cat_total'];
    }
}

// ---- Fetch out-of-stock food items ----
$outOfStockItems = [];
$stockRes = $conn->query("SELECT food_name, price FROM foods WHERE is_available = 0 LIMIT 5");
if ($stockRes) {
    while ($row = $stockRes->fetch_assoc()) {
        $outOfStockItems[] = $row;
    }
}

$conn->close();

require_once 'includes/admin_header.php';
?>

<div class="admin-topbar">
    <h1>Dashboard</h1>
    <p>Welcome back, <?php echo htmlspecialchars($_SESSION['admin_username']); ?> 👋</p>
</div>

<div class="admin-stats-grid">
    <div class="admin-stat-card">
        <i class="fa-solid fa-receipt"></i>
        <div><span><?php echo $totalOrders; ?></span><p>Total Orders</p></div>
    </div>
    <div class="admin-stat-card">
        <i class="fa-solid fa-users"></i>
        <div><span><?php echo $totalUsers; ?></span><p>Registered Users</p></div>
    </div>
    <div class="admin-stat-card">
        <i class="fa-solid fa-burger"></i>
        <div><span><?php echo $totalFoods; ?></span><p>Menu Items</p></div>
    </div>
    <div class="admin-stat-card">
        <i class="fa-solid fa-indian-rupee-sign"></i>
        <div><span>₹<?php echo number_format($totalRevenue, 0); ?></span><p>Total Revenue</p></div>
    </div>
    <div class="admin-stat-card highlight">
        <i class="fa-solid fa-clock"></i>
        <div><span><?php echo $pendingOrders; ?></span><p>Orders Preparing</p></div>
    </div>
</div>

<div class="admin-card">
    <h3>Recent Orders</h3>
    <table class="admin-table">
        <thead>
            <tr><th>Order ID</th><th>Customer</th><th>Total</th><th>Status</th><th>Date</th></tr>
        </thead>
        <tbody>
            <?php foreach ($recentOrders as $order): ?>
                <tr>
                    <td>#TB<?php echo str_pad($order['order_id'], 5, '0', STR_PAD_LEFT); ?></td>
                    <td><?php echo htmlspecialchars($order['full_name']); ?></td>
                    <td>₹<?php echo number_format($order['grand_total'], 0); ?></td>
                    <td><span class="order-status <?php echo $order['order_status']; ?>"><?php echo ucfirst($order['order_status']); ?></span></td>
                    <td><?php echo date('j M Y', strtotime($order['order_date'])); ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if (count($recentOrders) === 0): ?>
                <tr><td colspan="5" style="text-align:center; color: var(--color-muted);">No orders yet.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Out of Stock Alerts Widget -->
<?php if (count($outOfStockItems) > 0): ?>
    <div class="admin-card" style="margin-top: 30px; border-left: 4px solid var(--color-danger); padding: 20px;">
        <h3 style="color: var(--color-danger); margin: 0 0 10px 0; display:flex; align-items:center; gap:8px;">
            <i class="fa-solid fa-triangle-exclamation"></i> Out of Stock Alerts
        </h3>
        <ul style="margin: 0; padding-left: 20px; color: var(--color-text-muted);">
            <?php foreach ($outOfStockItems as $item): ?>
                <li style="margin-bottom: 8px;">
                    <strong style="color: var(--color-text);"><?php echo htmlspecialchars($item['food_name']); ?></strong> is currently marked as sold out.
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<!-- Charts Grid -->
<div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 30px; margin-top: 30px;" class="admin-charts-grid">
    
    <!-- Weekly Sales Chart -->
    <div class="admin-card" style="padding: 25px; margin: 0;">
        <h3>Weekly Sales Analysis</h3>
        <div style="width: 100%; height: 320px; margin-top: 15px; position: relative;">
            <canvas id="salesChart"></canvas>
        </div>
    </div>
    
    <!-- Category Sales Chart -->
    <div class="admin-card" style="padding: 25px; margin: 0;">
        <h3>Category Wise Sales</h3>
        <div style="width: 100%; height: 320px; margin-top: 15px; position: relative;">
            <?php if (count($categoryValues) > 0): ?>
                <canvas id="categoryChart"></canvas>
            <?php else: ?>
                <div style="display:flex; align-items:center; justify-content:center; height:100%; color: var(--color-muted); font-size: 0.9rem;">
                    No category sales data available.
                </div>
            <?php endif; ?>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    // ---- Sales Line Chart ----
    const ctxSales = document.getElementById('salesChart').getContext('2d');
    const gradient = ctxSales.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(255, 107, 53, 0.4)');
    gradient.addColorStop(1, 'rgba(255, 107, 53, 0.0)');

    new Chart(ctxSales, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($chartLabels); ?>,
            datasets: [{
                label: 'Sales (₹)',
                data: <?php echo json_encode($chartValues); ?>,
                borderColor: '#FF6B35',
                borderWidth: 3,
                pointBackgroundColor: '#FFD166',
                pointBorderColor: '#FF6B35',
                pointBorderWidth: 2,
                pointRadius: 6,
                pointHoverRadius: 8,
                backgroundColor: gradient,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1A1A2E',
                    titleColor: '#FFD166',
                    bodyColor: '#F0F0F0',
                    borderColor: 'rgba(255,255,255,0.08)',
                    borderWidth: 1,
                    displayColors: false,
                    padding: 12,
                    callbacks: {
                        label: function(context) {
                            return ' Revenue: ₹' + context.parsed.y.toLocaleString();
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { color: 'rgba(255, 255, 255, 0.05)' },
                    ticks: { color: '#8A8A9A', font: { family: "'Inter', sans-serif" } }
                },
                y: {
                    grid: { color: 'rgba(255, 255, 255, 0.05)' },
                    ticks: {
                        color: '#8A8A9A',
                        font: { family: "'Inter', sans-serif" },
                        callback: function(value) { return '₹' + value; }
                    }
                }
            }
        }
    });

    // ---- Category Sales Doughnut Chart ----
    const categoryCanvas = document.getElementById('categoryChart');
    if (categoryCanvas) {
        const ctxCat = categoryCanvas.getContext('2d');
        new Chart(ctxCat, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode($categoryLabels); ?>,
                datasets: [{
                    data: <?php echo json_encode($categoryValues); ?>,
                    backgroundColor: [
                        '#FF6B35', // Orange
                        '#FFD166', // Gold
                        '#00D68F', // Success Green
                        '#22a6ff', // Sky Blue
                        '#e040fb'  // Magenta
                    ],
                    borderWidth: 2,
                    borderColor: '#12121A' // matches secondary background color
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#8A8A9A',
                            font: { family: "'Inter', sans-serif", size: 11 },
                            padding: 15
                        }
                    },
                    tooltip: {
                        backgroundColor: '#1A1A2E',
                        titleColor: '#FFD166',
                        bodyColor: '#F0F0F0',
                        borderColor: 'rgba(255,255,255,0.08)',
                        borderWidth: 1,
                        padding: 12,
                        callbacks: {
                            label: function(context) {
                                return ' ' + context.label + ': ₹' + context.raw.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>

<?php require_once 'includes/admin_footer.php'; ?>
