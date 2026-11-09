<?php
/**
 * wishlist.php
 * -------------------------------------------------------
 * My Wishlist / Liked Dishes Page
 * Renders liked food cards dynamically based on LocalStorage.
 * -------------------------------------------------------
 */
$root = '../';
$pageTitle = 'My Wishlist | Foodo';
require_once '../includes/db_connect.php';
require_once '../includes/header.php';
require_once '../includes/helpers.php';

// Fetch all available foods from the database
$foods = [];
$foodQuery = "
    SELECT f.food_id, f.food_name, f.description, f.price, f.image, f.rating,
           f.is_available, f.is_bestseller, c.category_name, c.slug
    FROM foods f
    JOIN food_categories c ON f.category_id = c.category_id
    ORDER BY f.food_id ASC
";
$foodResult = $conn->query($foodQuery);
if ($foodResult) {
    while ($row = $foodResult->fetch_assoc()) {
        $row['image_url'] = foodImageUrl($row['image'], $row['food_name'], '../');
        $foods[] = $row;
    }
}
$conn->close();
?>

    <!-- ==================== PAGE HEADER ==================== -->
    <section class="page-header">
        <div class="container">
            <span class="eyebrow"><i class="fa-solid fa-heart" style="color: #FF4757;"></i> Favorites</span>
            <h1>My Liked Dishes</h1>
            <p>Your favorite cravings saved in one spot. Click ADD to order anytime.</p>
        </div>
    </section>

    <!-- ==================== WISHLIST SECTION ==================== -->
    <section class="section wishlist-section">
        <div class="container">
            
            <!-- Empty Wishlist State -->
            <div id="emptyWishlist" class="empty-cart" style="display: none; max-width: 600px; margin: 0 auto;">
                <i class="fa-regular fa-heart" style="font-size: 3.5rem; color: #FF4757; opacity: 0.8; margin-bottom: 20px;"></i>
                <h3>No Liked Dishes Yet</h3>
                <p>Explore our menu and click the heart icon on any dish you love!</p>
                <a href="menu.php" class="btn btn-primary" style="margin-top: 10px;">Browse Menu <i class="fa-solid fa-arrow-right"></i></a>
            </div>

            <!-- Active Wishlist Grid -->
            <div class="food-grid" id="wishlistGrid" style="display: none;">
                <!-- Cards injected here via JavaScript -->
            </div>

        </div>
    </section>

    <!-- Wishlist Page Renderer Script -->
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        // Parse all database foods from PHP
        const allFoods = <?php echo json_encode($foods); ?>;
        
        const wishlistGrid = document.getElementById('wishlistGrid');
        const emptyWishlist = document.getElementById('emptyWishlist');
        
        function getWishlistIds() {
            try {
                return JSON.parse(localStorage.getItem('foodo_wishlist')) || [];
            } catch(e) {
                return [];
            }
        }
        
        function renderWishlist() {
            const likedIds = getWishlistIds().map(id => id.toString());
            
            // Filter foods matching liked IDs
            const likedFoods = allFoods.filter(food => likedIds.includes(food.food_id.toString()));
            
            if (likedFoods.length === 0) {
                wishlistGrid.style.display = 'none';
                emptyWishlist.style.display = 'block';
            } else {
                emptyWishlist.style.display = 'none';
                wishlistGrid.style.display = 'grid';
                wishlistGrid.innerHTML = '';
                
                likedFoods.forEach(food => {
                    const isBestsellerBadge = food.is_bestseller == 1 ? '<span class="badge">Bestseller</span>' : '';
                    const availabilitySpan = food.is_available == 1 
                        ? '<span class="availability available"><i class="fa-solid fa-circle"></i> Available</span>' 
                        : '<span class="availability out"><i class="fa-solid fa-circle"></i> Sold Out</span>';
                    
                    const cardHtml = `
                        <div class="food-card" data-category="${food.slug}" data-name="${food.food_name.toLowerCase()}">
                            <div class="food-img-wrap">
                                <img src="${food.image_url}" alt="${food.food_name}">
                                <a href="../includes/cart_process.php?action=add&food_id=${food.food_id}&redirect=menu" 
                                   class="quick-add" aria-label="Add to cart">
                                    <i class="fa-solid fa-plus"></i> ADD
                                </a>
                                <button class="like-btn liked" data-id="${food.food_id}" aria-label="Unlike dish">
                                    <i class="fa-solid fa-heart"></i>
                                </button>
                                ${isBestsellerBadge}
                            </div>
                            <div class="food-info">
                                <div class="food-top-row">
                                    <h3>${food.food_name}</h3>
                                    <span class="rating"><i class="fa-solid fa-star"></i> ${parseFloat(food.rating).toFixed(1)}</span>
                                </div>
                                <p class="cat-tag">${food.category_name}</p>
                                <p class="food-desc">${food.description}</p>
                                <div class="food-bottom-row">
                                    <span class="price">₹${parseInt(food.price, 10)}</span>
                                    ${availabilitySpan}
                                </div>
                            </div>
                        </div>
                    `;
                    wishlistGrid.insertAdjacentHTML('beforeend', cardHtml);
                });
                
                // Re-bind Like Buttons events on the newly generated cards
                bindWishlistPageButtons();
            }
        }
        
        function bindWishlistPageButtons() {
            wishlistGrid.querySelectorAll('.like-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const id = btn.dataset.id.toString();
                    let list = getWishlistIds().map(x => x.toString());
                    
                    // Toggle out (since we are on the wishlist page, liking a liked item unlikes/removes it)
                    list = list.filter(item => item !== id);
                    
                    // Save to local storage
                    localStorage.setItem('foodo_wishlist', JSON.stringify(list));
                    
                    // Update global header badge
                    const headerBadge = document.querySelector('.wishlist-count');
                    if (headerBadge) {
                        headerBadge.textContent = list.length;
                        headerBadge.style.display = list.length > 0 ? 'flex' : 'none';
                    }
                    
                    // Re-render grid
                    renderWishlist();
                });
            });
            
            // Re-bind AJAX Add-To-Cart trigger to new cards
            wishlistGrid.querySelectorAll('.quick-add').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const profileChip = document.querySelector('.profile-chip');
                    if (!profileChip) return; // Allow redirect to login for guests
                    
                    e.preventDefault();
                    const url = btn.getAttribute('href');
                    const ajaxUrl = url + '&ajax=1';
                    
                    fetch(ajaxUrl)
                        .then(res => res.json())
                        .then(data => {
                            if (data.status === 'success') {
                                // Trigger fly animation
                                animateFlyToCart(btn, data.cart_count);
                            }
                        })
                        .catch(err => {
                            console.error('AJAX cart error:', err);
                            window.location.href = url;
                        });
                });
            });
            
            // Re-bind 3D tilt effects
            wishlistGrid.querySelectorAll('.food-card').forEach(card => {
                card.addEventListener('mousemove', (e) => {
                    const rect = card.getBoundingClientRect();
                    const x = e.clientX - rect.left;
                    const y = e.clientY - rect.top;
                    const centerX = rect.width / 2;
                    const centerY = rect.height / 2;
                    const rotateX = (y - centerY) / 20;
                    const rotateY = (centerX - x) / 20;
                    card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-5px)`;
                });
                card.addEventListener('mouseleave', () => {
                    card.style.transform = 'perspective(1000px) rotateX(0) rotateY(0) translateY(0)';
                    card.style.transition = 'transform 0.4s ease';
                });
            });
        }
        
        // Initial execution
        renderWishlist();
    });
    </script>

<?php require_once '../includes/footer.php'; ?>
