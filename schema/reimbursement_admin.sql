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
-- Table structure for table `reimbursement_admin`
--

CREATE TABLE `reimbursement_admin` (
  `uid` int(11) NOT NULL,
  `admin_name` varchar(128) NOT NULL,
  `admin_email` varchar(254) NOT NULL,
  `super_email` varchar(254) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `reimbursement_admin`
--

INSERT INTO `reimbursement_admin` (`uid`, `admin_name`, `admin_email`, `super_email`) VALUES
(1, 'Admin', 'admin@mail.uc.edu', 'super@mail.uc.edu');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `reimbursement_admin`
--
ALTER TABLE `reimbursement_admin`
  ADD UNIQUE KEY `uid` (`uid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `reimbursement_admin`
--
ALTER TABLE `reimbursement_admin`
  MODIFY `uid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
