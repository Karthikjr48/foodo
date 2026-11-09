<?php
/**
 * helpers.php
 * -------------------------------------------------------
 * Shared helper functions used across multiple pages.
 * -------------------------------------------------------
 */

// Absolute server path to the project root (food-ordering folder)
define('BASE_PATH', dirname(__DIR__));

/**
 * Returns the URL to a food's real uploaded image if it exists,
 * otherwise falls back to a colored placeholder with the food's name.
 *
 * @param string|null $imageFilename  Value from foods.image (e.g. "abc123.jpg")
 * @param string $foodName            Used for the placeholder text and alt text
 * @param string $root                '' if page is in root, '../' if inside /pages/ or /admin/
 */
function foodImageUrl($imageFilename, $foodName, $root = '') {
    // Unsplash high quality food image URL mappings for database foods
    $unsplashMap = [
        'cheese-burst-pizza.jpg' => 'https://images.unsplash.com/photo-1513104890138-7c749659a591?w=400&h=280&fit=crop',
        'margherita-pizza.jpg' => 'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?w=400&h=280&fit=crop',
        'classic-burger.jpg' => 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=400&h=280&fit=crop',
        'fried-chicken.jpg' => 'https://images.unsplash.com/photo-1569058242253-92a9c755a0ec?w=400&h=280&fit=crop',
        'chicken-biryani.jpg' => 'https://images.unsplash.com/photo-1563379091339-03b21ab4a4f8?w=400&h=280&fit=crop',
        'masala-dosa.jpg' => 'https://images.unsplash.com/photo-1668236543090-82eba5ee5976?w=400&h=280&fit=crop',
        'paneer-butter-masala.jpg' => 'https://images.unsplash.com/photo-1631452180519-c014fe946bc7?w=400&h=280&fit=crop',
        'fried-rice.jpg' => 'https://images.unsplash.com/photo-1603133872878-684f208fb84b?w=400&h=280&fit=crop',
        'veg-sandwich.jpg' => 'https://images.unsplash.com/photo-1528735602780-2552fd46c7af?w=400&h=280&fit=crop',
        'white-sauce-pasta.jpg' => 'https://images.unsplash.com/photo-1645112411341-6c4fd023714a?w=400&h=280&fit=crop',
        'hakka-noodles.jpg' => 'https://images.unsplash.com/photo-1569718212165-3a8278d5f624?w=400&h=280&fit=crop',
        'lava-cake.jpg' => 'https://images.unsplash.com/photo-1606313564200-e75d5e30476c?w=400&h=280&fit=crop',
        'ice-cream.jpg' => 'https://images.unsplash.com/photo-1572490122747-3968b75cc699?w=400&h=280&fit=crop',
        'watermelon-juice.jpg' => 'https://images.unsplash.com/photo-1589733901241-5e53429e145e?w=400&h=280&fit=crop',
        'cold-coffee.jpg' => 'https://images.unsplash.com/photo-1517701604599-bb29b565090c?w=400&h=280&fit=crop',
        'masala-chai.jpg' => 'https://images.unsplash.com/photo-1576092768241-dec231879fc3?w=400&h=280&fit=crop',
        'soft-drink.jpg' => 'https://images.unsplash.com/photo-1622483767028-3f66f32aef97?w=400&h=280&fit=crop',
        'spring-rolls.jpg' => 'https://images.unsplash.com/photo-1544025162-d76694265947?w=400&h=280&fit=crop',
        'buddha-bowl.jpg' => 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=400&h=280&fit=crop'
    ];

    if (!empty($imageFilename) && isset($unsplashMap[$imageFilename])) {
        return $unsplashMap[$imageFilename];
    }

    if (!empty($imageFilename) && file_exists(BASE_PATH . '/images/foods/' . $imageFilename)) {
        return $root . 'images/foods/' . $imageFilename;
    }
    return "https://placehold.co/400x280/FF4B3E/FFF8F0?text=" . urlencode($foodName);
}
?>
