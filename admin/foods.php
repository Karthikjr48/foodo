<?php
require_once 'includes/admin_check.php';
require_once '../includes/db_connect.php';

$pageTitle = 'Manage Foods';
$activePage = 'foods';

// ---- Handle delete action ----
if (isset($_GET['delete'])) {
    $deleteId = (int) $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM foods WHERE food_id = ?");
    $stmt->bind_param("i", $deleteId);
    $stmt->execute();
    $stmt->close();
    header('Location: foods.php?deleted=1');
    exit();
}

$foods = [];
$res = $conn->query("
    SELECT f.*, c.category_name FROM foods f
    JOIN food_categories c ON f.category_id = c.category_id
    ORDER BY f.food_id DESC
");
while ($row = $res->fetch_assoc()) { $foods[] = $row; }
$conn->close();
require_once 'includes/admin_header.php';
require_once '../includes/helpers.php';
?>

<div class="admin-topbar">
    <h1>Manage Foods</h1>
    <a href="food-form.php" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Add New Food</a>
</div>

<?php if (isset($_GET['deleted'])): ?>
    <div class="auth-note" style="max-width: 100%;"><i class="fa-solid fa-circle-check"></i> Food item deleted successfully.</div>
<?php endif; ?>
<?php if (isset($_GET['saved'])): ?>
    <div class="auth-note" style="max-width: 100%;"><i class="fa-solid fa-circle-check"></i> Food item saved successfully.</div>
<?php endif; ?>

<div class="admin-card">
    <table class="admin-table">
        <thead>
            <tr><th>Image</th><th>ID</th><th>Name</th><th>Category</th><th>Price</th><th>Rating</th><th>Status</th><th>Actions</th></tr>
        </thead>
        <tbody>
            <?php foreach ($foods as $food): ?>
                <tr>
                    <td><img src="<?php echo foodImageUrl($food['image'], $food['food_name'], '../'); ?>" alt="" style="width:44px;height:44px;border-radius:8px;object-fit:cover;"></td>
                    <td>#<?php echo $food['food_id']; ?></td>
                    <td><?php echo htmlspecialchars($food['food_name']); ?><?php if ($food['is_bestseller']) echo ' <span class="badge-inline">Bestseller</span>'; ?></td>
                    <td><?php echo htmlspecialchars($food['category_name']); ?></td>
                    <td>₹<?php echo number_format($food['price'], 0); ?></td>
                    <td><i class="fa-solid fa-star" style="color:var(--color-gold)"></i> <?php echo number_format($food['rating'], 1); ?></td>
                    <td><span class="order-status <?php echo $food['is_available'] ? 'delivered' : 'cancelled'; ?>"><?php echo $food['is_available'] ? 'Available' : 'Sold Out'; ?></span></td>
                    <td class="admin-actions">
                        <a href="food-form.php?id=<?php echo $food['food_id']; ?>" class="btn btn-outline btn-sm">Edit</a>
                        <a href="foods.php?delete=<?php echo $food['food_id']; ?>" class="btn btn-outline btn-sm danger" onclick="return confirm('Delete this food item?');">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (count($foods) === 0): ?>
                <tr><td colspan="8" style="text-align:center; color: var(--color-muted);">No foods added yet.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once 'includes/admin_footer.php'; ?>
