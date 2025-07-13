-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 24, 2025 at 05:00 PM
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
-- Database: `electricityms`
--

-- --------------------------------------------------------

--
-- Table structure for table `bills`
--

CREATE TABLE `bills` (
  `id` int(11) NOT NULL,
  `bill_date` date NOT NULL,
  `units_consumed` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `status` enum('paid','unpaid') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bills`
--

INSERT INTO `bills` (`id`, `bill_date`, `units_consumed`, `customer_id`, `amount`, `due_date`, `status`) VALUES
(1, '2025-05-24', 120, 1, 600.00, '2025-06-08', 'paid'),
(2, '2025-05-24', 200, 2, 1000.00, '2025-06-08', 'paid'),
(33, '2025-04-01', 250, 1, 3750.00, '2025-04-15', 'unpaid'),
(34, '2025-04-01', 180, 2, 2700.00, '2025-04-15', 'paid'),
(35, '2025-04-01', 300, 3, 4500.00, '2025-04-15', 'unpaid'),
(36, '2025-04-05', 220, 4, 3300.00, '2025-04-20', 'paid'),
(37, '2025-04-05', 190, 5, 2850.00, '2025-04-20', 'unpaid'),
(38, '2025-04-05', 260, 6, 3900.00, '2025-04-20', 'paid'),
(39, '2025-04-10', 150, 7, 2250.00, '2025-04-25', 'unpaid'),
(40, '2025-04-10', 210, 8, 3150.00, '2025-04-25', 'paid'),
(41, '2025-04-10', 240, 9, 3600.00, '2025-04-25', 'unpaid'),
(42, '2025-04-15', 200, 10, 3000.00, '2025-04-30', 'paid'),
(43, '2025-04-15', 175, 11, 2625.00, '2025-04-30', 'unpaid'),
(44, '2025-04-15', 230, 12, 3450.00, '2025-04-30', 'paid'),
(45, '2025-04-20', 160, 13, 2400.00, '2025-05-05', 'unpaid'),
(46, '2025-04-20', 185, 14, 2775.00, '2025-05-05', 'paid'),
(47, '2025-04-20', 195, 15, 2925.00, '2025-05-05', 'unpaid'),
(48, '2025-04-25', 210, 16, 3150.00, '2025-05-10', 'paid'),
(49, '2025-04-25', 225, 17, 3375.00, '2025-05-10', 'unpaid'),
(50, '2025-04-25', 205, 18, 3075.00, '2025-05-10', 'paid'),
(51, '2025-05-01', 180, 19, 2700.00, '2025-05-15', 'unpaid'),
(52, '2025-05-01', 190, 20, 2850.00, '2025-05-15', 'paid'),
(53, '2025-05-01', 250, 21, 3750.00, '2025-05-15', 'unpaid'),
(54, '2025-05-05', 230, 22, 3450.00, '2025-05-20', 'paid'),
(55, '2025-05-05', 200, 23, 3000.00, '2025-05-20', 'unpaid'),
(56, '2025-05-05', 270, 24, 4050.00, '2025-05-20', 'paid'),
(57, '2025-05-10', 260, 25, 3900.00, '2025-05-25', 'unpaid'),
(58, '2025-05-10', 240, 26, 3600.00, '2025-05-25', 'paid'),
(59, '2025-05-10', 220, 27, 3300.00, '2025-05-25', 'unpaid'),
(60, '2025-05-15', 185, 28, 2775.00, '2025-05-30', 'paid'),
(61, '2025-05-15', 210, 29, 3150.00, '2025-05-30', 'unpaid'),
(62, '2025-05-15', 195, 30, 2925.00, '2025-05-30', 'paid');

-- --------------------------------------------------------

--
-- Table structure for table `complaints`
--

CREATE TABLE `complaints` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `status` enum('pending','resolved') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `complaints`
--

INSERT INTO `complaints` (`id`, `customer_id`, `name`, `message`, `status`) VALUES
(1, 1, 'Hina', 'meter reading is not accurate.', 'resolved'),
(2, 1, 'hina', 'Issue with last month\'s bill.', 'pending'),
(3, 2, 'yusrra', 'Meter reading seems incorrect.', 'pending'),
(4, 3, 'ahmed', 'Billing discrepancy noticed.', 'pending'),
(5, 4, 'fatima', 'Power outage in my area.', 'resolved'),
(6, 5, 'mohammed', 'Need clarification on charges.', 'pending'),
(7, 6, 'ali', 'Frequent voltage fluctuation.', 'pending'),
(8, 7, 'aisha', 'Requesting bill correction.', 'pending'),
(9, 8, 'ibrahim', 'Overcharged this month.', 'pending'),
(10, 9, 'layla', 'Meter not functioning.', 'pending'),
(11, 10, 'omar', 'Issue with online payment.', 'pending'),
(12, 11, 'zainab', 'Power failure complaint.', 'pending'),
(13, 12, 'yusuf', 'High bill despite low usage.', 'pending'),
(14, 13, 'mariam', 'Incorrect meter reading.', 'pending'),
(15, 14, 'hassan', 'Service interruption complaint.', 'pending'),
(16, 15, 'noura', 'Late bill delivery.', 'pending'),
(17, 16, 'khalid', 'Unexpected service cut.', 'pending'),
(18, 17, 'sara', 'Billing account mismatch.', 'resolved'),
(19, 18, 'tariq', 'Frequent disconnections.', 'resolved'),
(20, 19, 'amina', 'Request for new connection.', 'pending'),
(21, 20, 'salim', 'Complaint about rude staff.', 'resolved'),
(22, 21, 'yasmin', 'Overcharge due to wrong meter.', 'pending'),
(23, 22, 'karim', 'Smart meter issues.', 'resolved'),
(24, 23, 'hana', 'Need bill payment extension.', 'pending'),
(25, 24, 'sami', 'Unable to login to portal.', 'pending'),
(67, 25, 'dina', 'No bill received.', 'pending'),
(68, 26, 'rashid', 'Previous complaint unresolved.', 'pending'),
(69, 27, 'salma', 'Electricity fluctuation.', 'resolved'),
(70, 28, 'bilal', 'Error in bill generation.', 'pending'),
(71, 29, 'zayd', 'Requesting usage breakdown.', 'resolved'),
(72, 30, 'junaid', 'Power theft in area.', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `name`, `email`, `address`, `phone`, `user_id`) VALUES
(1, 'Hina', 'hina@gmail.com', 'Rawalpindi', '12345678999', 2),
(2, 'Yusra', 'Yusra@gmail.com', 'Islamabad', '12345678999', 3),
(3, 'ahmed', 'ahmed@example.com', 'House 12, Street 5, G-9/1, Islamabad', '0301-1111111', 4),
(4, 'fatima', 'fatima@example.com', 'House 45, Block B, DHA Phase 6, Lahore', '0302-2222222', 5),
(5, 'mohammed', 'mohammed@example.com', 'Flat 3A, Gulshan-e-Iqbal, Karachi', '0303-3333333', 6),
(6, 'ali', 'ali@example.com', 'House 78, Street 12, F-10, Islamabad', '0304-4444444', 7),
(7, 'aisha', 'aisha@example.com', 'House 90, Model Town, Lahore', '0305-5555555', 8),
(8, 'ibrahim', 'ibrahim@example.com', 'House 21, PECHS Block 2, Karachi', '0306-6666666', 9),
(9, 'layla', 'layla@example.com', 'Flat 5, Bahria Town Phase 8, Rawalpindi', '0307-7777777', 10),
(10, 'omar', 'omar@example.com', 'House 100, North Nazimabad, Karachi', '0308-8888888', 11),
(11, 'zainab', 'zainab@example.com', 'House 11, Gulberg III, Lahore', '0309-9999999', 12),
(12, 'yusuf', 'yusuf@example.com', 'House 3, F-6/2, Islamabad', '0310-1010101', 13),
(13, 'mariam', 'mariam@example.com', 'Flat 9C, Askari 10, Lahore', '0311-1112121', 14),
(14, 'hassan', 'hassan@example.com', 'House 14, Clifton Block 8, Karachi', '0312-1313131', 15),
(15, 'noura', 'noura@example.com', 'House 88, Satellite Town, Rawalpindi', '0313-1414141', 16),
(16, 'khalid', 'khalid@example.com', 'Plot 22, Gulistan-e-Jauhar, Karachi', '0314-1515151', 17),
(17, 'sara', 'sara@example.com', 'House 32, Johar Town, Lahore', '0315-1616161', 18),
(18, 'tariq', 'tariq@example.com', 'House 67, I-8/3, Islamabad', '0316-1717171', 19),
(19, 'amina', 'amina@example.com', 'Flat 1B, Garden East, Karachi', '0317-1818181', 20),
(20, 'salim', 'salim@example.com', 'House 10, Wapda Town, Lahore', '0318-1919191', 21),
(21, 'yasmin', 'yasmin@example.com', 'House 43, G-13, Islamabad', '0319-2020202', 22),
(22, 'karim', 'karim@example.com', 'Flat 7A, Gulshan-e-Maymar, Karachi', '0320-2121212', 23),
(23, 'hana', 'hana@example.com', 'House 56, DHA Phase 2, Lahore', '0321-2222323', 24),
(24, 'sami', 'sami@example.com', 'House 8, F-11/1, Islamabad', '0322-2424242', 25),
(25, 'dina', 'dina@example.com', 'House 6, Malir Cantt, Karachi', '0323-2525252', 26),
(26, 'rashid', 'rashid@example.com', 'Flat 10, Bahria Orchard, Lahore', '0324-2626262', 27),
(27, 'salma', 'salma@example.com', 'House 5, G-7/4, Islamabad', '0325-2727272', 28),
(28, 'bilal', 'bilal@example.com', 'House 39, Scheme 33, Karachi', '0326-2828282', 29),
(29, 'zayd', 'zayd@example.com', 'Flat 12C, Askari 11, Lahore', '0327-2929292', 30),
(30, 'junaid', 'junaid@example.com', 'House 17, PWD Housing Society, Islamabad', '0328-3030303', 31),
(31, 'farah', 'farah@example.com', 'House 55, Nazimabad No. 1, Karachi', '0329-3131313', 32),
(32, 'nadia', 'nadiaexample.com', 'Flat 4, Valencia Town, Lahore', '0330-3232323', 33);

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `transaction_date` datetime NOT NULL DEFAULT current_timestamp(),
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `status` enum('completed','pending','failed') NOT NULL DEFAULT 'pending',
  `bill_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `customer_id`, `transaction_date`, `amount`, `payment_method`, `status`, `bill_id`) VALUES
(1, 1, '2025-05-24 19:10:45', 600.00, 'online pay', 'completed', 1),
(2, 2, '2025-05-24 19:13:21', 1000.00, 'online', 'completed', 2);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','customer') NOT NULL,
  `email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `email`) VALUES
(1, 'admin', '$2y$10$KShSXnPzHI6.Zy5Gm1.9ruQklVS.i3RLrQH3fRGe530mwdU1KWGM6', 'admin', 'admin@example.com'),
(2, 'Hina', '$2y$10$QY.ukkssFA1sLtZOhRUiPuLfS/1EiWe1axHheJd0RitbISA2J6W9e', 'customer', 'hina@gmail.com'),
(3, 'Yusra', '$2y$10$2NIswIszgz67Ai8wt.boZ.n4QJ5qaNUUHH5uHyXcC7JLAYJxXF.Wq', 'customer', 'Yusra@gmail.com'),
(4, 'ahmed', '$2y$10$yQFzft3/0b0TmMYhtrCESeXQ0AmvS.H2hdicW1o7F2HvHW3cxu1EO', 'customer', 'ahmed@example.com'),
(5, 'fatima', '$2y$10$yQFzft3/0b0TmMYhtrCESeXQ0AmvS.H2hdicW1o7F2HvHW3cxu1EO', 'customer', 'fatima@example.com'),
(6, 'mohammed', '$2y$10$yQFzft3/0b0TmMYhtrCESeXQ0AmvS.H2hdicW1o7F2HvHW3cxu1EO', 'customer', 'mohammed@example.com'),
(7, 'ali', '$2y$10$yQFzft3/0b0TmMYhtrCESeXQ0AmvS.H2hdicW1o7F2HvHW3cxu1EO', 'customer', 'ali@example.com'),
(8, 'aisha', '$2y$10$yQFzft3/0b0TmMYhtrCESeXQ0AmvS.H2hdicW1o7F2HvHW3cxu1EO', 'customer', 'aisha@example.com'),
(9, 'ibrahim', '$2y$10$yQFzft3/0b0TmMYhtrCESeXQ0AmvS.H2hdicW1o7F2HvHW3cxu1EO', 'customer', 'ibrahim@example.com'),
(10, 'layla', '$2y$10$yQFzft3/0b0TmMYhtrCESeXQ0AmvS.H2hdicW1o7F2HvHW3cxu1EO', 'customer', 'layla@example.com'),
(11, 'omar', '$2y$10$yQFzft3/0b0TmMYhtrCESeXQ0AmvS.H2hdicW1o7F2HvHW3cxu1EO', 'customer', 'omar@example.com'),
(12, 'zainab', '$2y$10$yQFzft3/0b0TmMYhtrCESeXQ0AmvS.H2hdicW1o7F2HvHW3cxu1EO', 'customer', 'zainab@example.com'),
(13, 'yusuf', '$2y$10$yQFzft3/0b0TmMYhtrCESeXQ0AmvS.H2hdicW1o7F2HvHW3cxu1EO', 'customer', 'yusuf@example.com'),
(14, 'mariam', '$2y$10$yQFzft3/0b0TmMYhtrCESeXQ0AmvS.H2hdicW1o7F2HvHW3cxu1EO', 'customer', 'mariam@example.com'),
(15, 'hassan', '$2y$10$yQFzft3/0b0TmMYhtrCESeXQ0AmvS.H2hdicW1o7F2HvHW3cxu1EO', 'customer', 'hassan@example.com'),
(16, 'noura', '$2y$10$yQFzft3/0b0TmMYhtrCESeXQ0AmvS.H2hdicW1o7F2HvHW3cxu1EO', 'customer', 'noura@example.com'),
(17, 'khalid', '$2y$10$yQFzft3/0b0TmMYhtrCESeXQ0AmvS.H2hdicW1o7F2HvHW3cxu1EO', 'customer', 'khalid@example.com'),
(18, 'sara', '$2y$10$yQFzft3/0b0TmMYhtrCESeXQ0AmvS.H2hdicW1o7F2HvHW3cxu1EO', 'customer', 'sara@example.com'),
(19, 'tariq', '$2y$10$LlpbYWh72jFwIbzScm8zxOSxBkzMq3axOloLBnYZrTtfl.YjLFXz2', 'customer', 'tariq@example.com'),
(20, 'amina', '$2y$10$LlpbYWh72jFwIbzScm8zxOSxBkzMq3axOloLBnYZrTtfl.YjLFXz2', 'customer', 'amina@example.com'),
(21, 'salim', '$2y$10$LlpbYWh72jFwIbzScm8zxOSxBkzMq3axOloLBnYZrTtfl.YjLFXz2', 'customer', 'salim@example.com'),
(22, 'yasmin', '$2y$10$LlpbYWh72jFwIbzScm8zxOSxBkzMq3axOloLBnYZrTtfl.YjLFXz2', 'customer', 'yasmin@example.com'),
(23, 'karim', '$2y$10$LlpbYWh72jFwIbzScm8zxOSxBkzMq3axOloLBnYZrTtfl.YjLFXz2', 'customer', 'karim@example.com'),
(24, 'hana', '$2y$10$LlpbYWh72jFwIbzScm8zxOSxBkzMq3axOloLBnYZrTtfl.YjLFXz2', 'customer', 'hana@example.com'),
(25, 'sami', '$2y$10$LlpbYWh72jFwIbzScm8zxOSxBkzMq3axOloLBnYZrTtfl.YjLFXz2', 'customer', 'sami@example.com'),
(26, 'dina', '$2y$10$LlpbYWh72jFwIbzScm8zxOSxBkzMq3axOloLBnYZrTtfl.YjLFXz2', 'customer', 'dina@example.com'),
(27, 'rashid', '$2y$10$LlpbYWh72jFwIbzScm8zxOSxBkzMq3axOloLBnYZrTtfl.YjLFXz2', 'customer', 'rashid@example.com'),
(28, 'salma', '$2y$10$LlpbYWh72jFwIbzScm8zxOSxBkzMq3axOloLBnYZrTtfl.YjLFXz2', 'customer', 'salma@example.com'),
(29, 'bilal', '$2y$10$LlpbYWh72jFwIbzScm8zxOSxBkzMq3axOloLBnYZrTtfl.YjLFXz2', 'customer', 'bilal@example.com'),
(30, 'zayd', '$2y$10$LlpbYWh72jFwIbzScm8zxOSxBkzMq3axOloLBnYZrTtfl.YjLFXz2', 'customer', 'zayd@example.com'),
(31, 'junaid', '$2y$10$LlpbYWh72jFwIbzScm8zxOSxBkzMq3axOloLBnYZrTtfl.YjLFXz2', 'customer', 'junaid@example.com'),
(32, 'farah', '$2y$10$LlpbYWh72jFwIbzScm8zxOSxBkzMq3axOloLBnYZrTtfl.YjLFXz2', 'customer', 'farah@example.com'),
(33, 'nadia', '$2y$10$LlpbYWh72jFwIbzScm8zxOSxBkzMq3axOloLBnYZrTtfl.YjLFXz2', 'customer', 'nadia@example.com');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bills`
--
ALTER TABLE `bills`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `complaints`
--
ALTER TABLE `complaints`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_customer_user` (`user_id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `bill_id` (`bill_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bills`
--
ALTER TABLE `bills`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `complaints`
--
ALTER TABLE `complaints`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bills`
--
ALTER TABLE `bills`
  ADD CONSTRAINT `bills_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`);

--
-- Constraints for table `complaints`
--
ALTER TABLE `complaints`
  ADD CONSTRAINT `complaints_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`);

--
-- Constraints for table `customers`
--
ALTER TABLE `customers`
  ADD CONSTRAINT `fk_customer_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`bill_id`) REFERENCES `bills` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
