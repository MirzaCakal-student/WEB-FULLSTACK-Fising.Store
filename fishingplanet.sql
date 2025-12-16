-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 16, 2025 at 07:36 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `fishingplanet`
--

-- --------------------------------------------------------

--
-- Table structure for table `addresses`
--

CREATE TABLE `addresses` (
  `address_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `full_name` varchar(120) NOT NULL,
  `street` varchar(160) NOT NULL,
  `city` varchar(100) NOT NULL,
  `zip_code` varchar(20) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `addresses`
--

INSERT INTO `addresses` (`address_id`, `user_id`, `full_name`, `street`, `city`, `zip_code`, `country`, `phone`, `created_at`) VALUES
(6, 43, 'mikic cakal', '14', 'Ilijas', '1234', 'Bosnia and Herzegovina', '23123123123', '2025-12-15 03:57:25'),
(7, 43, 'mikic cakal', '14', 'Ilijas', '1234', 'Bosnia and Herzegovina', '23123123123', '2025-12-15 03:57:30'),
(8, 43, 'mikic cakal', '14', 'Ilijas', '1234', 'Bosnia and Herzegovina', '23123123123', '2025-12-15 03:57:44'),
(9, 43, 'mikic cakal', '14', 'Ilijas', '1234', 'Bosnia and Herzegovina', '23123123123', '2025-12-15 03:58:03'),
(10, 43, 'mikic cakal', '14', 'Ilijas', '1234', 'Bosnia and Herzegovina', '23123123123', '2025-12-15 03:58:07'),
(11, 43, 'mikic cakal', '14', 'Ilijas', '1234', 'Bosnia and Herzegovina', '23123123123', '2025-12-15 03:58:22'),
(12, 43, 'mikic cakal', '14', 'Ilijas', '1234', 'Bosnia and Herzegovina', '23123123123', '2025-12-15 03:59:05'),
(13, 43, 'mikic cakal', '14', 'Ilijas', '1234', 'Bosnia and Herzegovina', '23123123123', '2025-12-15 03:59:10'),
(14, 43, 'mikic cakal', '14', 'Ilijas', '1234', 'Bosnia and Herzegovina', '23123123123', '2025-12-15 03:59:37'),
(15, 43, 'mikic cakal', '14', 'Ilijas', '1234', 'Bosnia and Herzegovina', '23123123123', '2025-12-15 04:02:24'),
(16, 43, 'mikic cakal', '14', 'Ilijas', '1234', 'Bosnia and Herzegovina', '23123123123', '2025-12-15 04:03:22'),
(17, 43, 'mikic cakal', '14', 'Ilijas', '1234', 'Bosnia and Herzegovina', '23123123123', '2025-12-15 04:03:44');

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `cart_item_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart_items`
--

INSERT INTO `cart_items` (`cart_item_id`, `user_id`, `product_id`, `quantity`, `created_at`) VALUES
(2, 27, 13, 2, '2025-12-15 02:46:03'),
(11, 43, 15, 1, '2025-12-15 04:49:20'),
(12, 43, 9, 1, '2025-12-15 05:16:57'),
(13, 43, 11, 1, '2025-12-15 05:16:58');

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `inventory_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `change_type` enum('IN','OUT') NOT NULL,
  `quantity_change` int(11) NOT NULL,
  `note` varchar(200) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`inventory_id`, `product_id`, `change_type`, `quantity_change`, `note`, `created_at`) VALUES
(5, 5, 'IN', 60, 'initial stock', '2025-11-03 01:22:56'),
(12, 5, 'IN', 60, 'Initial stock - Tackle Box XL', '2025-12-15 02:44:31'),
(13, 9, 'IN', 30, 'Initial stock - UltraLight Rod', '2025-12-15 02:44:31'),
(14, 10, 'IN', 15, 'Initial stock - Heavy Duty Rod', '2025-12-15 02:44:31'),
(15, 11, 'IN', 20, 'Initial stock - Shimano Reel', '2025-12-15 02:44:31'),
(16, 12, 'IN', 18, 'Initial stock - Baitcasting Reel', '2025-12-15 02:44:31'),
(17, 13, 'IN', 50, 'Initial stock - Braided Line', '2025-12-15 02:44:31'),
(18, 14, 'IN', 60, 'Initial stock - Monofilament Line', '2025-12-15 02:44:31'),
(19, 15, 'IN', 100, 'Initial stock - Soft Plastic Lures', '2025-12-15 02:44:31'),
(20, 16, 'IN', 80, 'Initial stock - Crankbait Set', '2025-12-15 02:44:31');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `shipping_address_id` int(11) DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` varchar(50) DEFAULT 'pending',
  `payment_status` enum('unpaid','paid','refunded','failed') NOT NULL DEFAULT 'unpaid',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `shipping_address_id`, `total_amount`, `status`, `payment_status`, `created_at`) VALUES
(101, 27, NULL, 0.00, 'pending', 'paid', '2025-12-14 18:59:50'),
(106, 43, 9, 39.90, 'pending', 'unpaid', '2025-12-15 03:58:03'),
(107, 43, 10, 39.90, 'pending', 'unpaid', '2025-12-15 03:58:07'),
(108, 43, 11, 39.90, 'pending', 'unpaid', '2025-12-15 03:58:22'),
(109, 43, 12, 74.80, 'pending', 'unpaid', '2025-12-15 03:59:05'),
(110, 43, 13, 74.80, 'pending', 'unpaid', '2025-12-15 03:59:10'),
(111, 43, 14, 74.80, 'pending', 'unpaid', '2025-12-15 03:59:37'),
(112, 43, 15, 74.80, 'pending', 'unpaid', '2025-12-15 04:02:24'),
(113, 43, 16, 74.80, 'pending', 'unpaid', '2025-12-15 04:03:22'),
(114, 43, 17, 74.80, 'pending', 'unpaid', '2025-12-15 04:03:44');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL CHECK (`quantity` > 0),
  `price` decimal(10,2) NOT NULL CHECK (`price` >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`order_item_id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(6, 106, 5, 1, 34.90),
(7, 107, 5, 1, 34.90),
(8, 108, 5, 1, 34.90),
(9, 109, 5, 2, 34.90),
(10, 110, 5, 2, 34.90),
(11, 111, 5, 2, 34.90),
(12, 112, 5, 2, 34.90),
(13, 113, 5, 2, 34.90),
(14, 114, 5, 2, 34.90);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `payment_method` enum('card','paypal','cod','bank') NOT NULL,
  `amount` decimal(10,2) NOT NULL CHECK (`amount` >= 0),
  `status` enum('initiated','succeeded','failed','refunded') NOT NULL,
  `method` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `order_id`, `payment_method`, `amount`, `status`, `method`, `created_at`) VALUES
(1, 101, 'card', 59.99, 'succeeded', NULL, '2025-12-14 09:30:00'),
(6, 113, 'card', 74.80, '', 'paypal', '2025-12-15 04:03:22'),
(7, 114, 'card', 74.80, '', 'paypal', '2025-12-15 04:03:44');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `name` varchar(160) NOT NULL,
  `category` varchar(80) NOT NULL,
  `price` decimal(10,2) NOT NULL CHECK (`price` >= 0),
  `stock_quantity` int(11) NOT NULL DEFAULT 0 CHECK (`stock_quantity` >= 0),
  `image_url` varchar(300) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `name`, `category`, `price`, `stock_quantity`, `image_url`, `created_at`, `description`) VALUES
(5, 'Tackle Box XL', 'Accessories', 34.90, 60, 'box1.jpg', '2025-11-03 01:22:47', 'Extra large tackle box with 4 trays and multiple compartments. Waterproof design.'),
(9, 'UltraLight Spinning Rod', 'Rods', 89.99, 30, 'rod2.jpg', '2025-12-12 21:37:41', 'Lightweight spinning rod ideal for beginners and experienced anglers. 6.5 feet, fast action.'),
(10, 'Heavy Duty Casting Rod', 'Rods', 159.99, 15, 'rod3.jpg', '2025-12-12 21:37:41', 'Heavy duty casting rod for big game fishing. 8 feet, extra heavy action.'),
(11, 'Shimano Spinning Reel 4000', 'Reels', 199.99, 20, 'reel1.jpg', '2025-12-12 21:37:41', 'High-quality Shimano spinning reel with smooth drag system. 5.0:1 gear ratio.'),
(12, 'Baitcasting Reel Pro', 'Reels', 149.99, 18, 'reel2.jpg', '2025-12-12 21:37:41', 'Professional baitcasting reel with magnetic brake system. 7.1:1 gear ratio.'),
(13, 'Braided Fishing Line 300m', 'Lines', 34.99, 50, 'line1.jpg', '2025-12-12 21:37:41', 'Super strong braided fishing line, 300m spool. 20lb test strength.'),
(14, 'Monofilament Line 500m', 'Lines', 24.99, 60, 'line2.jpg', '2025-12-12 21:37:41', 'Premium monofilament fishing line, 500m spool. 15lb test, clear color.'),
(15, 'Soft Plastic Lure Pack', 'Baits', 19.99, 100, 'bait1.jpg', '2025-12-12 21:37:41', 'Soft plastic lures in various colors and sizes. Great for bass fishing.'),
(16, 'Crankbait Set (5pcs)', 'Baits', 29.99, 80, 'bait2.jpg', '2025-12-12 21:37:41', 'Set of 5 high-quality crankbaits. Different depths and colors for various conditions.'),
(17, 'Carbon Pro Fishing Rod 7ft', 'Rods', 12.00, 12, 'rod1.jpg', '2025-12-15 03:34:58', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(80) NOT NULL,
  `email` varchar(160) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `profile_picture` varchar(300) DEFAULT NULL,
  `role` enum('user','admin') NOT NULL DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password`, `password_hash`, `profile_picture`, `role`, `created_at`) VALUES
(27, 'mirza cakal', 'mirza@gmail.com', NULL, '$2y$10$jnMM9JOOlPHspOgtX7/i9.OBg/tKr1rjgULixIO2h2prwe46SGYYS', NULL, 'admin', '2025-12-13 12:21:12'),
(43, 'mikica cakal', 'mirza.cakal@stu.ibu.edu.ba', NULL, '$2y$10$XakmO8eOt9SELBJMV4hDGOXI/tKSeLePg2NrAtmjXqsSaPyBMW9DG', NULL, 'user', '2025-12-15 03:36:29');

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_cart_totals`
-- (See below for the actual view)
--
CREATE TABLE `v_cart_totals` (
`user_id` int(11)
,`total` decimal(42,2)
);

-- --------------------------------------------------------

--
-- Table structure for table `wishlist_items`
--

CREATE TABLE `wishlist_items` (
  `wishlist_item_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wishlist_items`
--

INSERT INTO `wishlist_items` (`wishlist_item_id`, `user_id`, `product_id`, `created_at`) VALUES
(1, 27, 10, '2025-12-15 02:46:27'),
(2, 27, 12, '2025-12-15 02:46:27'),
(6, 43, 14, '2025-12-15 04:49:22');

-- --------------------------------------------------------

--
-- Structure for view `v_cart_totals`
--
DROP TABLE IF EXISTS `v_cart_totals`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_cart_totals`  AS SELECT `ci`.`user_id` AS `user_id`, sum(`p`.`price` * `ci`.`quantity`) AS `total` FROM (`cart_items` `ci` join `products` `p` on(`p`.`product_id` = `ci`.`product_id`)) GROUP BY `ci`.`user_id` ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `addresses`
--
ALTER TABLE `addresses`
  ADD PRIMARY KEY (`address_id`),
  ADD KEY `fk_addresses_user` (`user_id`);

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`cart_item_id`),
  ADD UNIQUE KEY `unique_user_product` (`user_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`inventory_id`),
  ADD KEY `fk_inventory_product` (`product_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `fk_orders_user` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD UNIQUE KEY `uq_order_product` (`order_id`,`product_id`),
  ADD KEY `fk_oit_product` (`product_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `fk_pay_order` (`order_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `wishlist_items`
--
ALTER TABLE `wishlist_items`
  ADD PRIMARY KEY (`wishlist_item_id`),
  ADD UNIQUE KEY `unique_user_product_wishlist` (`user_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `addresses`
--
ALTER TABLE `addresses`
  MODIFY `address_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `cart_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `inventory_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=116;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `wishlist_items`
--
ALTER TABLE `wishlist_items`
  MODIFY `wishlist_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `addresses`
--
ALTER TABLE `addresses`
  ADD CONSTRAINT `fk_addresses_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `inventory`
--
ALTER TABLE `inventory`
  ADD CONSTRAINT `fk_inventory_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_oit_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_oit_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON UPDATE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `fk_pay_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `wishlist_items`
--
ALTER TABLE `wishlist_items`
  ADD CONSTRAINT `wishlist_items_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wishlist_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
