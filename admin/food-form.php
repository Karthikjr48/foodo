<?php
require_once 'includes/admin_check.php';
require_once '../includes/db_connect.php';

$pageTitle = 'Add / Edit Food';
$activePage = 'foods';

$editId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$isEdit = $editId > 0;

$food = [
    'food_name' => '', 'description' => '', 'price' => '', 'category_id' => '',
    'rating' => '4.5', 'is_available' => 1, 'is_bestseller' => 0
];

if ($isEdit) {
    $stmt = $conn->prepare("SELECT * FROM foods WHERE food_id = ?");
    $stmt->bind_param("i", $editId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $food = $result->fetch_assoc();
    }
    $stmt->close();
}

$categories = [];
$catRes = $conn->query("SELECT category_id, category_name FROM food_categories ORDER BY category_name ASC");
while ($row = $catRes->fetch_assoc()) { $categories[] = $row; }

require_once 'includes/admin_header.php';
require_once '../includes/helpers.php';
?>

<div class="admin-topbar">
    <h1><?php echo $isEdit ? 'Edit Food' : 'Add New Food'; ?></h1>
    <a href="foods.php" class="btn btn-outline"><i class="fa-solid fa-arrow-left"></i> Back to Foods</a>
</div>

<div class="admin-card">
    <form action="food-form-process.php" method="post" enctype="multipart/form-data">
        <?php if ($isEdit): ?><input type="hidden" name="food_id" value="<?php echo $food['food_id']; ?>"><?php endif; ?>

        <div class="form-group">
            <label>Food Image</label>
            <div class="image-upload-row">
                <img src="<?php echo foodImageUrl($food['image'] ?? null, $food['food_name'] ?: 'Food', '../'); ?>" alt="Current image" class="image-preview">
                <div>
                    <input type="file" name="food_image" accept=".jpg,.jpeg,.png,.webp">
                    <p class="image-upload-hint">JPG, PNG or WEBP. Max 2MB. <?php echo $isEdit ? 'Leave empty to keep the current image.' : ''; ?></p>
                </div>
            </div>
        </div>


        <div class="form-row">
            <div class="form-group">
                <label for="food_name">Food Name</label>
                <input type="text" id="food_name" name="food_name" value="<?php echo htmlspecialchars($food['food_name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="category_id">Category</label>
                <select id="category_id" name="category_id" required>
                    <option value="">Select category</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['category_id']; ?>" <?php echo ($food['category_id'] == $cat['category_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['category_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="3" required><?php echo htmlspecialchars($food['description']); ?></textarea>
        </div>

        <div class="form-row three-col">
            <div class="form-group">
                <label for="price">Price (₹)</label>
                <input type="number" id="price" name="price" step="0.01" value="<?php echo htmlspecialchars($food['price']); ?>" required>
            </div>
            <div class="form-group">
                <label for="rating">Rating (0-5)</label>
                <input type="number" id="rating" name="rating" step="0.1" min="0" max="5" value="<?php echo htmlspecialchars($food['rating']); ?>" required>
            </div>
            <div class="form-group">
                <label>&nbsp;</label>
                <label class="checkbox-label" style="padding: 12px 0;">
                    <input type="checkbox" name="is_available" <?php echo $food['is_available'] ? 'checked' : ''; ?>>
                    <span>Available</span>
                </label>
            </div>
        </div>

        <div class="form-group">
            <label class="checkbox-label">
                <input type="checkbox" name="is_bestseller" <?php echo $food['is_bestseller'] ? 'checked' : ''; ?>>
                <span>Mark as Bestseller</span>
            </label>
        </div>

        <button type="submit" class="btn btn-primary"><?php echo $isEdit ? 'Update Food' : 'Add Food'; ?></button>
    </form>
</div>

<?php require_once 'includes/admin_footer.php'; ?>
