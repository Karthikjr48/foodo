<?php
$root = '../';
$pageTitle = 'Menu | Foodo';
require_once '../includes/db_connect.php';
require_once '../includes/header.php';

// ---- Fetch all categories (for the filter bar) ----
$categories = [];
$catResult = $conn->query("SELECT category_id, category_name, slug FROM food_categories ORDER BY category_id ASC");
while ($row = $catResult->fetch_assoc()) {
    $categories[] = $row;
}

// ---- Fetch all foods, joined with their category name/slug ----
$foods = [];
$foodQuery = "
    SELECT f.food_id, f.food_name, f.description, f.price, f.image, f.rating,
           f.is_available, f.is_bestseller, c.category_name, c.slug
    FROM foods f
    JOIN food_categories c ON f.category_id = c.category_id
    ORDER BY f.food_id ASC
";
$foodResult = $conn->query($foodQuery);
while ($row = $foodResult->fetch_assoc()) {
    $foods[] = $row;
}

require_once '../includes/helpers.php';
$conn->close();

?>

    <!-- ==================== PAGE HEADER ==================== -->
    <section class="page-header">
        <div class="container">
            <span class="eyebrow">Full Menu</span>
            <h1>Everything We Serve</h1>
            <p>Browse all <?php echo count($categories); ?> categories or search for exactly what you're craving.</p>

            <form class="search-box" action="menu.php" method="get">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" name="search" id="menuSearch" placeholder="Search dishes...">
                <button type="submit" class="btn btn-primary">Search</button>
            </form>
        </div>
    </section>

    <!-- ==================== CATEGORY FILTER BAR (built from the database) ==================== -->
    <section class="section filter-section" id="categories">
        <div class="container">
            <div class="filter-bar" id="filterBar">
                <button class="filter-chip active" data-filter="all">All</button>
                <?php foreach ($categories as $cat): ?>
                    <button class="filter-chip" data-filter="<?php echo htmlspecialchars($cat['slug']); ?>">
                        <?php echo htmlspecialchars($cat['category_name']); ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ==================== MENU GRID (built from the database) ==================== -->
    <section class="section menu-section">
        <div class="container">

            <?php if (count($foods) === 0): ?>
                <p class="no-results">No dishes found in the database yet.</p>
            <?php else: ?>
                <div class="food-grid" id="foodGrid">
                    <?php foreach ($foods as $food): ?>
                        <div class="food-card"
                             data-category="<?php echo htmlspecialchars($food['slug']); ?>"
                             data-name="<?php echo htmlspecialchars(strtolower($food['food_name'])); ?>">
                            <div class="food-img-wrap">
                                <img src="<?php echo foodImageUrl($food['image'], $food['food_name'], '../'); ?>" alt="<?php echo htmlspecialchars($food['food_name']); ?>">
                                <a href="../includes/cart_process.php?action=add&food_id=<?php echo $food['food_id']; ?>&redirect=menu"
                                   class="quick-add" aria-label="Add to cart">
                                    <i class="fa-solid fa-plus"></i> ADD
                                </a>
                                <button class="like-btn" data-id="<?php echo $food['food_id']; ?>" aria-label="Like dish">
                                    <i class="fa-regular fa-heart"></i>
                                </button>
                                <?php if ($food['is_bestseller']): ?>
                                    <span class="badge">Bestseller</span>
                                <?php endif; ?>
                            </div>
                            <div class="food-info">
                                <div class="food-top-row">
                                    <h3><?php echo htmlspecialchars($food['food_name']); ?></h3>
                                    <span class="rating"><i class="fa-solid fa-star"></i> <?php echo number_format($food['rating'], 1); ?></span>
                                </div>
                                <p class="cat-tag"><?php echo htmlspecialchars($food['category_name']); ?></p>
                                <p class="food-desc"><?php echo htmlspecialchars($food['description']); ?></p>
                                <div class="food-bottom-row">
                                    <span class="price">₹<?php echo number_format($food['price'], 0); ?></span>
                                    <?php if ($food['is_available']): ?>
                                        <span class="availability available"><i class="fa-solid fa-circle"></i> Available</span>
                                    <?php else: ?>
                                        <span class="availability out"><i class="fa-solid fa-circle"></i> Sold Out</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Shown by JS when a filter/search returns nothing -->
            <p class="no-results" id="noResults" style="display:none;">No dishes match your search. Try a different keyword or category.</p>
        </div>
    </section>

<?php require_once '../includes/footer.php'; ?>
