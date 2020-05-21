-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Server version: 5.7.24-log
-- PHP Version: 7.2.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tribunal`
--

-- --------------------------------------------------------

--
-- Table structure for table `reimbursement_main`
--

CREATE TABLE `reimbursement_main` (
  `uid` int(11) NOT NULL,
  `name` varchar(128) NOT NULL,
  `position` varchar(128) NOT NULL,
  `email` varchar(254) NOT NULL,
  `mid` varchar(9) NOT NULL,
  `date` date NOT NULL,
  `vendor` varchar(128) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `description` varchar(128) NOT NULL,
  `status` varchar(128) NOT NULL,
  `type` varchar(128) NOT NULL,
  `receipt_name` varchar(254) DEFAULT NULL,
  `document_name` varchar(254) DEFAULT NULL,
  `officer_name` varchar(128) DEFAULT NULL,
  `officer_position` varchar(128) DEFAULT NULL,
  `address` varchar(254) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `reimbursement_main`
--
ALTER TABLE `reimbursement_main`
  ADD PRIMARY KEY (`uid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `reimbursement_main`
--
ALTER TABLE `reimbursement_main`
  MODIFY `uid` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
