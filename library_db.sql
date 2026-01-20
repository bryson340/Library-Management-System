-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 20, 2026 at 09:36 PM
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
-- Database: `library_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `book_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `author` varchar(100) NOT NULL,
  `genre` varchar(50) DEFAULT NULL,
  `is_available` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `cover_image` varchar(255) DEFAULT 'default.jpg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`book_id`, `title`, `author`, `genre`, `is_available`, `created_at`, `cover_image`) VALUES
(1, 'The Great Gatsby', 'F. Scott Fitzgerald', 'Classic', 1, '2026-01-20 19:03:28', 'book_696fe53878678.jpg'),
(2, '1984', 'George Orwell', 'Dystopian', 1, '2026-01-20 19:03:28', 'book_696fe56dd4476.jpg'),
(3, 'Introduction to PHP', 'John Doe', 'Education', 1, '2026-01-20 19:03:28', 'default.jpg'),
(4, 'clean code', 'robert martin', 'tech', 1, '2026-01-20 19:09:19', 'book_696fe4fdd554c.jpg'),
(5, 'Rich Dad Poor Dad', 'Robert Kiyosaki', 'knowledge', 0, '2026-01-20 20:10:48', 'book_696fe148f0c01.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `issued_books`
--

CREATE TABLE `issued_books` (
  `issue_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `book_id` int(11) DEFAULT NULL,
  `issue_date` date DEFAULT curdate(),
  `return_date` date DEFAULT NULL,
  `status` enum('issued','returned') DEFAULT 'issued'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `issued_books`
--

INSERT INTO `issued_books` (`issue_id`, `user_id`, `book_id`, `issue_date`, `return_date`, `status`) VALUES
(1, 1, 4, '2026-01-21', '2026-01-20', 'returned'),
(2, 1, 5, '2026-01-21', NULL, 'issued');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','student') DEFAULT 'student',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `role`, `created_at`) VALUES
(1, 'admin', '$2y$10$HkokhwzAI4vDlfJRqy.CBOzcZAZkRHhUleiHsPHCoX1ALRcrWfF.W', 'admin', '2026-01-20 18:42:39');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`book_id`);

--
-- Indexes for table `issued_books`
--
ALTER TABLE `issued_books`
  ADD PRIMARY KEY (`issue_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `book_id` (`book_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `book_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `issued_books`
--
ALTER TABLE `issued_books`
  MODIFY `issue_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `issued_books`
--
ALTER TABLE `issued_books`
  ADD CONSTRAINT `issued_books_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `issued_books_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`book_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
