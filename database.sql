-- =========================================================
-- FOODO ORDERING WEBSITE - DATABASE SQL FILE (HOSTING VERSION)
--
-- HOW TO USE ON INFINITYFREE / SHARED HOSTING:
-- 1. Open phpMyAdmin and SELECT your existing database first
--    (e.g. if0_42338868_foodo) using the left sidebar
-- 2. Click the "Import" tab (while that database stays selected)
-- 3. Choose this file and click "Go"
-- (No CREATE DATABASE / USE statements here — shared hosting
--  does not allow creating databases via SQL, so this file creates
--  tables directly inside whichever database you have selected.)
-- =========================================================

-- =========================================================
-- TABLE 1: admin
-- Stores admin panel login credentials
-- =========================================================
CREATE TABLE admin (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,   -- stored using PHP password_hash()
    email VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =========================================================
-- TABLE 2: users
-- Stores registered customer accounts
-- =========================================================
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(15) NOT NULL,
    password VARCHAR(255) NOT NULL,   -- stored using PHP password_hash()
    address TEXT,
    city VARCHAR(50),
    state VARCHAR(50),
    pincode VARCHAR(10),
    profile_image VARCHAR(255) DEFAULT 'default-avatar.png',
    reset_token VARCHAR(255) DEFAULT NULL,
    reset_expiry DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =========================================================
-- TABLE 3: food_categories
-- The 18 food categories shown on Home/Menu pages
-- =========================================================
CREATE TABLE food_categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(50) NOT NULL,
    slug VARCHAR(50) NOT NULL UNIQUE,   -- used in category-details.html?cat=slug
    category_image VARCHAR(255)
) ENGINE=InnoDB;

-- =========================================================
-- TABLE 4: foods
-- Every dish on the menu, linked to a category
-- =========================================================
CREATE TABLE foods (
    food_id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    food_name VARCHAR(100) NOT NULL,
    description VARCHAR(255),
    price DECIMAL(8,2) NOT NULL,
    image VARCHAR(255),
    rating DECIMAL(2,1) DEFAULT 4.5,
    is_available TINYINT(1) DEFAULT 1,   -- 1 = available, 0 = sold out
    is_bestseller TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES food_categories(category_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =========================================================
-- TABLE 5: cart
-- Items currently in a logged-in user's cart
-- =========================================================
CREATE TABLE cart (
    cart_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    food_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (food_id) REFERENCES foods(food_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =========================================================
-- TABLE 6: orders
-- One row per placed order
-- =========================================================
CREATE TABLE orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(15) NOT NULL,
    email VARCHAR(100) NOT NULL,
    address TEXT NOT NULL,
    city VARCHAR(50) NOT NULL,
    state VARCHAR(50) NOT NULL,
    pincode VARCHAR(10) NOT NULL,
    payment_method VARCHAR(20) NOT NULL,   -- 'cod', 'upi', 'card'
    subtotal DECIMAL(8,2) NOT NULL,
    delivery_fee DECIMAL(8,2) NOT NULL DEFAULT 40.00,
    tax DECIMAL(8,2) NOT NULL,
    grand_total DECIMAL(8,2) NOT NULL,
    discount_amount DECIMAL(8,2) DEFAULT 0.00,
    coupon_code VARCHAR(50) DEFAULT NULL,
    order_status VARCHAR(20) NOT NULL DEFAULT 'preparing',  -- preparing, delivered, cancelled
    razorpay_order_id VARCHAR(100) DEFAULT NULL,    -- Razorpay order ID (e.g. order_xxxxx)
    razorpay_payment_id VARCHAR(100) DEFAULT NULL,  -- Razorpay payment ID (e.g. pay_xxxxx)
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =========================================================
-- TABLE 7: order_items
-- Individual dishes inside each order
-- =========================================================
CREATE TABLE order_items (
    order_item_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    food_id INT NOT NULL,
    food_name VARCHAR(100) NOT NULL,   -- stored again here so order history stays
    price DECIMAL(8,2) NOT NULL,       -- correct even if the food's price changes later
    quantity INT NOT NULL,
    line_total DECIMAL(8,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (food_id) REFERENCES foods(food_id)
) ENGINE=InnoDB;

-- =========================================================
-- TABLE 8: contact_messages
-- Messages submitted through the Contact page
-- =========================================================
CREATE TABLE contact_messages (
    message_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(150) NOT NULL,
    message TEXT NOT NULL,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;


-- =========================================================
-- SAMPLE DATA
-- =========================================================

-- ---------- Admin login ----------
-- Username: admin   Password: admin123  (hashed below with PHP password_hash)
INSERT INTO admin (username, password, email) VALUES
('admin', '$2b$12$6uNFzKofUxdtOokS50wb1eVmq.7cQdOH8DeeNQqdt19KMzWzdsbWC', 'admin@tastybite.com');

-- ---------- Sample Users ----------
-- Both sample accounts use the password: password123
INSERT INTO users (full_name, email, phone, password, address, city, state, pincode) VALUES
('Priya Sharma', 'priya.sharma@email.com', '9876543210', '$2b$12$ZC2WzH3LiBfoev3d5omw1uUiLL.IyU2svneIozZO5mYNTSVE/rcLm', '24, Anna Salai, T. Nagar', 'Chennai', 'Tamil Nadu', '600017'),
('Arjun Mehta', 'arjun.mehta@email.com', '9123456780', '$2b$12$ZC2WzH3LiBfoev3d5omw1uUiLL.IyU2svneIozZO5mYNTSVE/rcLm', '12, MG Road', 'Bengaluru', 'Karnataka', '560001');

-- ---------- Food Categories (18 total) ----------
INSERT INTO food_categories (category_name, slug, category_image) VALUES
('Pizza', 'pizza', 'pizza.jpg'),
('Burger', 'burger', 'burger.jpg'),
('Fried Chicken', 'fried-chicken', 'fried-chicken.jpg'),
('Biryani', 'biryani', 'biryani.jpg'),
('South Indian', 'south-indian', 'south-indian.jpg'),
('North Indian', 'north-indian', 'north-indian.jpg'),
('Chinese', 'chinese', 'chinese.jpg'),
('Sandwich', 'sandwich', 'sandwich.jpg'),
('Pasta', 'pasta', 'pasta.jpg'),
('Noodles', 'noodles', 'noodles.jpg'),
('Desserts', 'desserts', 'desserts.jpg'),
('Ice Cream', 'ice-cream', 'ice-cream.jpg'),
('Juices', 'juices', 'juices.jpg'),
('Coffee', 'coffee', 'coffee.jpg'),
('Tea', 'tea', 'tea.jpg'),
('Soft Drinks', 'soft-drinks', 'soft-drinks.jpg'),
('Snacks', 'snacks', 'snacks.jpg'),
('Healthy Food', 'healthy', 'healthy.jpg');

-- ---------- Foods (matches the items already shown in the frontend Menu page) ----------
INSERT INTO foods (category_id, food_name, description, price, image, rating, is_available, is_bestseller) VALUES
(1, 'Cheese Burst Pizza', 'Loaded mozzarella with a molten cheese-stuffed crust.', 279.00, 'cheese-burst-pizza.jpg', 4.7, 1, 1),
(1, 'Margherita Pizza', 'Classic cheese & basil pizza on a thin, crispy crust.', 249.00, 'margherita-pizza.jpg', 4.7, 1, 1),
(2, 'Classic Cheese Burger', 'Grilled patty, melted cheese, fresh lettuce & tomato.', 169.00, 'classic-burger.jpg', 4.6, 1, 0),
(3, 'Crispy Fried Chicken', 'Golden, crunchy fried chicken with secret spice blend.', 219.00, 'fried-chicken.jpg', 4.8, 1, 0),
(4, 'Chicken Dum Biryani', 'Fragrant basmati rice layered with spiced chicken.', 289.00, 'chicken-biryani.jpg', 4.9, 1, 1),
(5, 'Masala Dosa', 'Crispy rice crepe filled with spiced potato masala.', 99.00, 'masala-dosa.jpg', 4.6, 1, 0),
(6, 'Paneer Butter Masala', 'Creamy tomato gravy with soft paneer cubes.', 239.00, 'paneer-butter-masala.jpg', 4.7, 1, 0),
(7, 'Chilli Garlic Fried Rice', 'Wok-tossed rice with garlic, chilli and vegetables.', 189.00, 'fried-rice.jpg', 4.5, 1, 0),
(8, 'Grilled Veg Sandwich', 'Grilled bread stuffed with fresh veggies & cheese.', 129.00, 'veg-sandwich.jpg', 4.4, 1, 0),
(9, 'White Sauce Pasta', 'Creamy alfredo pasta with herbs and parmesan.', 219.00, 'white-sauce-pasta.jpg', 4.5, 1, 0),
(10, 'Hakka Noodles', 'Wok-tossed noodles with crunchy vegetables.', 179.00, 'hakka-noodles.jpg', 4.5, 0, 0),
(11, 'Chocolate Lava Cake', 'Warm chocolate cake with a gooey molten center.', 149.00, 'lava-cake.jpg', 4.9, 1, 0),
(12, 'Belgian Chocolate Ice Cream', 'Rich Belgian chocolate scoop with choco chips.', 99.00, 'ice-cream.jpg', 4.7, 1, 0),
(13, 'Fresh Watermelon Juice', 'Chilled, refreshing juice with no added sugar.', 79.00, 'watermelon-juice.jpg', 4.4, 1, 0),
(14, 'Cold Coffee', 'Chilled coffee blended with milk and ice cream.', 89.00, 'cold-coffee.jpg', 4.6, 1, 0),
(15, 'Masala Chai', 'Spiced Indian tea brewed with milk and ginger.', 39.00, 'masala-chai.jpg', 4.5, 1, 0),
(16, 'Chilled Cola (500ml)', 'Ice-cold soft drink, served straight from the fridge.', 49.00, 'soft-drink.jpg', 4.3, 1, 0),
(17, 'Veg Spring Rolls', 'Crispy rolls stuffed with fresh mixed vegetables.', 129.00, 'spring-rolls.jpg', 4.4, 1, 0),
(18, 'Quinoa Buddha Bowl', 'Quinoa, greens, chickpeas and a light lemon dressing.', 199.00, 'buddha-bowl.jpg', 4.6, 1, 0);

-- ---------- Sample Cart (for user_id 1, matching the Cart page demo) ----------
INSERT INTO cart (user_id, food_id, quantity) VALUES
(1, 5, 2),   -- Chicken Dum Biryani x2
(1, 1, 1),   -- Cheese Burst Pizza x1
(1, 17, 2);  -- Chilled Cola x2

-- ---------- Sample Orders (matching the My Orders page demo) ----------
INSERT INTO orders (user_id, full_name, phone, email, address, city, state, pincode, payment_method, subtotal, delivery_fee, tax, grand_total, order_status, order_date) VALUES
(1, 'Priya Sharma', '9876543210', 'priya.sharma@email.com', '24, Anna Salai, T. Nagar', 'Chennai', 'Tamil Nadu', '600017', 'cod', 955.00, 40.00, 48.00, 1043.00, 'preparing', '2026-07-01 20:45:00'),
(1, 'Priya Sharma', '9876543210', 'priya.sharma@email.com', '24, Anna Salai, T. Nagar', 'Chennai', 'Tamil Nadu', '600017', 'upi', 358.00, 0.00, 0.00, 358.00, 'delivered', '2026-06-28 13:20:00'),
(1, 'Priya Sharma', '9876543210', 'priya.sharma@email.com', '24, Anna Salai, T. Nagar', 'Chennai', 'Tamil Nadu', '600017', 'card', 297.00, 40.00, 0.00, 297.00, 'cancelled', '2026-06-10 19:15:00');

-- ---------- Sample Order Items ----------
INSERT INTO order_items (order_id, food_id, food_name, price, quantity, line_total) VALUES
(1, 5, 'Chicken Dum Biryani', 289.00, 2, 578.00),
(1, 1, 'Cheese Burst Pizza', 279.00, 1, 279.00),
(1, 17, 'Chilled Cola (500ml)', 49.00, 2, 98.00),
(2, 11, 'Hakka Noodles', 179.00, 2, 358.00),
(3, 6, 'Masala Dosa', 99.00, 3, 297.00);

-- ---------- Sample Contact Message ----------
INSERT INTO contact_messages (name, email, subject, message) VALUES
('Rahul Kumar', 'rahul.kumar@email.com', 'Question about catering', 'Hi, do you offer catering for a 50-person office event? Please share pricing details.');
