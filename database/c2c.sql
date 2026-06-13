-- ============================================================
-- C2C E-Commerce Platform — Full Database Schema
-- Database: c2c_ecommerce
-- Updated to match new frontend (app.js product list)
-- ============================================================

CREATE DATABASE IF NOT EXISTS `c2c_ecommerce`;
USE `c2c_ecommerce`;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

-- Drop tables in dependency order
DROP TABLE IF EXISTS `pending_listing`;
DROP TABLE IF EXISTS `transaction`;
DROP TABLE IF EXISTS `cart`;
DROP TABLE IF EXISTS `wishlist`;
DROP TABLE IF EXISTS `product`;
DROP TABLE IF EXISTS `user`;

-- ============================================================
-- TABLE: user
-- ============================================================
CREATE TABLE `user` (
  `user_id`       INT(11)      NOT NULL AUTO_INCREMENT,
  `first_name`    VARCHAR(100) NOT NULL,
  `last_name`     VARCHAR(100) NOT NULL,
  `email`         VARCHAR(255) NOT NULL DEFAULT '',
  `username`      VARCHAR(100) NOT NULL DEFAULT '',
  `password_hash` VARCHAR(255) NOT NULL DEFAULT '',
  `role`          ENUM('buyer','seller','admin','moderator') NOT NULL DEFAULT 'buyer',
  `status`        ENUM('active','inactive','banned','pending','suspended') NOT NULL DEFAULT 'active',
  `register_date` DATETIME     DEFAULT NULL,
  `last_active`   DATETIME     DEFAULT NULL,
  `wallet_balance` DOUBLE(10,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `user`
  (`user_id`, `first_name`, `last_name`, `email`, `username`, `password_hash`, `role`, `status`, `register_date`, `last_active`, `wallet_balance`)
VALUES
  (1, 'Admin', 'User', 'mapondaglodi@gmail.com', 'admin_glodi', '$2y$10$fgJUVarub.BzPGX5RMbGyOyiY51aNAhqxrAbm3HAWseyJviseuaXS', 'admin', 'active', NOW(), NOW(), 0.00),
  (2, 'Seller', 'Bob', 'spongebobgokugta@gmail.com', 'seller_bob', '$2y$10$fgJUVarub.BzPGX5RMbGyOyiY51aNAhqxrAbm3HAWseyJviseuaXS', 'seller', 'active', NOW(), NOW(), 0.00),
  (3, 'Buyer', 'Orange', 'upbeat482orange@gmail.com', 'buyer_orange', '$2y$10$fgJUVarub.BzPGX5RMbGyOyiY51aNAhqxrAbm3HAWseyJviseuaXS', 'buyer', 'active', NOW(), NOW(), 0.00);

-- ============================================================
-- TABLE: product
-- ============================================================
CREATE TABLE `product` (
  `item_id`             INT(11)      NOT NULL AUTO_INCREMENT,
  `item_brand`          VARCHAR(200) NOT NULL,
  `item_name`           VARCHAR(255) NOT NULL,
  `item_price`          DOUBLE(10,2) NOT NULL,
  `item_original_price` DOUBLE(10,2) DEFAULT NULL,
  `item_image`          VARCHAR(255) NOT NULL,
  `item_details`        VARCHAR(255) NOT NULL DEFAULT '',
  `item_category`       VARCHAR(50)  NOT NULL DEFAULT 'phones',
  `item_is_new`         TINYINT(1)   NOT NULL DEFAULT 0,
  `item_is_sale`        TINYINT(1)   NOT NULL DEFAULT 0,
  `item_in_stock`       TINYINT(1)   NOT NULL DEFAULT 1,
  `item_quantity`       INT(11)      NOT NULL DEFAULT 1,
  `item_status`         ENUM('active','sold','hidden') NOT NULL DEFAULT 'active',
  `seller_id`           INT(11)      DEFAULT NULL,
  `item_register`       DATETIME     DEFAULT NULL,
  `item_description`    TEXT         DEFAULT NULL,
  PRIMARY KEY (`item_id`),
  KEY `seller_id` (`seller_id`),
  CONSTRAINT `product_ibfk_1` FOREIGN KEY (`seller_id`) REFERENCES `user` (`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `product`
  (`item_id`, `item_brand`, `item_name`, `item_price`, `item_original_price`, `item_image`, `item_details`, `item_category`, `item_is_new`, `item_is_sale`, `item_in_stock`, `item_quantity`, `item_status`, `seller_id`, `item_register`, `item_description`)
VALUES
-- ---- Phones -------------------------------------------------
(1,  'Samsung', 'Samsung Galaxy A72', 
     4599.99,  6599.99, 
     './assets/phones/samsung-galaxy-a72-4g.jpg',
     '128GB • Snapdragon 720G • 4G',
     'phones', 0, 1, 1, 1, 'active', 2, '2025-01-15 10:00:00',
     'The Samsung Galaxy A72 combines a sleek design with a powerful camera system and a long-lasting battery. Featuring a vibrant Super AMOLED display and IP67 water resistance, it is perfect for everyday use.'),

(2,  'Samsung', 'Samsung Galaxy Note 20',
     6999.99,  11999.99,
     './assets/phones/SamsungGalaxyNote20.jpg',
     '256GB • Exynos 990 • 5G',
     'phones', 0, 1, 1, 1, 'active', 3, '2024-11-20 08:30:00',
     'Experience the power of a PC in your pocket with the Galaxy Note 20. Comes with the iconic S Pen for seamless note-taking and creativity on a stunning Infinity-O Display.'),

(3,  'Apple',   'Apple iPhone X Space Grey',
     4999.99,  8999.99,
     './assets/phones/iphone-x-spacegrey.jpg',
     '256GB • A11 Bionic • Unlocked',
     'phones', 0, 1, 1, 1, 'active', 2, '2024-06-10 14:00:00',
     'The revolutionary iPhone X features an all-screen edge-to-edge design, Face ID, and dual 12MP cameras with Portrait Lighting. A true modern classic.'),

-- ---- PC Parts -----------------------------------------------
(6,  'Intel',  'Intel Core i7',
     8999.99,  NULL,
     './assets/computerParts/IntelCorei7.png',
     'Processor • 14700K • 20 Cores',
     'pc parts', 1, 0, 1, 1, 'active', 2, '2024-12-01 00:00:00',
     'The Intel Core i7-14700K delivers exceptional multi-threaded performance with 20 cores. Ideal for gaming, content creation, and demanding workloads.'),

(7,  'ZOTAC',  'ZOTAC RTX 5070 AMP White',
     13999.99, NULL,
     './assets/computerParts/ZOTAC GAMING GeForce RTX 5070 AMP White Edition.jpg',
     'Graphics Card • 12GB GDDR6X • PCIe 4.0',
     'pc parts', 1, 0, 1, 1, 'active', 2, '2024-12-10 00:00:00',
     'The ZOTAC GAMING GeForce RTX 5070 AMP White Edition brings stunning next-gen performance with 12GB of GDDR6X memory and PCIe 4.0 support in a sleek white design.'),

(8,  'MEK',    'MEK 5080 Gaming PC',
     49999.99, NULL,
     './assets/computerParts/MEK 5080 Gaming PC_PreBuild.jpg',
     'PreBuild • RTX 5080 • 32GB RAM',
     'pc parts', 1, 0, 1, 1, 'active', 3, '2024-12-20 00:00:00',
     'The ultimate gaming PC. The MEK 5080 comes pre-built with an RTX 5080, 32GB DDR5 RAM, and a 2TB NVMe SSD, ready to play right out of the box.'),

(9,  'ZOTAC',  'ZOTAC GAMING GeForce RTX 8GB',
     5499.99,  7999.99,
     './assets/computerParts/ZOTAC_GamingGeForceRTX8GB.jpg',
     'Graphics Card • 8GB GDDR6 • PCIe 4.0',
     'pc parts', 0, 1, 1, 1, 'active', 2, '2025-03-01 00:00:00',
     'An excellent mid-range graphics card offering great 1440p gaming performance. The ZOTAC GAMING GeForce RTX with 8GB GDDR6 is now available at a discounted price.'),

-- ---- Peripherals --------------------------------------------
(10, 'Apple',   'Airpods 4',
     3499.99,  4499.99,
     './assets/airpod4.jpg',
     'Wireless • ANC • H2 Chip',
     'peripherals', 0, 1, 1, 1, 'active', 3, '2025-02-20 00:00:00',
     'AirPods 4 with Active Noise Cancellation. Experience sound tailored to you with Adaptive Audio, Conversation Awareness, and a personalized spatial audio experience powered by the H2 chip.'),

(11, 'TeckNet', 'TeckNet Wireless Keyboard',
     599.99,   NULL,
     './assets/peripherals/TeckNetWireless Keyboard.webp',
     'Wireless • Ergonomic • Black',
     'peripherals', 1, 0, 1, 1, 'active', 2, '2024-10-05 00:00:00',
     'A slim, ergonomic wireless keyboard designed for all-day comfort. Features whisper-quiet keys and a reliable 2.4GHz wireless connection.'),

(12, 'JBL',     'JBL Tune 720BT',
     1199.99,  NULL,
     './assets/peripherals/wirelessJBL.jpg',
     'Over-ear • Wireless • ANC',
     'peripherals', 1, 0, 1, 1, 'active', 3, '2025-01-10 00:00:00',
     'JBL over-ear wireless headphones with Active Noise Cancellation. Enjoy powerful JBL Pro Sound, 30-hour battery life, and a foldable design for on-the-go convenience.'),

-- ---- Accessories --------------------------------------------
(13, 'Generic', 'SuperPuff iPhone 13 Case',
     299.99,   NULL,
     './assets/covers/SuperPuffiPhone13.jpg',
     'Silicone • Puff Design • Red',
     'accessories', 1, 0, 1, 1, 'active', 2, '2024-09-10 00:00:00',
     'A stylish and protective puff silicone case for the iPhone 13. Provides excellent drop protection while maintaining a slim, fashionable profile.'),

(14, 'Generic', 'Generic Clear Cover',
     150.00,   NULL,
     './assets/covers/cover.jpg',
     'Clear • Shockproof',
     'accessories', 1, 0, 1, 1, 'active', 3, '2024-08-15 00:00:00',
     'A simple, crystal-clear shockproof case compatible with multiple phone models. Lightweight and thin, it shows off your phone design while protecting it.'),

(15, 'Generic', 'Large Desk Mat',
     499.99,   650.00,
     './assets/covers/deskMat.jpg',
     'Leather • Large • Waterproof',
     'accessories', 0, 1, 1, 1, 'active', 2, '2024-10-25 00:00:00',
     'A premium large leather desk mat to elevate your workspace. Waterproof surface, non-slip base, and a generous size to accommodate your keyboard, mouse, and more.'),

(16, 'Generic', 'iPhone 13 Leather Cover',
     399.99,   NULL,
     './assets/covers/iPhone13Cover.jpg',
     'Leather • MagSafe • Brown',
     'accessories', 1, 0, 1, 1, 'active', 3, '2024-11-05 00:00:00',
     'Crafted from genuine leather, this MagSafe-compatible case for the iPhone 13 develops a natural patina over time, making it uniquely yours.'),

(17, 'Generic', 'Samsung Galaxy S25 Case',
     349.99,   NULL,
     './assets/covers/samsungGalaxyS25.jpg',
     'Rugged • Stand • Black',
     'accessories', 1, 0, 1, 1, 'active', 2, '2024-12-15 00:00:00',
     'A rugged protective case for the Samsung Galaxy S25 featuring a built-in kickstand. Drop-tested and ready for anything.');

-- ============================================================
-- TABLE: transaction
-- ============================================================
CREATE TABLE `transaction` (
  `transaction_id`   INT(11) NOT NULL AUTO_INCREMENT,
  `buyer_id`         INT(11) NOT NULL,
  `seller_id`        INT(11) NOT NULL,
  `item_id`          INT(11) NOT NULL,
  `amount`           DOUBLE(10,2) NOT NULL,
  `transaction_date` DATETIME DEFAULT NULL,
  `order_status`     ENUM('received', 'shipped', 'delivered', 'return_pending_seller', 'return_denied_damaged', 'return_denied_seller', 'return_escalated_admin', 'return_refunded', 'return_denied_admin') NOT NULL DEFAULT 'received',
  `return_reason`    TEXT DEFAULT NULL,
  `return_condition` VARCHAR(50) DEFAULT NULL,
  `updated_at`       DATETIME DEFAULT NULL,
  PRIMARY KEY (`transaction_id`),
  KEY `buyer_id` (`buyer_id`),
  KEY `seller_id` (`seller_id`),
  KEY `item_id` (`item_id`),
  CONSTRAINT `trans_ibfk_1` FOREIGN KEY (`buyer_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `trans_ibfk_2` FOREIGN KEY (`seller_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `trans_ibfk_3` FOREIGN KEY (`item_id`) REFERENCES `product` (`item_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE: cart
-- ============================================================
CREATE TABLE `cart` (
  `cart_id`  INT(11) NOT NULL AUTO_INCREMENT,
  `user_id`  INT(11) NOT NULL,
  `item_id`  INT(11) NOT NULL,
  `quantity` INT(11) NOT NULL DEFAULT 1,
  `added_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`cart_id`),
  KEY `user_id` (`user_id`),
  KEY `item_id` (`item_id`),
  CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `product` (`item_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE: wishlist
-- ============================================================
CREATE TABLE `wishlist` (
  `wishlist_id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id`     INT(11) NOT NULL,
  `item_id`     INT(11) NOT NULL,
  `added_at`    DATETIME DEFAULT NULL,
  PRIMARY KEY (`wishlist_id`),
  KEY `user_id` (`user_id`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE: pending_listing
-- ============================================================
CREATE TABLE `pending_listing` (
  `listing_id`       INT(11) NOT NULL AUTO_INCREMENT,
  `seller_id`        INT(11) NOT NULL,
  `item_name`        VARCHAR(255) NOT NULL,
  `item_price`       DOUBLE(10,2) NOT NULL,
  `item_category`    VARCHAR(50) NOT NULL,
  `item_quantity`    INT(11) NOT NULL DEFAULT 1,
  `item_description` TEXT,
  `item_image`       VARCHAR(255) NOT NULL,
  `kyc_status`       VARCHAR(50) NOT NULL,
  `kyc_match_score`  INT(11) DEFAULT NULL,
  `raw_kyc_data`     JSON DEFAULT NULL,
  `created_at`       DATETIME DEFAULT NULL,
  PRIMARY KEY (`listing_id`),
  KEY `seller_id` (`seller_id`),
  CONSTRAINT `pending_listing_ibfk_1` FOREIGN KEY (`seller_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

COMMIT;