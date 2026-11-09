<?php
require_once 'includes/admin_check.php';
require_once '../includes/db_connect.php';

$pageTitle = 'Manage Categories';
$activePage = 'categories';

// ---- Handle add ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['category_name'])) {
    $name = trim($_POST['category_name']);
    $slug = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $name), '-'));

    $stmt = $conn->prepare("INSERT INTO food_categories (category_name, slug) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $slug);
    $stmt->execute();
    $stmt->close();
    header('Location: categories.php?added=1');
    exit();
}

// ---- Handle delete ----
if (isset($_GET['delete'])) {
    $deleteId = (int) $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM food_categories WHERE category_id = ?");
    $stmt->bind_param("i", $deleteId);
    $stmt->execute();
    $stmt->close();
    header('Location: categories.php?deleted=1');
    exit();
}

$categories = [];
$res = $conn->query("
    SELECT c.*, COUNT(f.food_id) AS food_count
    FROM food_categories c LEFT JOIN foods f ON c.category_id = f.category_id
    GROUP BY c.category_id ORDER BY c.category_id ASC
");
while ($row = $res->fetch_assoc()) { $categories[] = $row; }
$conn->close();

require_once 'includes/admin_header.php';
?>

<div class="admin-topbar">
    <h1>Manage Categories</h1>
</div>

<?php if (isset($_GET['added'])): ?>
    <div class="auth-note" style="max-width: 100%;"><i class="fa-solid fa-circle-check"></i> Category added successfully.</div>
<?php endif; ?>
<?php if (isset($_GET['deleted'])): ?>
    <div class="auth-note" style="max-width: 100%;"><i class="fa-solid fa-circle-check"></i> Category deleted successfully.</div>
<?php endif; ?>
<?php if (isset($_GET['error'])): ?>
    <div class="auth-note error" style="max-width: 100%;"><i class="fa-solid fa-circle-exclamation"></i> Cannot delete — foods are still assigned to this category. Reassign or delete them first.</div>
<?php endif; ?>

<div class="admin-card">
    <h3>Add New Category</h3>
    <form action="categories.php" method="post" class="inline-form">
        <input type="text" name="category_name" placeholder="e.g. Wraps & Rolls" required>
        <button type="submit" class="btn btn-primary">Add Category</button>
    </form>
</div>

<div class="admin-card">
    <table class="admin-table">
        <thead>
            <tr><th>ID</th><th>Category Name</th><th>Slug</th><th>Foods in Category</th><th>Actions</th></tr>
        </thead>
        <tbody>
            <?php foreach ($categories as $cat): ?>
                <tr>
                    <td>#<?php echo $cat['category_id']; ?></td>
                    <td><?php echo htmlspecialchars($cat['category_name']); ?></td>
                    <td><?php echo htmlspecialchars($cat['slug']); ?></td>
                    <td><?php echo $cat['food_count']; ?></td>
                    <td class="admin-actions">
                        <?php if ($cat['food_count'] == 0): ?>
                            <a href="categories.php?delete=<?php echo $cat['category_id']; ?>" class="btn btn-outline btn-sm danger" onclick="return confirm('Delete this category?');">Delete</a>
                        <?php else: ?>
                            <span style="color: var(--color-muted); font-size: 0.82rem;">In use</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once 'includes/admin_footer.php'; ?>
