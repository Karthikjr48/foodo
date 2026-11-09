<?php
$root = '';
$pageTitle = 'Foodo | Order Delicious Food Online';
require_once 'includes/header.php';
?>

    <!-- ==================== HERO BANNER ==================== -->
    <section class="hero">
        <div class="container hero-inner fade-in-up">
            <div class="hero-text">
                <span class="eyebrow">🔥 Hungry? We've got you.</span>
                <h1>Delicious food, <br>delivered to your <span>doorstep</span>.</h1>
                <p>Order from 500+ dishes across 18 cuisines. Fresh ingredients, fast delivery, and unbeatable taste — every single time.</p>

                <!-- Search Box -->
                <form class="search-box" action="pages/menu.php" method="get">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" name="search" placeholder="Search for biryani, pizza, dosa...">
                    <button type="submit" class="btn btn-primary">Search</button>
                </form>

                <div class="hero-stats">
                    <div><strong>500+</strong><span>Menu Items</span></div>
                    <div><strong>25 min</strong><span>Avg Delivery</span></div>
                    <div><strong>4.8 ★</strong><span>Customer Rating</span></div>
                </div>
            </div>

            <div class="hero-image">
                <img src="https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=600&h=600&fit=crop" alt="Signature dish">
            </div>
        </div>
    </section>

    <!-- ==================== CATEGORIES SECTION ==================== -->
    <section class="section categories-section" id="categories">
        <div class="container fade-in-up">
            <div class="section-heading">
                <h2>Explore Categories</h2>
                <p>Pick a craving, we'll handle the rest.</p>
            </div>

            <div class="category-scroll">
                <a href="pages/menu.php?category=pizza" class="category-chip">
                    <span class="category-icon"><i class="fa-solid fa-pizza-slice"></i></span>
                    <span>Pizza</span>
                </a>
                <a href="pages/menu.php?category=burger" class="category-chip">
                    <span class="category-icon"><i class="fa-solid fa-burger"></i></span>
                    <span>Burger</span>
                </a>
                <a href="pages/menu.php?category=fried-chicken" class="category-chip">
                    <span class="category-icon"><i class="fa-solid fa-drumstick-bite"></i></span>
                    <span>Fried Chicken</span>
                </a>
                <a href="pages/menu.php?category=biryani" class="category-chip">
                    <span class="category-icon"><i class="fa-solid fa-bowl-rice"></i></span>
                    <span>Biryani</span>
                </a>
                <a href="pages/menu.php?category=south-indian" class="category-chip">
                    <span class="category-icon"><i class="fa-solid fa-bowl-food"></i></span>
                    <span>South Indian</span>
                </a>
                <a href="pages/menu.php?category=north-indian" class="category-chip">
                    <span class="category-icon"><i class="fa-solid fa-utensils"></i></span>
                    <span>North Indian</span>
                </a>
                <a href="pages/menu.php?category=chinese" class="category-chip">
                    <span class="category-icon"><i class="fa-solid fa-bowl-rice"></i></span>
                    <span>Chinese</span>
                </a>
                <a href="pages/menu.php?category=desserts" class="category-chip">
                    <span class="category-icon"><i class="fa-solid fa-cake-candles"></i></span>
                    <span>Desserts</span>
                </a>
                <a href="pages/menu.php?category=soft-drinks" class="category-chip">
                    <span class="category-icon"><i class="fa-solid fa-glass-water"></i></span>
                    <span>Beverages</span>
                </a>
            </div>
        </div>
    </section>

    <!-- ==================== POPULAR FOODS ==================== -->
    <section class="section">
        <div class="container fade-in-up">
            <div class="section-heading">
                <h2>Popular Right Now</h2>
                <p>The dishes everyone's ordering this week.</p>
            </div>

            <div class="food-grid">

                <!-- Food Card 1 -->
                <div class="food-card" data-name="margherita pizza" data-category="pizza">
                    <div class="food-img-wrap">
                        <img src="https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?w=400&h=280&fit=crop" alt="Margherita Pizza">
                        <a href="includes/cart_process.php?action=add&food_id=2&redirect=menu" class="quick-add" aria-label="Add to cart">
                            <i class="fa-solid fa-plus"></i> ADD
                        </a>
                        <button class="like-btn" data-id="2" aria-label="Like dish">
                            <i class="fa-regular fa-heart"></i>
                        </button>
                        <span class="badge">Bestseller</span>
                    </div>
                    <div class="food-info">
                        <div class="food-top-row">
                            <h3>Margherita Pizza</h3>
                            <span class="rating"><i class="fa-solid fa-star"></i> 4.7</span>
                        </div>
                        <p class="food-desc">Classic cheese & basil pizza on a thin, crispy crust.</p>
                        <div class="food-bottom-row">
                            <span class="price">₹249</span>
                            <span class="availability available"><i class="fa-solid fa-circle"></i> Available</span>
                        </div>
                    </div>
                </div>

                <!-- Food Card 2 -->
                <div class="food-card" data-name="chicken biryani" data-category="biryani">
                    <div class="food-img-wrap">
                        <img src="https://images.unsplash.com/photo-1563379091339-03b21ab4a4f8?w=400&h=280&fit=crop" alt="Chicken Biryani">
                        <a href="includes/cart_process.php?action=add&food_id=5&redirect=menu" class="quick-add" aria-label="Add to cart">
                            <i class="fa-solid fa-plus"></i> ADD
                        </a>
                        <button class="like-btn" data-id="5" aria-label="Like dish">
                            <i class="fa-regular fa-heart"></i>
                        </button>
                    </div>
                    <div class="food-info">
                        <div class="food-top-row">
                            <h3>Chicken Biryani</h3>
                            <span class="rating"><i class="fa-solid fa-star"></i> 4.9</span>
                        </div>
                        <p class="food-desc">Fragrant basmati rice layered with spiced chicken.</p>
                        <div class="food-bottom-row">
                            <span class="price">₹289</span>
                            <span class="availability available"><i class="fa-solid fa-circle"></i> Available</span>
                        </div>
                    </div>
                </div>

                <!-- Food Card 3 -->
                <div class="food-card" data-name="veg sandwich" data-category="sandwich">
                    <div class="food-img-wrap">
                        <img src="https://images.unsplash.com/photo-1528735602780-2552fd46c7af?w=400&h=280&fit=crop" alt="Veg Sandwich">
                        <a href="includes/cart_process.php?action=add&food_id=8&redirect=menu" class="quick-add" aria-label="Add to cart">
                            <i class="fa-solid fa-plus"></i> ADD
                        </a>
                        <button class="like-btn" data-id="8" aria-label="Like dish">
                            <i class="fa-regular fa-heart"></i>
                        </button>
                    </div>
                    <div class="food-info">
                        <div class="food-top-row">
                            <h3>Grilled Veg Sandwich</h3>
                            <span class="rating"><i class="fa-solid fa-star"></i> 4.4</span>
                        </div>
                        <p class="food-desc">Grilled bread stuffed with fresh veggies & cheese.</p>
                        <div class="food-bottom-row">
                            <span class="price">₹129</span>
                            <span class="availability available"><i class="fa-solid fa-circle"></i> Available</span>
                        </div>
                    </div>
                </div>

                <!-- Food Card 4 -->
                <div class="food-card" data-name="hakka noodles" data-category="noodles">
                    <div class="food-img-wrap">
                        <img src="https://images.unsplash.com/photo-1569718212165-3a8278d5f624?w=400&h=280&fit=crop" alt="Hakka Noodles">
                        <a href="includes/cart_process.php?action=add&food_id=11&redirect=menu" class="quick-add" aria-label="Add to cart">
                            <i class="fa-solid fa-plus"></i> ADD
                        </a>
                        <button class="like-btn" data-id="11" aria-label="Like dish">
                            <i class="fa-regular fa-heart"></i>
                        </button>
                    </div>
                    <div class="food-info">
                        <div class="food-top-row">
                            <h3>Hakka Noodles</h3>
                            <span class="rating"><i class="fa-solid fa-star"></i> 4.5</span>
                        </div>
                        <p class="food-desc">Wok-tossed noodles with crunchy vegetables.</p>
                        <div class="food-bottom-row">
                            <span class="price">₹179</span>
                            <span class="availability out">
                                <i class="fa-solid fa-circle"></i> Sold Out</span>
                        </div>
                    </div>
                </div>

            </div>

            <div class="section-cta">
                <a href="pages/menu.php" class="btn btn-outline">View Full Menu <i class="fa-solid fa-arrow-right"></i></a>
            </div>
        </div>
    </section>

    <!-- ==================== TODAY'S SPECIAL ==================== -->
    <section class="section special-section">
        <div class="container special-inner fade-in-up">
            <div class="special-image">
                <img src="https://images.unsplash.com/photo-1563379091339-03b21ab4a4f8?w=520&h=400&fit=crop" alt="Today's Special Dish">
            </div>
            <div class="special-text">
                <span class="eyebrow">Today's Special</span>
                <h2>Hyderabadi Chicken Dum Biryani</h2>
                <p>Slow-cooked on dum with hand-picked spices, caramelized onions, and tender chicken. Available only today — limited quantity.</p>
                <div class="special-price">
                    <span class="price">₹329</span>
                    <span class="old-price">₹399</span>
                    <span class="discount-tag">18% OFF</span>
                </div>
                <a href="pages/menu.php" class="btn btn-primary">Order Now</a>
            </div>
        </div>
    </section>

    <!-- ==================== OFFERS SECTION ==================== -->
    <section class="section offers-section">
        <div class="container fade-in-up">
            <div class="section-heading light">
                <h2>Offers For You</h2>
                <p>Grab these deals before they're gone.</p>
            </div>
            <div class="offers-grid">
                <div class="offer-card">
                    <i class="fa-solid fa-tags"></i>
                    <h3>FLAT 50% OFF</h3>
                    <p>On your first order above ₹199. Use code <strong>WELCOME50</strong></p>
                </div>
                <div class="offer-card">
                    <i class="fa-solid fa-truck-fast"></i>
                    <h3>FREE DELIVERY</h3>
                    <p>On all orders above ₹299, all day, every day.</p>
                </div>
                <div class="offer-card">
                    <i class="fa-solid fa-gift"></i>
                    <h3>COMBO DEALS</h3>
                    <p>Save up to ₹120 on curated meal combos.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ==================== WHY CHOOSE US ==================== -->
    <section class="section">
        <div class="container fade-in-up">
            <div class="section-heading">
                <h2>Why Choose Foodo</h2>
                <p>Simple reasons our customers keep coming back.</p>
            </div>
            <div class="features-grid">
                <div class="feature-card">
                    <i class="fa-solid fa-bolt"></i>
                    <h3>Fast Delivery</h3>
                    <p>Hot food delivered to your door in 25-30 minutes on average.</p>
                </div>
                <div class="feature-card">
                    <i class="fa-solid fa-leaf"></i>
                    <h3>Fresh Ingredients</h3>
                    <p>Every dish is prepared fresh using quality, hand-picked ingredients.</p>
                </div>
                <div class="feature-card">
                    <i class="fa-solid fa-shield-halved"></i>
                    <h3>Secure Payments</h3>
                    <p>Multiple safe payment options, including cash on delivery.</p>
                </div>
                <div class="feature-card">
                    <i class="fa-solid fa-headset"></i>
                    <h3>24/7 Support</h3>
                    <p>Our support team is here for you, any time, any day.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ==================== CUSTOMER REVIEWS ==================== -->
    <section class="section reviews-section">
        <div class="container fade-in-up">
            <div class="section-heading">
                <h2>What Our Customers Say</h2>
                <p>Real feedback from real food lovers.</p>
            </div>
            <div class="reviews-grid">
                <div class="review-card">
                    <div class="review-stars"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></div>
                    <p>"Semma fast-ah Chicken Dum Biryani deliver aachu! Packaging romba neat, and taste super spicy-ah true Hyderabadi flavor."</p>
                    <div class="reviewer">
                        <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=60&h=60&fit=crop&crop=face" alt="Aarav Mehta">
                        <div><strong>Aarav Mehta</strong><span>Chennai</span></div>
                    </div>
                </div>
                <div class="review-card">
                    <div class="review-stars"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></div>
                    <p>"White Sauce Pasta &amp; Cheese Burst Pizza were incredibly rich and cheesy. The crust was soft and perfectly baked. Absolutely loved it!"</p>
                    <div class="reviewer">
                        <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=60&h=60&fit=crop&crop=face" alt="Sneha Reddy">
                        <div><strong>Sneha Reddy</strong><span>Hyderabad</span></div>
                    </div>
                </div>
                <div class="review-card">
                    <div class="review-stars"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></div>
                    <p>"Masala Dosa is crispy and hot even when delivered. Great breakfast combos, and the pricing is very reasonable."</p>
                    <div class="reviewer">
                        <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=60&h=60&fit=crop&crop=face" alt="Rohan Iyer">
                        <div><strong>Rohan Iyer</strong><span>Bengaluru</span></div>
                    </div>
                </div>
                <div class="review-card">
                    <div class="review-stars"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></div>
                    <p>"Paneer Butter Masala and Naan combo taste was premium restaurant style. My family really enjoyed the food. Highly recommended!"</p>
                    <div class="reviewer">
                        <img src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=60&h=60&fit=crop&crop=face" alt="Deepika Nair">
                        <div><strong>Deepika Nair</strong><span>Chennai</span></div>
                    </div>
                </div>
                <div class="review-card">
                    <div class="review-stars"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star-half-stroke"></i></div>
                    <p>"Midnight order paneenpa, Hakka Noodles and Spring Rolls delivered hot within 25 mins. Super quick and reliable delivery service!"</p>
                    <div class="reviewer">
                        <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=60&h=60&fit=crop&crop=face" alt="Vikram Singh">
                        <div><strong>Vikram Singh</strong><span>Trichy</span></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ==================== CTA BANNER ==================== -->
    <section class="section cta-banner-section">
        <div class="container cta-banner-inner fade-in-up">
            <div class="cta-banner-text">
                <h2>Ready to Order?</h2>
                <p>Join thousands of food lovers. Order your favorite meals with just a few clicks.</p>
                <a href="pages/menu.php" class="btn btn-primary">Browse Menu <i class="fa-solid fa-arrow-right"></i></a>
            </div>
            <div class="cta-banner-image">
                <img src="https://images.unsplash.com/photo-1476224203421-9ac39bcb3327?w=500&h=400&fit=crop" alt="Delicious food spread">
            </div>
        </div>
    </section>


<?php
require_once 'includes/footer.php';
?>
