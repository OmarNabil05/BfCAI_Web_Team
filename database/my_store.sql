-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 04, 2025 at 07:46 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `my_store`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `photo`, `description`) VALUES
(1, 'Burgers', 'burgers.jpg', 'Delicious beef, chicken, and veggie burgers'),
(2, 'Pizza', 'pizza.jpg', 'Hot and fresh pizzas with various toppings'),
(3, 'Pasta', 'pasta.jpg', 'Italian pasta dishes with rich sauces'),
(4, 'Salads', 'salads.jpg', 'Fresh and healthy salad options'),
(5, 'Desserts', 'desserts.jpg', 'Sweet treats and desserts'),
(6, 'Beverages', 'beverages.jpg', 'Cold and hot drinks');

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `photos` varchar(1000) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  `category_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`id`, `name`, `photos`, `price`, `description`, `category_id`) VALUES
(1, 'Classic Beef Burger', 'classic_burger.jpg', 12.99, 'Juicy beef patty with lettuce, tomato, onion, and special sauce', 1),
(2, 'Chicken Burger', 'chicken_burger.jpg', 11.99, 'Crispy chicken breast with mayo and pickles', 1),
(3, 'Veggie Burger', 'veggie_burger.jpg', 10.99, 'Plant-based patty with avocado and veggies', 1),
(4, 'Double Cheese Burger', 'double_burger.jpg', 15.99, 'Two beef patties with double cheese', 1),
(5, 'Margherita Pizza', 'margherita.jpg', 14.99, 'Classic tomato sauce, mozzarella, and basil', 2),
(6, 'Pepperoni Pizza', 'pepperoni.jpg', 16.99, 'Loaded with pepperoni and extra cheese', 2),
(7, 'Vegetarian Pizza', 'veggie_pizza.jpg', 15.99, 'Fresh vegetables and mushrooms', 2),
(8, 'BBQ Chicken Pizza', 'bbq_chicken.jpg', 17.99, 'BBQ sauce, grilled chicken, and onions', 2),
(9, 'Spaghetti Carbonara', 'carbonara.jpg', 13.99, 'Creamy sauce with bacon and parmesan', 3),
(10, 'Penne Arrabiata', 'arrabiata.jpg', 12.99, 'Spicy tomato sauce with garlic', 3),
(11, 'Fettuccine Alfredo', 'alfredo.jpg', 14.99, 'Rich cream sauce with parmesan cheese', 3),
(12, 'Lasagna', 'lasagna.jpg', 15.99, 'Layered pasta with meat sauce and cheese', 3),
(13, 'Caesar Salad', 'caesar.jpg', 9.99, 'Romaine lettuce with caesar dressing and croutons', 4),
(14, 'Greek Salad', 'greek_salad.jpg', 10.99, 'Fresh vegetables with feta cheese and olives', 4),
(15, 'Chicken Salad', 'chicken_salad.jpg', 12.99, 'Grilled chicken breast on mixed greens', 4),
(16, 'Chocolate Cake', 'chocolate_cake.jpg', 6.99, 'Rich chocolate cake with chocolate frosting', 5),
(17, 'Cheesecake', 'cheesecake.jpg', 7.99, 'Creamy New York style cheesecake', 5),
(18, 'Tiramisu', 'tiramisu.jpg', 7.99, 'Italian coffee-flavored dessert', 5),
(19, 'Ice Cream Sundae', 'sundae.jpg', 5.99, 'Vanilla ice cream with toppings', 5),
(20, 'Coca Cola', 'coke.jpg', 2.99, 'Classic cola drink', 6),
(21, 'Orange Juice', 'orange_juice.jpg', 3.99, 'Freshly squeezed orange juice', 6),
(22, 'Iced Coffee', 'iced_coffee.jpg', 4.99, 'Cold brew coffee with ice', 6),
(23, 'Lemonade', 'lemonade.jpg', 3.49, 'Fresh homemade lemonade', 6);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `status`, `created_at`) VALUES
(1, 1, 1, '2025-12-01 10:30:00'),
(2, 2, 1, '2025-12-01 12:15:00'),
(3, 4, 1, '2025-12-02 14:20:00'),
(4, 5, 1, '2025-12-02 16:45:00'),
(5, 6, 0, '2025-12-03 11:00:00'),
(6, 7, 0, '2025-12-03 13:30:00'),
(7, 1, 0, '2025-12-04 09:15:00'),
(8, 3, 0, '2025-12-04 15:00:00'),
(9, 4, 0, '2025-12-05 10:30:00'),
(10, 5, 0, '2025-12-05 12:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(10) UNSIGNED NOT NULL,
  `order_id` int(10) UNSIGNED NOT NULL,
  `item_id` int(10) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `item_id`, `quantity`) VALUES
(1, 1, 1, 2),
(2, 1, 20, 2),
(3, 1, 16, 1),
(4, 2, 5, 1),
(5, 2, 6, 2),
(6, 2, 20, 4),
(7, 3, 9, 1),
(8, 3, 11, 1),
(9, 3, 13, 2),
(10, 3, 21, 2),
(11, 4, 4, 2),
(12, 4, 7, 1),
(13, 4, 15, 1),
(14, 4, 20, 3),
(15, 4, 17, 2),
(16, 5, 2, 1),
(17, 5, 20, 1),
(18, 6, 15, 1),
(19, 6, 14, 1),
(20, 6, 21, 1),
(21, 7, 8, 2),
(22, 7, 20, 3),
(23, 7, 19, 2),
(24, 8, 10, 1),
(25, 8, 13, 1),
(26, 8, 22, 1),
(27, 9, 1, 3),
(28, 9, 5, 1),
(29, 9, 9, 2),
(30, 9, 20, 5),
(31, 9, 16, 3),
(32, 10, 3, 1),
(33, 10, 23, 1),
(34, 10, 18, 1);

INSERT INTO `order_items` (`id`, `order_id`, `item_id`, `quantity`) VALUES
(1, 1, 1, 1),
(2, 1, 4, 2),
(3, 2, 3, 1),
(4, 3, 2, 1),
(5, 3, 4, 3);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `phone_number`, `email`, `password`, `role`) VALUES
(1, 'Alice Smith', '01000000001', 'alice@example.com', 'pass123', 0),
(2, 'Bob Johnson', '01000000002', 'bob@example.com', 'secret456', 0),
(3, 'Admin User', '01000000003', 'admin@example.com', 'adminpass', 1),
(4, 'John Smith', '01111111111', 'john@example.com', 'password123', 0),
(5, 'Sarah Johnson', '01222222222', 'sarah@example.com', 'password123', 0),
(6, 'Mike Davis', '01333333333', 'mike@example.com', 'password123', 0),
(7, 'Emma Wilson', '01444444444', 'emma@example.com', 'password123', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `items`
--
--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;
--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `items`
--
ALTER TABLE `items`
  ADD CONSTRAINT `items_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
