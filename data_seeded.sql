-- ---------------------------------------------
-- WATCH ECOMMERCE SEEDED DATA
-- Run after database.sql
-- ---------------------------------------------

USE watch_ecommerce;

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

SET @automatic_desc = 'This Product is the Same as Original (Master Quality) with 3 month Machine Replacement Warranty. If you want the original box kit, extra charges apply! Cash on Delivery available all over India.';
SET @mens_desc = 'This Product is the Same as Original (Master Quality) with 3 month Machine Replacement Warranty. If you want the original box kit, extra charges apply! Cash on Delivery available all over India.';
SET @mens_chrono_desc = 'This Product is the Same as Original (Master Quality) with 3 month Machine Replacement Warranty. If you want the original box kit, extra charges apply! Cash on Delivery available all over India. All Chrono working.';
SET @edifice_desc = 'This Product is the Same as Original (Master Quality) with 3 month Machine Replacement Warranty. original box free, no extra charges apply! Cash on Delivery available all over India. All Chrono working.';
SET @gshock_desc = 'Brand - G-Shock Casio. For Unisex. 7AA Premium Collection. Water and impact Resistant. Countdown timer. Mineral Glass. Digital drive System. Working StopWatch/ Day/Date Digital Display. Easy Pin Buckled Lock. Working AutoLight. Working World time with Automatic Reset.';

INSERT INTO products (name, description, category_id, price, stock, image)
SELECT seed.name, seed.description, c.id, seed.price, seed.stock, seed.image
FROM (
    SELECT 'Women Classic Watch' AS name, 'Elegant analog watch designed for women with a slim dial and stylish strap.' AS description, 'For Women' AS category_name, 2499.00 AS price, 15 AS stock, 'images/uploads/products/_02.png' AS image
    UNION ALL SELECT 'Men Classic Watch', 'Premium analog wristwatch for men with leather strap.', 'For Men', 2799.00, 20, 'images/uploads/products/_01.png'
    UNION ALL SELECT 'G-Shock Digital Watch', 'G-Shock digital watch for everyone.', 'G-Shock', 999.00, 25, 'images/uploads/products/_03.png'
    UNION ALL SELECT 'Automatic Mechanical Watch', 'Automatic mechanical watch with premium movement.', 'Automatic', 3999.00, 10, 'images/uploads/products/_04.png'

    UNION ALL SELECT 'Tommy Hilfiger Automatic Silver Rose Gold Chain With White Dial', @automatic_desc, 'Automatic', 1999.00, 20, 'images/uploads/products/_04.png'
    UNION ALL SELECT 'Tommy Hilfiger Automatic Silver Rose Gold Chain With Black Dial', @automatic_desc, 'Automatic', 1999.00, 20, 'images/uploads/products/_04.png'
    UNION ALL SELECT 'Tommy Hilfiger Automatic Silver Chain With White Dial', @automatic_desc, 'Automatic', 1999.00, 20, 'images/uploads/products/_04.png'
    UNION ALL SELECT 'Tommy Hilfiger Automatic Silver Chain With Blue Dial', @automatic_desc, 'Automatic', 1999.00, 20, 'images/uploads/products/_04.png'
    UNION ALL SELECT 'Tommy Hilfiger Automatic Silver Chain With Black Dial', @automatic_desc, 'Automatic', 1999.00, 20, 'images/uploads/products/_04.png'
    UNION ALL SELECT 'Tommy Hilfiger Automatic Black Chain With Black Dial', @automatic_desc, 'Automatic', 1999.00, 20, 'images/uploads/products/_04.png'
    UNION ALL SELECT 'Tommy Hilfiger Automatic Silicone Brown Strap, Silver Case With Black Dial', @automatic_desc, 'Automatic', 1799.00, 20, 'images/uploads/products/_04.png'
    UNION ALL SELECT 'Tommy Hilfiger Automatic Silicone Grey Strap, Silver Case With Black Dial', @automatic_desc, 'Automatic', 1799.00, 20, 'images/uploads/products/_04.png'
    UNION ALL SELECT 'Tommy Hilfiger Automatic Silicone Olive Green Strap, Gold Case With White Dial', @automatic_desc, 'Automatic', 1799.00, 20, 'images/uploads/products/_04.png'
    UNION ALL SELECT 'Tommy Hilfiger Automatic Silicone Black Strap, Silver Case With Black Dial', @automatic_desc, 'Automatic', 1799.00, 20, 'images/uploads/products/_04.png'
    UNION ALL SELECT 'Tommy Hilfiger Automatic Silicone Black Strap, Black Case With Black Dial', @automatic_desc, 'Automatic', 1799.00, 20, 'images/uploads/products/_04.png'
    UNION ALL SELECT 'Tommy Hilfiger Automatic Leather Tan Brown Strap, Silver Rosegold Case With White Dial', @automatic_desc, 'Automatic', 1799.00, 20, 'images/uploads/products/_04.png'
    UNION ALL SELECT 'Tommy Hilfiger Automatic Leather Brown Strap, Silver Rosegold Case With Black Dial', @automatic_desc, 'Automatic', 1799.00, 20, 'images/uploads/products/_04.png'
    UNION ALL SELECT 'Tommy Hilfiger Automatic Leather Blue Strap, Silver Case With Blue Dial', @automatic_desc, 'Automatic', 1799.00, 20, 'images/uploads/products/_04.png'
    UNION ALL SELECT 'Tommy Hilfiger Automatic Leather Black Strap, Silver Case With Black Dial', @automatic_desc, 'Automatic', 1799.00, 20, 'images/uploads/products/_04.png'
    UNION ALL SELECT 'Tommy Hilfiger Automatic Leather Black Strap, Black Case With Black Dial', @automatic_desc, 'Automatic', 1799.00, 20, 'images/uploads/products/_04.png'
    UNION ALL SELECT 'Fosssil Automatic Towns-Man Edition Black Leather', @automatic_desc, 'Automatic', 1699.00, 18, 'images/uploads/products/_04.png'
    UNION ALL SELECT 'Fosssil Automatic Towns-Man Edition Tan Brown Leather', @automatic_desc, 'Automatic', 1699.00, 18, 'images/uploads/products/_04.png'
    UNION ALL SELECT 'Fosssil Automatic Bronson Leather', @automatic_desc, 'Automatic', 1699.00, 18, 'images/uploads/products/_04.png'
    UNION ALL SELECT 'Fosssil Automatic Brown Leather Black Case', @automatic_desc, 'Automatic', 1799.00, 18, 'images/uploads/products/_04.png'
    UNION ALL SELECT 'Fosssil Automatic Everett Brown Leather Black Case', @automatic_desc, 'Automatic', 1799.00, 18, 'images/uploads/products/_04.png'
    UNION ALL SELECT 'Fosssil Automatic Metal Silver Rose Gold', @automatic_desc, 'Automatic', 1899.00, 18, 'images/uploads/products/_04.png'
    UNION ALL SELECT 'Fosssil Automatic Black Metal Towns-Man Edition', @automatic_desc, 'Automatic', 1999.00, 18, 'images/uploads/products/_04.png'
    UNION ALL SELECT 'Fosssil Automatic Black Metal Brown shade', @automatic_desc, 'Automatic', 1999.00, 18, 'images/uploads/products/_04.png'
    UNION ALL SELECT 'Fosssil Automatic Black Metal Silver dial shade', @automatic_desc, 'Automatic', 1999.00, 18, 'images/uploads/products/_04.png'
    UNION ALL SELECT 'Cartier Automatic Leather', @automatic_desc, 'Automatic', 1999.00, 18, 'images/uploads/products/_04.png'
    UNION ALL SELECT 'Cartier Automatic Leather Arebic', @automatic_desc, 'Automatic', 1999.00, 18, 'images/uploads/products/_04.png'

    UNION ALL SELECT 'Edifice Chronograph Working Silver Black Dial', @edifice_desc, 'For Men', 1499.00, 20, 'images/uploads/products/_01.png'
    UNION ALL SELECT 'Edifice Chronograph Working Golden Silver White Dial', @edifice_desc, 'For Men', 1499.00, 20, 'images/uploads/products/_01.png'
    UNION ALL SELECT 'Edifice Chronograph Working Golden Silver Black Dial', @edifice_desc, 'For Men', 1499.00, 20, 'images/uploads/products/_01.png'
    UNION ALL SELECT 'Edifice Chronograph Working Golden Black Dial', @edifice_desc, 'For Men', 1499.00, 20, 'images/uploads/products/_01.png'
    UNION ALL SELECT 'Edifice Chronograph Working Golden White Dial', @edifice_desc, 'For Men', 1499.00, 20, 'images/uploads/products/_01.png'
    UNION ALL SELECT 'Edifice Chronograph Working Rose Gold Silver White Dial', @edifice_desc, 'For Men', 1499.00, 20, 'images/uploads/products/_01.png'
    UNION ALL SELECT 'Edifice Chronograph Working Leather Strap', @edifice_desc, 'For Men', 1199.00, 20, 'images/uploads/products/_01.png'
    UNION ALL SELECT 'Edifice Chronograph Working Black', @edifice_desc, 'For Men', 1499.00, 20, 'images/uploads/products/_01.png'
    UNION ALL SELECT 'Fosssil Metal Chronograph Working', @mens_chrono_desc, 'For Men', 1299.00, 20, 'images/uploads/products/_01.png'
    UNION ALL SELECT 'Fosssil Leather Chronograph Working', @mens_chrono_desc, 'For Men', 1199.00, 20, 'images/uploads/products/_01.png'
    UNION ALL SELECT 'Fosssil Black Leather Slim Watch Chronograph Working', @mens_chrono_desc, 'For Men', 1199.00, 20, 'images/uploads/products/_01.png'
    UNION ALL SELECT 'Patek P Silver Black', @mens_desc, 'For Men', 1199.00, 20, 'images/uploads/products/_01.png'
    UNION ALL SELECT 'Patek P Black', @mens_desc, 'For Men', 1199.00, 20, 'images/uploads/products/_01.png'
    UNION ALL SELECT 'Patek P Golden Black', @mens_desc, 'For Men', 1199.00, 20, 'images/uploads/products/_01.png'
    UNION ALL SELECT 'Patek P Golden White', @mens_desc, 'For Men', 1199.00, 20, 'images/uploads/products/_01.png'
    UNION ALL SELECT 'Patek P Golden Green', @mens_desc, 'For Men', 1199.00, 20, 'images/uploads/products/_01.png'
    UNION ALL SELECT 'Tissot Slim Leather Chronograph Working Black Strap, Silver Gold Case, White Dial', @mens_chrono_desc, 'For Men', 1499.00, 20, 'images/uploads/products/_01.png'
    UNION ALL SELECT 'Tissot Slim Leather Chronograph Working Black Strap, Silver Gold Case, Black Dial', @mens_chrono_desc, 'For Men', 1499.00, 20, 'images/uploads/products/_01.png'
    UNION ALL SELECT 'Tissot Slim Leather Chronograph Working Black Strap, Silver Case, Black Dial', @mens_chrono_desc, 'For Men', 1499.00, 20, 'images/uploads/products/_01.png'
    UNION ALL SELECT 'Tissot Slim Leather Chronograph Working Brown Strap, Silver Rose Gold Case, Black Dial', @mens_chrono_desc, 'For Men', 1499.00, 20, 'images/uploads/products/_01.png'
    UNION ALL SELECT 'Tissot Slim Leather Chronograph Working Brown Strap, Silver Rose Gold Case, White Dial', @mens_chrono_desc, 'For Men', 1499.00, 20, 'images/uploads/products/_01.png'
    UNION ALL SELECT 'Tissot Metal Regular Chronograph Working Silver Black Dial', @mens_chrono_desc, 'For Men', 1499.00, 20, 'images/uploads/products/_01.png'
    UNION ALL SELECT 'Tissot Metal Regular Chronograph Working Silver White dial', @mens_chrono_desc, 'For Men', 1499.00, 20, 'images/uploads/products/_01.png'
    UNION ALL SELECT 'Tag Cr7 Metal Black Chronograph Working', @mens_chrono_desc, 'For Men', 1799.00, 20, 'images/uploads/products/_01.png'

    UNION ALL SELECT 'Casio AE 1200 leather Brown', @gshock_desc, 'G-Shock', 999.00, 25, 'images/uploads/products/_03.png'
    UNION ALL SELECT 'Casio AE 1200 leather Black Silver', @gshock_desc, 'G-Shock', 999.00, 25, 'images/uploads/products/_03.png'
    UNION ALL SELECT 'Casio AE 1200 leather Black', @gshock_desc, 'G-Shock', 999.00, 25, 'images/uploads/products/_03.png'
    UNION ALL SELECT 'Casio AE 1200 leather Black Copper', @gshock_desc, 'G-Shock', 999.00, 25, 'images/uploads/products/_03.png'
    UNION ALL SELECT 'G - SHOCK BBGM - 2100 Silver Metal Body', @gshock_desc, 'G-Shock', 1399.00, 25, 'images/uploads/products/_03.png'
    UNION ALL SELECT 'G - SHOCK BBGM - 2100 Full Black Metal Body', @gshock_desc, 'G-Shock', 1399.00, 25, 'images/uploads/products/_03.png'
    UNION ALL SELECT 'G - SHOCK BBGBM - 2100 Silver Tiffany Blue Metal Body', @gshock_desc, 'G-Shock', 1399.00, 25, 'images/uploads/products/_03.png'
    UNION ALL SELECT 'G - SHOCK BBGBM-2100 Silver Green Metal Body', @gshock_desc, 'G-Shock', 1399.00, 25, 'images/uploads/products/_03.png'
    UNION ALL SELECT 'G-SHOCK BBGBM-2100 Silver Black Metal Body', @gshock_desc, 'G-Shock', 1399.00, 25, 'images/uploads/products/_03.png'
    UNION ALL SELECT 'G-SHOCK BBGM-2100 Blue Metal Body', @gshock_desc, 'G-Shock', 1399.00, 25, 'images/uploads/products/_03.png'
    UNION ALL SELECT 'G-SHOCK BBGM-2100 Rose gold Metal Body', @gshock_desc, 'G-Shock', 1399.00, 25, 'images/uploads/products/_03.png'
    UNION ALL SELECT 'G-SHOCK BBGM-2100 gold Metal Body', @gshock_desc, 'G-Shock', 1399.00, 25, 'images/uploads/products/_03.png'
    UNION ALL SELECT 'G-SHOCK BBGM-2100 Black Green Metal Body', @gshock_desc, 'G-Shock', 1399.00, 25, 'images/uploads/products/_03.png'
    UNION ALL SELECT 'G-SHOCK BBGA-2100 Black Fiber Body', @gshock_desc, 'G-Shock', 1299.00, 25, 'images/uploads/products/_03.png'
    UNION ALL SELECT 'G-SHOCK BBGA-2100 Black White Fiber Body', @gshock_desc, 'G-Shock', 1299.00, 25, 'images/uploads/products/_03.png'
    UNION ALL SELECT 'G-SHOCK BBGA- 110 Black Fiber Body', @gshock_desc, 'G-Shock', 1299.00, 25, 'images/uploads/products/_03.png'
    UNION ALL SELECT 'G-SHOCK BBGA- 110 Black gold Fiber Body', @gshock_desc, 'G-Shock', 1299.00, 25, 'images/uploads/products/_03.png'
    UNION ALL SELECT 'G-SHOCK BBGM- 110 Silver Metal Body', @gshock_desc, 'G-Shock', 1399.00, 25, 'images/uploads/products/_03.png'
    UNION ALL SELECT 'G-SHOCK BBGM- 110 Black Metal Body', @gshock_desc, 'G-Shock', 1399.00, 25, 'images/uploads/products/_03.png'
    UNION ALL SELECT 'G-SHOCK BBGM- 110 Gold Metal Body', @gshock_desc, 'G-Shock', 1399.00, 25, 'images/uploads/products/_03.png'
    UNION ALL SELECT 'G-SHOCK BBGM- 5600 Silver Metal Body', @gshock_desc, 'G-Shock', 999.00, 25, 'images/uploads/products/_03.png'
    UNION ALL SELECT 'G-SHOCK BBGM- 5600 Black Metal Body', @gshock_desc, 'G-Shock', 999.00, 25, 'images/uploads/products/_03.png'
    UNION ALL SELECT 'G-SHOCK BBGM- 5000 Gold Full Metal', @gshock_desc, 'G-Shock', 1199.00, 25, 'images/uploads/products/_03.png'
    UNION ALL SELECT 'G-SHOCK BBGM- 5000 Silver Full Metal', @gshock_desc, 'G-Shock', 1199.00, 25, 'images/uploads/products/_03.png'
    UNION ALL SELECT 'G-SHOCK BBGM- 5000 Black Full Metal', @gshock_desc, 'G-Shock', 1199.00, 25, 'images/uploads/products/_03.png'
    UNION ALL SELECT 'G-SHOCK BBGM- 2100b Tiffany Blue Silver Full Metal', @gshock_desc, 'G-Shock', 1999.00, 25, 'images/uploads/products/_03.png'
    UNION ALL SELECT 'G-SHOCK BBGM- 2100b Silver Black Full Metal', @gshock_desc, 'G-Shock', 1999.00, 25, 'images/uploads/products/_03.png'
) AS seed
INNER JOIN categories c ON c.name = seed.category_name
LEFT JOIN products p ON p.name = seed.name
WHERE p.id IS NULL
ORDER BY RAND();

INSERT INTO product_images (product_id, image_url, sort_order)
SELECT p.id, p.image, 0
FROM products p
WHERE NOT EXISTS (
  SELECT 1 FROM product_images pi WHERE pi.product_id = p.id
);

INSERT INTO box_options (name, image, price, is_active) VALUES
('Cartier Box', 'images/uploads/boxes/9228632-ef58233904f2.png', 350.00, 1),
('G-Shock Kit Box', 'images/uploads/boxes/9228632-ef58233904f2.png', 400.00, 1),
('Fossil Box', 'images/uploads/boxes/9228632-ef58233904f2.png', 375.00, 1),
('Hublot Box', 'images/uploads/boxes/9228632-ef58233904f2.png', 650.00, 1),
('Michael Kors Box', 'images/uploads/boxes/9228632-ef58233904f2.png', 450.00, 1),
('Patek Philippe Box', 'images/uploads/boxes/9228632-ef58233904f2.png', 700.00, 1),
('Rado Box', 'images/uploads/boxes/9228632-ef58233904f2.png', 425.00, 1),
('Rolex Box', 'images/uploads/boxes/9228632-ef58233904f2.png', 800.00, 1),
('TAG Heuer Box', 'images/uploads/boxes/9228632-ef58233904f2.png', 500.00, 1),
('Tissot Box', 'images/uploads/boxes/9228632-ef58233904f2.png', 390.00, 1),
('Tommy Hilfiger Box', 'images/uploads/boxes/9228632-ef58233904f2.png', 320.00, 1)
ON DUPLICATE KEY UPDATE
  image = VALUES(image),
  price = VALUES(price),
  is_active = VALUES(is_active);

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
VALUES
(
  'image',
  'images/uploads/carousel/_01.jpg',
  'Watch Ecommerce',
  'Browse premium watches with cart, reviews, wishlist, checkout, and admin order tracking.',
  'Shop Now',
  'shop.php'
),
(
  'video',
  'images/uploads/carousel/_01.mp4',
  'Watch Ecommerce',
  'Browse premium watches with cart, reviews, wishlist, checkout, and admin order management.',
  'Shop Now',
  'shop.php'
);

INSERT INTO userReview (file_path)
VALUES
  ("images/uploads/reviews/_01.png"),
  ("images/uploads/reviews/_01.png"),
  ("images/uploads/reviews/_01.png"),
  ("images/uploads/reviews/_01.png"),
  ("images/uploads/reviews/_01.png");

INSERT INTO featuredProductsVideo (file_path)
VALUES
  ("images/uploads/ugcs/_01.mp4"),
  ("images/uploads/ugcs/_01.mp4"),
  ("images/uploads/ugcs/_01.mp4"),
  ("images/uploads/ugcs/_01.mp4"),
  ("images/uploads/ugcs/_01.mp4");

