-- =====================================
-- WATCH ECOMMERCE DATABASE
-- Production Ready SQL Schema
-- =====================================

CREATE DATABASE IF NOT EXISTS watch_ecommerce
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE watch_ecommerce;

-- =====================================
-- USERS
-- =====================================

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  phone VARCHAR(20),
  role ENUM('user','admin') DEFAULT 'user',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =====================================
-- USER ADDRESSES
-- =====================================

CREATE TABLE addresses (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  full_name VARCHAR(120),
  phone VARCHAR(20),
  address_line TEXT,
  city VARCHAR(100),
  state VARCHAR(100),
  pincode VARCHAR(10),
  country VARCHAR(100) DEFAULT 'India',
  is_default BOOLEAN DEFAULT FALSE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- =====================================
-- CATEGORIES
-- =====================================

CREATE TABLE categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  image VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =====================================
-- PRODUCTS
-- =====================================

CREATE TABLE products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(190) NOT NULL,
  description TEXT,
  category_id INT,
  price DECIMAL(10,2) NOT NULL,
  stock INT DEFAULT 0,
  image VARCHAR(255),
  is_active BOOLEAN DEFAULT TRUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (category_id) REFERENCES categories(id)
  ON DELETE SET NULL
);

-- =====================================
-- PRODUCT IMAGES
-- =====================================

CREATE TABLE product_images (
  id INT AUTO_INCREMENT PRIMARY KEY,
  product_id INT NOT NULL,
  image_url VARCHAR(255),
  FOREIGN KEY (product_id) REFERENCES products(id)
  ON DELETE CASCADE
);

-- =====================================
-- SHOPPING CART
-- =====================================

CREATE TABLE cart (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  product_id INT,
  quantity INT DEFAULT 1,
  added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE(user_id, product_id),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- =====================================
-- WISHLIST
-- =====================================

CREATE TABLE wishlist (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  product_id INT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE(user_id, product_id),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- =====================================
-- ORDERS
-- =====================================

CREATE TABLE orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  address_id INT,
  total_amount DECIMAL(10,2) NOT NULL,
  status ENUM('pending','confirmed','shipped','delivered','cancelled')
  DEFAULT 'pending',
  payment_method VARCHAR(50),
  payment_status ENUM('pending','paid','failed') DEFAULT 'pending',
  razorpay_payment_id VARCHAR(100),
  razorpay_order_id VARCHAR(100),
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (address_id) REFERENCES addresses(id)
);

-- =====================================
-- ORDER ITEMS
-- =====================================

CREATE TABLE order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  product_id INT NOT NULL,
  quantity INT NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (order_id) REFERENCES orders(id)
  ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id)
);

-- =====================================
-- PAYMENTS
-- =====================================

CREATE TABLE payments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT,
  payment_gateway VARCHAR(50),
  transaction_id VARCHAR(150),
  amount DECIMAL(10,2),
  status ENUM('success','failed','pending'),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (order_id) REFERENCES orders(id)
);

-- =====================================
-- PRODUCT REVIEWS
-- =====================================

CREATE TABLE reviews (
  id INT AUTO_INCREMENT PRIMARY KEY,
  product_id INT,
  user_id INT,
  rating INT CHECK (rating BETWEEN 1 AND 5),
  comment TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (product_id) REFERENCES products(id)
  ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id)
  ON DELETE CASCADE
);

-- =====================================
-- COUPONS
-- =====================================

CREATE TABLE coupons (
  id INT AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(50) UNIQUE,
  discount_percent INT,
  valid_from DATE,
  valid_to DATE,
  is_active BOOLEAN DEFAULT TRUE
);

-- =====================================
-- ORDER COUPONS
-- =====================================

CREATE TABLE order_coupons (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT,
  coupon_id INT,
  discount_amount DECIMAL(10,2),
  FOREIGN KEY (order_id) REFERENCES orders(id),
  FOREIGN KEY (coupon_id) REFERENCES coupons(id)
);

-- =====================================
-- INDEXES (FOR PERFORMANCE)
-- =====================================

CREATE INDEX idx_products_category ON products(category_id);
CREATE INDEX idx_orders_user ON orders(user_id);
CREATE INDEX idx_cart_user ON cart(user_id);
CREATE INDEX idx_reviews_product ON reviews(product_id);

-- =====================================
-- DEFAULT CATEGORIES
-- =====================================

INSERT INTO categories (name,image) VALUES
('G-Shock','https://www.danielwellington.com/cdn/shop/files/51a753b76ff3491e5cf13ebaf42f835b48e16a19.png'),
('For Men','https://png.pngtree.com/png-vector/20230906/ourmid/pngtree-wristwatch-analog-classic-brown-leather-strap-watch-png-image_10001801.png'),
('For Women','https://www.danielwellington.com/cdn/shop/files/51a753b76ff3491e5cf13ebaf42f835b48e16a19.png'),
('Automatic Mechanica','https://www.danielwellington.com/cdn/shop/files/51a753b76ff3491e5cf13ebaf42f835b48e16a19.png');

-- =====================================
-- DEFAULT ADMIN USER
-- email: admin@demo.com
-- password: Admin@123
-- =====================================

INSERT INTO users (name,email,password,role)
VALUES (
'Admin',
'admin@demo.com',
'$2y$10$5K8nOcM4zQwM/knuXxGZ0es8yGHnXyQFQpC8W1pWnZ7mx1fN0n3Uq',
'admin'
);

-- =====================================
-- SAMPLE PRODUCTS
-- =====================================

INSERT INTO products (name,description,category_id,price,stock,image) VALUES
('Women Classic Watch',
'Elegant analog watch designed for women with a slim dial and stylish strap.',
1,
2499.00,
15,
'https://www.danielwellington.com/cdn/shop/files/51a753b76ff3491e5cf13ebaf42f835b48e16a19.png'),

('Men Classic Watch',
'Premium analog wristwatch for men with leather strap.',
2,
2799.00,
20,
'https://png.pngtree.com/png-vector/20230906/ourmid/pngtree-wristwatch-analog-classic-brown-leather-strap-watch-png-image_10001801.png'),

('Kids Digital Watch',
'Colorful digital watch designed for kids.',
3,
999.00,
25,
'https://cdn.grofers.com/cdn-cgi/image/f=auto,fit=scale-down,q=70,metadata=none,w=1080/da/cms-assets/cms/product/4c216a16-854c-4bb4-901f-0169d2eeae41.png');