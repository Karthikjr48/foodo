<?php
require_once 'includes/admin_check.php';
require_once '../includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: foods.php');
    exit();
}

$foodId = isset($_POST['food_id']) ? (int) $_POST['food_id'] : 0;
$isEdit = $foodId > 0;

$foodName = trim($_POST['food_name']);
$description = trim($_POST['description']);
$price = (float) $_POST['price'];
$categoryId = (int) $_POST['category_id'];
$rating = (float) $_POST['rating'];
$isAvailable = isset($_POST['is_available']) ? 1 : 0;
$isBestseller = isset($_POST['is_bestseller']) ? 1 : 0;

// ---- Handle image upload (optional — only if a new file was chosen) ----
$imageFilename = null; // null = don't touch the image column
$uploadDir = '../images/foods/';
$allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
$maxSize = 2 * 1024 * 1024; // 2MB

if (isset($_FILES['food_image']) && $_FILES['food_image']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['food_image'];

    if (in_array($file['type'], $allowedTypes) && $file['size'] <= $maxSize) {
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $imageFilename = 'food_' . time() . '_' . uniqid() . '.' . strtolower($extension);

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        move_uploaded_file($file['tmp_name'], $uploadDir . $imageFilename);
    }
    // If validation fails, we simply skip the image (no crash) — the
    // existing/placeholder image is kept, which is a safe default for a college project.
}

if ($isEdit) {
    if ($imageFilename !== null) {
        // A new image was uploaded — update it along with everything else
        $stmt = $conn->prepare("
            UPDATE foods SET food_name=?, description=?, price=?, category_id=?, rating=?, is_available=?, is_bestseller=?, image=?
            WHERE food_id=?
        ");
        $stmt->bind_param("ssdidiisi", $foodName, $description, $price, $categoryId, $rating, $isAvailable, $isBestseller, $imageFilename, $foodId);
    } else {
        // No new image — keep the existing one untouched
        $stmt = $conn->prepare("
            UPDATE foods SET food_name=?, description=?, price=?, category_id=?, rating=?, is_available=?, is_bestseller=?
            WHERE food_id=?
        ");
        $stmt->bind_param("ssdidiii", $foodName, $description, $price, $categoryId, $rating, $isAvailable, $isBestseller, $foodId);
    }
} else {
    // ---- Insert new food ----
    $stmt = $conn->prepare("
        INSERT INTO foods (food_name, description, price, category_id, rating, is_available, is_bestseller, image)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("ssdidiis", $foodName, $description, $price, $categoryId, $rating, $isAvailable, $isBestseller, $imageFilename);
}

$stmt->execute();
$stmt->close();
$conn->close();

header('Location: foods.php?saved=1');
exit();
?>
