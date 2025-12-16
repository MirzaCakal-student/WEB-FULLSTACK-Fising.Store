-- Clean schema & data for Fishing Planet project
-- Database: fishingplanet
 -- finished
CREATE DATABASE IF NOT EXISTS `fishingplanet`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `fishingplanet`;

-- =========================
-- USERS
-- =========================
DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(80) NOT NULL,
  `email` varchar(160) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('user','admin') NOT NULL DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=7
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `users` WRITE;
INSERT INTO `users` VALUES
  (1,'admin','admin@fp.com','admin','admin','2025-11-03 02:22:33'),
  (2,'mirza','mirza@example.com','$2y$10$abcdefghijklmnopqrstuv','user','2025-11-03 02:22:33'),
  (3,'ajla','ajla@example.com','$2y$10$abcdefghijklmnopqrstuv','user','2025-11-03 02:22:33'),
  (4,'edna','edna@example.com','$2y$10$abcdefghijklmnopqrstuv','user','2025-11-03 02:22:33'),
  (5,'kenan','kenan@example.com','$2y$10$abcdefghijklmnopqrstuv','user','2025-11-03 02:22:33'),
  (6,'amina','amina@example.com','$2y$10$abcdefghijklmnopqrstuv','user','2025-11-03 02:22:33');
UNLOCK TABLES;

-- =========================
-- PRODUCTS
-- =========================

DROP TABLE IF EXISTS `products`;

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(160) NOT NULL,
  `category` varchar(80) NOT NULL,
  `price` decimal(10,2) NOT NULL CHECK (`price` >= 0),
  `stock_quantity` int(11) NOT NULL DEFAULT 0 CHECK (`stock_quantity` >= 0),
  `image_url` varchar(300) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `products` WRITE;

INSERT INTO `products` (`product_id`, `name`, `category`, `price`, `stock_quantity`, `image_url`, `created_at`) VALUES
  (1,'Carbon Fiber Rod','Rods',89.99,30,'/public/uploads/products/rod1.jpg','2025-11-03 02:22:47'),
  (2,'Spinning Reel Pro','Reels',129.90,25,'/public/uploads/products/reel1.jpg','2025-11-03 02:22:47'),
  (3,'Braided Line 300m','Lines',24.50,100,'/public/uploads/products/line1.jpg','2025-11-03 02:22:47'),
  (4,'Worm Bait Set','Baits',19.99,200,'/public/uploads/products/bait1.jpg','2025-11-03 02:22:47'),
  (5,'Tackle Box XL','Accessories',34.90,60,'/public/uploads/products/box1.jpg','2025-11-03 02:22:47'),
  (6,'Fly Rod #5','Rods',159.00,15,'/public/uploads/products/rod2.jpg','2025-11-03 02:22:47'),
  (7,'Casting Reel 300','Reels',179.00,12,'/public/uploads/products/reel2.jpg','2025-11-03 02:22:47'),
  (8,'Polarized Sunglasses','Accessories',29.00,80,'/public/uploads/products/glasses1.jpg','2025-11-03 02:22:47');

UNLOCK TABLES;

-- =========================
-- ORDERS
-- =========================
DROP TABLE IF EXISTS `orders`;

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `payment_status` enum('unpaid','paid','refunded','failed') NOT NULL DEFAULT 'unpaid',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`order_id`),
  KEY `fk_orders_user` (`user_id`),
  CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`)
    REFERENCES `users` (`user_id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `orders` WRITE;
INSERT INTO `orders` VALUES
  (1,2,198.96,'paid','2025-11-03 02:23:18'),
  (2,3,164.80,'paid','2025-11-03 02:23:18');
UNLOCK TABLES;

-- =========================
-- PAYMENTS
-- =========================
DROP TABLE IF EXISTS `payments`;

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `payment_method` enum('card','paypal','cod','bank') NOT NULL,
  `amount` decimal(10,2) NOT NULL CHECK (`amount` >= 0),
  `status` enum('initiated','succeeded','failed','refunded') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`payment_id`),
  KEY `fk_pay_order` (`order_id`),
  CONSTRAINT `fk_pay_order` FOREIGN KEY (`order_id`)
    REFERENCES `orders` (`order_id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `payments` WRITE;
INSERT INTO `payments` VALUES
  (1,1,'card',198.96,'succeeded','2025-11-03 02:23:34'),
  (2,2,'paypal',164.80,'succeeded','2025-11-03 02:23:34');
UNLOCK TABLES;

-- =========================
-- CART ITEMS
-- =========================
DROP TABLE IF EXISTS `cart_items`;

CREATE TABLE `cart_items` (
  `cart_item_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL CHECK (`quantity` > 0),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`cart_item_id`),
  UNIQUE KEY `uq_cart_user_product` (`user_id`,`product_id`),
  KEY `fk_cart_product` (`product_id`),
  CONSTRAINT `fk_cart_product` FOREIGN KEY (`product_id`)
    REFERENCES `products` (`product_id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_cart_user` FOREIGN KEY (`user_id`)
    REFERENCES `users` (`user_id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `cart_items` WRITE;
INSERT INTO `cart_items` VALUES
  (1,2,1,1,'2025-11-03 02:23:11'),
  (2,2,3,2,'2025-11-03 02:23:11'),
  (3,2,4,3,'2025-11-03 02:23:11'),
  (4,3,2,1,'2025-11-03 02:23:11'),
  (5,3,5,1,'2025-11-03 02:23:11'),
  (6,4,8,2,'2025-11-03 02:23:11');
UNLOCK TABLES;

-- =========================
-- INVENTORY
-- =========================
DROP TABLE IF EXISTS `inventory`;

CREATE TABLE `inventory` (
  `inventory_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `change_type` enum('IN','OUT') NOT NULL,
  `quantity_change` int(11) NOT NULL,
  `note` varchar(200) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`inventory_id`),
  KEY `fk_inventory_product` (`product_id`),
  CONSTRAINT `fk_inventory_product` FOREIGN KEY (`product_id`)
    REFERENCES `products` (`product_id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `inventory` WRITE;
INSERT INTO `inventory` VALUES
  (1,1,'IN',30,'initial stock','2025-11-03 02:22:56'),
  (2,2,'IN',25,'initial stock','2025-11-03 02:22:56'),
  (3,3,'IN',100,'initial stock','2025-11-03 02:22:56'),
  (4,4,'IN',200,'initial stock','2025-11-03 02:22:56'),
  (5,5,'IN',60,'initial stock','2025-11-03 02:22:56'),
  (6,6,'IN',15,'initial stock','2025-11-03 02:22:56'),
  (7,7,'IN',12,'initial stock','2025-11-03 02:22:56'),
  (8,8,'IN',80,'initial stock','2025-11-03 02:22:56'),
  (9,3,'OUT',10,'promotion pack build','2025-11-03 02:22:56'),
  (10,4,'OUT',5,'damaged','2025-11-03 02:22:56');
UNLOCK TABLES;

-- =========================
-- ORDER ITEMS
-- =========================
DROP TABLE IF EXISTS `order_items`;

CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL CHECK (`quantity` > 0),
  `price` decimal(10,2) NOT NULL CHECK (`price` >= 0),
  PRIMARY KEY (`order_item_id`),
  UNIQUE KEY `uq_order_product` (`order_id`,`product_id`),
  KEY `fk_oit_product` (`product_id`),
  CONSTRAINT `fk_oit_order` FOREIGN KEY (`order_id`)
    REFERENCES `orders` (`order_id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_oit_product` FOREIGN KEY (`product_id`)
    REFERENCES `products` (`product_id`)
    ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `order_items` WRITE;
INSERT INTO `order_items` VALUES
  (1,1,1,1,89.99),
  (2,1,3,2,24.50),
  (3,1,4,3,19.99),
  (4,2,2,1,129.90),
  (5,2,5,1,34.90);
UNLOCK TABLES;

-- =========================
-- WISHLIST ITEMS
-- =========================
DROP TABLE IF EXISTS `wishlist_items`;

CREATE TABLE `wishlist_items` (
  `wishlist_item_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`wishlist_item_id`),
  UNIQUE KEY `uq_wishlist_user_product` (`user_id`,`product_id`),
  KEY `fk_wish_product` (`product_id`),
  CONSTRAINT `fk_wish_product` FOREIGN KEY (`product_id`)
    REFERENCES `products` (`product_id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_wish_user` FOREIGN KEY (`user_id`)
    REFERENCES `users` (`user_id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `wishlist_items` WRITE;
INSERT INTO `wishlist_items` VALUES
  (1,2,7,'2025-11-03 02:23:05'),
  (2,2,6,'2025-11-03 02:23:05'),
  (3,3,1,'2025-11-03 02:23:05'),
  (4,3,5,'2025-11-03 02:23:05'),
  (5,4,2,'2025-11-03 02:23:05'),
  (6,5,8,'2025-11-03 02:23:05');
UNLOCK TABLES;

-- =========================
-- ADDRESSES
-- =========================
DROP TABLE IF EXISTS `addresses`;

CREATE TABLE `addresses` (
  `address_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `full_name` varchar(120) NOT NULL,
  `street` varchar(160) NOT NULL,
  `city` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`address_id`),
  KEY `fk_addresses_user` (`user_id`),
  CONSTRAINT `fk_addresses_user` FOREIGN KEY (`user_id`)
    REFERENCES `users` (`user_id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `addresses` WRITE;
INSERT INTO `addresses` VALUES
  (1,2,'Mirza Čakal','Zmaja od Bosne 12','Sarajevo','2025-11-03 02:22:39'),
  (2,3,'Ajla Bektić','Titova 3','Sarajevo','2025-11-03 02:22:39'),
  (3,4,'Edna Sej','Kulina bana 9','Mostar','2025-11-03 02:22:39'),
  (4,5,'Kenan H','Maršala Tita 44','Tuzla','2025-11-03 02:22:39'),
  (5,6,'Amina S','Šetalište 7','Banja Luka','2025-11-03 02:22:39');
UNLOCK TABLES;

-- =========================
-- VIEW: v_cart_totals
-- =========================
DROP VIEW IF EXISTS `v_cart_totals`;

CREATE VIEW `v_cart_totals` AS
SELECT
  ci.user_id AS user_id,
  SUM(p.price * ci.quantity) AS total
FROM cart_items ci
JOIN products p ON p.product_id = ci.product_id
GROUP BY ci.user_id;
