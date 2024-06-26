-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 21, 2024 at 08:41 AM
-- Server version: 10.4.21-MariaDB
-- PHP Version: 7.4.29
SET
  SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

START TRANSACTION;

SET
  time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;

/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;

/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;

/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `craigeDB`
--
-- --------------------------------------------------------
--
-- Table structure for table `messages`
--
CREATE TABLE
  `messages` (
    `id` int (11) NOT NULL,
    `message` text NOT NULL,
    `recipient_id` int (11) NOT NULL,
    `sender_id` int (11) NOT NULL,
    `status` int (11) DEFAULT 1,
    `created` datetime DEFAULT current_timestamp(),
    `modified` datetime DEFAULT current_timestamp(),
    `created_ip` varchar(50) DEFAULT NULL,
    `modified_ip` varchar(50) DEFAULT NULL
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- --------------------------------------------------------
--
-- Table structure for table `users`
--
CREATE TABLE
  `users` (
    `id` int (11) NOT NULL,
    `name` varchar(20) NOT NULL,
    `email` varchar(100) NOT NULL,
    `password` varchar(255) NOT NULL,
    `gender` varchar(10) DEFAULT NULL,
    `birthdate` datetime DEFAULT NULL,
    `hobby` text DEFAULT NULL,
    `profile_picture` text DEFAULT NULL,
    `last_login_time` datetime DEFAULT NULL,
    `created` datetime DEFAULT NULL,
    `modified` datetime DEFAULT NULL,
    `created_ip` varchar(50) DEFAULT NULL,
    `modified_ip` varchar(50) DEFAULT NULL
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

--
-- Indexes for dumped tables
--
--
-- Indexes for table `messages`
--
ALTER TABLE `messages` ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users` ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--
--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages` MODIFY `id` int (11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users` MODIFY `id` int (11) NOT NULL AUTO_INCREMENT;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;

/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;

/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;