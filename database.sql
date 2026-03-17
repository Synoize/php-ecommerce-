-- ---------------------------------------------
-- WATCH ECOMMERCE DATABASE
-- Production-ready SQL schema
-- ---------------------------------------------

CREATE DATABASE IF NOT EXISTS watch_ecommerce
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE watch_ecommerce;

CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(190) NOT NULL,
  password VARCHAR(255) NOT NULL,
  phone VARCHAR(20) DEFAULT NULL,
  role ENUM('user', 'admin') NOT NULL DEFAULT 'user',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_users_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS addresses (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  full_name VARCHAR(120) NOT NULL,
  phone VARCHAR(20) NOT NULL,
  address_line TEXT NOT NULL,
  city VARCHAR(100) NOT NULL,
  state VARCHAR(100) NOT NULL,
  pincode VARCHAR(10) NOT NULL,
  country VARCHAR(100) NOT NULL DEFAULT 'India',
  is_default TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_addresses_user (user_id),
  CONSTRAINT fk_addresses_user
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS categories (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  image VARCHAR(255) DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_categories_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS products (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(190) NOT NULL,
  description TEXT DEFAULT NULL,
  category_id INT UNSIGNED DEFAULT NULL,
  price DECIMAL(10,2) NOT NULL,
  stock INT UNSIGNED NOT NULL DEFAULT 0,
  image VARCHAR(255) DEFAULT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_products_category (category_id),
  KEY idx_products_active (is_active),
  CONSTRAINT chk_products_price_non_negative CHECK (price >= 0),
  CONSTRAINT fk_products_category
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS product_images (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  product_id INT UNSIGNED NOT NULL,
  image_url VARCHAR(255) NOT NULL,
  sort_order INT UNSIGNED NOT NULL DEFAULT 0,
  KEY idx_product_images_product (product_id),
  CONSTRAINT fk_product_images_product
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS product_box_options (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  product_id INT UNSIGNED NOT NULL,
  name VARCHAR(150) NOT NULL,
  image VARCHAR(255) DEFAULT NULL,
  price DECIMAL(10,2) NOT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_product_box_options_product (product_id),
  CONSTRAINT chk_product_box_options_price_non_negative CHECK (price >= 0),
  CONSTRAINT fk_product_box_options_product
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS cart (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  product_id INT UNSIGNED NOT NULL,
  quantity INT UNSIGNED NOT NULL DEFAULT 1,
  box_option_id INT UNSIGNED DEFAULT NULL,
  box_quantity INT UNSIGNED NOT NULL DEFAULT 0,
  added_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_cart_user_product (user_id, product_id),
  KEY idx_cart_user (user_id),
  KEY idx_cart_product (product_id),
  KEY idx_cart_box_option (box_option_id),
  CONSTRAINT chk_cart_quantity_positive CHECK (quantity >= 1),
  CONSTRAINT chk_cart_box_quantity_non_negative CHECK (box_quantity >= 0),
  CONSTRAINT fk_cart_user
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_cart_product
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
  CONSTRAINT fk_cart_box_option
    FOREIGN KEY (box_option_id) REFERENCES product_box_options(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS wishlist (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  product_id INT UNSIGNED NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_wishlist_user_product (user_id, product_id),
  KEY idx_wishlist_user (user_id),
  KEY idx_wishlist_product (product_id),
  CONSTRAINT fk_wishlist_user
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_wishlist_product
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS orders (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  address_id INT UNSIGNED DEFAULT NULL,
  total_amount DECIMAL(10,2) NOT NULL,
  status ENUM('pending', 'confirmed', 'shipped', 'delivered', 'cancelled') NOT NULL DEFAULT 'pending',
  payment_method VARCHAR(50) DEFAULT NULL,
  payment_status ENUM('pending', 'paid', 'failed') NOT NULL DEFAULT 'pending',
  razorpay_payment_id VARCHAR(100) DEFAULT NULL,
  razorpay_order_id VARCHAR(100) DEFAULT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_orders_user (user_id),
  KEY idx_orders_status (status),
  KEY idx_orders_created_at (created_at),
  KEY idx_orders_razorpay_order (razorpay_order_id),
  CONSTRAINT chk_orders_total_non_negative CHECK (total_amount >= 0),
  CONSTRAINT fk_orders_user
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_orders_address
    FOREIGN KEY (address_id) REFERENCES addresses(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS order_items (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_id INT UNSIGNED NOT NULL,
  product_id INT UNSIGNED NOT NULL,
  quantity INT UNSIGNED NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  box_option_id INT UNSIGNED DEFAULT NULL,
  box_option_name VARCHAR(150) DEFAULT NULL,
  box_option_price DECIMAL(10,2) DEFAULT NULL,
  box_quantity INT UNSIGNED NOT NULL DEFAULT 0,
  KEY idx_order_items_order (order_id),
  KEY idx_order_items_product (product_id),
  KEY idx_order_items_box_option (box_option_id),
  CONSTRAINT chk_order_items_quantity_positive CHECK (quantity >= 1),
  CONSTRAINT chk_order_items_box_quantity_non_negative CHECK (box_quantity >= 0),
  CONSTRAINT chk_order_items_price_non_negative CHECK (price >= 0),
  CONSTRAINT fk_order_items_order
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  CONSTRAINT fk_order_items_product
    FOREIGN KEY (product_id) REFERENCES products(id),
  CONSTRAINT fk_order_items_box_option
    FOREIGN KEY (box_option_id) REFERENCES product_box_options(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS payments (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_id INT UNSIGNED NOT NULL,
  payment_gateway VARCHAR(50) NOT NULL,
  transaction_id VARCHAR(150) NOT NULL,
  amount DECIMAL(10,2) NOT NULL,
  status ENUM('success', 'failed', 'pending') NOT NULL DEFAULT 'pending',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_payments_transaction (transaction_id),
  KEY idx_payments_order (order_id),
  CONSTRAINT chk_payments_amount_non_negative CHECK (amount >= 0),
  CONSTRAINT fk_payments_order
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS reviews (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  product_id INT UNSIGNED NOT NULL,
  user_id INT UNSIGNED NOT NULL,
  rating TINYINT UNSIGNED NOT NULL,
  comment TEXT DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_reviews_product_user (product_id, user_id),
  KEY idx_reviews_product (product_id),
  KEY idx_reviews_user (user_id),
  CONSTRAINT chk_reviews_rating_range CHECK (rating BETWEEN 1 AND 5),
  CONSTRAINT fk_reviews_product
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
  CONSTRAINT fk_reviews_user
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS coupons (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(50) NOT NULL,
  discount_percent INT UNSIGNED NOT NULL,
  valid_from DATE DEFAULT NULL,
  valid_to DATE DEFAULT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_coupons_code (code),
  CONSTRAINT chk_coupons_discount_range CHECK (discount_percent BETWEEN 1 AND 100),
  CONSTRAINT chk_coupons_validity_range CHECK (valid_to IS NULL OR valid_from IS NULL OR valid_to >= valid_from)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS order_coupons (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_id INT UNSIGNED NOT NULL,
  coupon_id INT UNSIGNED NOT NULL,
  discount_amount DECIMAL(10,2) NOT NULL,
  UNIQUE KEY uq_order_coupon (order_id, coupon_id),
  KEY idx_order_coupons_coupon (coupon_id),
  CONSTRAINT chk_order_coupons_discount_non_negative CHECK (discount_amount >= 0),
  CONSTRAINT fk_order_coupons_order
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  CONSTRAINT fk_order_coupons_coupon
    FOREIGN KEY (coupon_id) REFERENCES coupons(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS slides (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  type VARCHAR(20) NOT NULL,
  file_path VARCHAR(255) NOT NULL,
  title VARCHAR(150) DEFAULT NULL,
  description TEXT DEFAULT NULL,
  button_name VARCHAR(100) DEFAULT NULL,
  button_link VARCHAR(255) DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO categories (name, image) VALUES
('G-Shock', 'images/uploads/categories/g-shock.png'),
('For Men', 'images/uploads/categories/man.png'),
('For Women', 'images/uploads/categories/woman.png'),
('Automatic', 'images/uploads/categories/automatic-mechanical.png')
ON DUPLICATE KEY UPDATE
  image = VALUES(image);

INSERT INTO users (name, email, password, role)
VALUES (
  'Admin',
  'admin@gmail.com',
  '$2y$10$HuZFYH11qin0LzYkV1W3luJX5r8xvViWPYO7dJyZhZDnn0vSeXpJ6',
  'admin'
)
ON DUPLICATE KEY UPDATE
  name = VALUES(name),
  password = VALUES(password),
  role = VALUES(role);

INSERT INTO products (name, description, category_id, price, stock, image)
SELECT
  'Women Classic Watch',
  'Elegant analog watch designed for women with a slim dial and stylish strap.',
  c.id,
  2499.00,
  15,
  'images/uploads/products/_02.png'
FROM categories c
WHERE c.name = 'For Women'
  AND NOT EXISTS (
    SELECT 1 FROM products WHERE name = 'Women Classic Watch'
  );

INSERT INTO products (name, description, category_id, price, stock, image)
SELECT
  'Men Classic Watch',
  'Premium analog wristwatch for men with leather strap.',
  c.id,
  2799.00,
  20,
  'images/uploads/products/_01.png'
FROM categories c
WHERE c.name = 'For Men'
  AND NOT EXISTS (
    SELECT 1 FROM products WHERE name = 'Men Classic Watch'
  );

INSERT INTO products (name, description, category_id, price, stock, image)
SELECT
  'G-Shock Digital Watch',
  'G-Shock digital watch for everyone.',
  c.id,
  999.00,
  25,
  'images/uploads/products/_03.png'
FROM categories c
WHERE c.name = 'G-Shock'
  AND NOT EXISTS (
    SELECT 1 FROM products WHERE name = 'G-Shock Digital Watch'
  );

INSERT INTO products (name, description, category_id, price, stock, image)
SELECT
  'Automatic Mechanical Watch',
  'Automatic mechanical watch with premium movement.',
  c.id,
  3999.00,
  10,
  'images/uploads/products/_04.png'
FROM categories c
WHERE c.name = 'Automatic'
  AND NOT EXISTS (
    SELECT 1 FROM products WHERE name = 'Automatic Mechanical Watch'
  );

INSERT INTO product_images (product_id, image_url, sort_order)
SELECT p.id, p.image, 0
FROM products p
WHERE NOT EXISTS (
  SELECT 1 FROM product_images pi WHERE pi.product_id = p.id
);

INSERT INTO product_box_options (product_id, name, image, price, is_active)
SELECT p.id, 'G-Shock Kit Box', 'images/uploads/boxes/gshock-kit-box.jpg', 400.00, 1
FROM products p
WHERE p.name = 'Women Classic Watch'
  AND NOT EXISTS (
    SELECT 1 FROM product_box_options b WHERE b.product_id = p.id AND b.name = 'G-Shock Kit Box'
  );

INSERT INTO product_box_options (product_id, name, image, price, is_active)
SELECT p.id, 'Premium Gift Box', 'images/uploads/boxes/premium-gift-box.jpg', 550.00, 1
FROM products p
WHERE p.name = 'Men Classic Watch'
  AND NOT EXISTS (
    SELECT 1 FROM product_box_options b WHERE b.product_id = p.id AND b.name = 'Premium Gift Box'
  );

INSERT INTO coupons (code, discount_percent, valid_from, valid_to, is_active)
VALUES
('WELCOME10', 10, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 365 DAY), 1),
('FESTIVE15', 15, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 180 DAY), 1)
ON DUPLICATE KEY UPDATE
  discount_percent = VALUES(discount_percent),
  valid_from = VALUES(valid_from),
  valid_to = VALUES(valid_to),
  is_active = VALUES(is_active);

INSERT INTO slides (type, file_path, title, description, button_name, button_link)
VALUES (
  'image',
  'images/uploads/carousel/_1.jpg',
  'Watch Ecommerce',
  'Browse premium watches with cart, reviews, wishlist, checkout, and admin order tracking.',
  'Shop Now',
  'shop.php'
),
 (
  'video',
  'images/uploads/carousel/_01.mp4',
  'Watch Ecommerce',
  'Browse premium watches with cart, reviews, wishlist, checkout, and admin order tracking.',
  'Shop Now',
  'shop.php'
);
