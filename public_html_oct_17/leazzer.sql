-- phpMyAdmin SQL Dump
-- version 4.7.7
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 16, 2018 at 07:58 AM
-- Server version: 5.6.39-cll-lve
-- PHP Version: 5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `leazzer`
--
CREATE DATABASE IF NOT EXISTS `leazzer` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `leazzer`;

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(200) NOT NULL,
  `password` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `admin_configuration`
--

CREATE TABLE `admin_configuration` (
  `name` varchar(255) NOT NULL,
  `data_value` longtext NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `amenity_dictionary`
--

CREATE TABLE `amenity_dictionary` (
  `auto_id` int(11) NOT NULL,
  `option_id` int(11) NOT NULL,
  `equivalent` varchar(100) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `id` int(11) NOT NULL,
  `firstname` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lastname` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `emailid` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pwd` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `logintype` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `facility`
--

CREATE TABLE `facility` (
  `id` int(11) NOT NULL,
  `firstname` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lastname` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `emailid` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pwd` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `logintype` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `companyname` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address1` text COLLATE utf8_unicode_ci,
  `address2` text COLLATE utf8_unicode_ci,
  `city` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `state` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `zipcode` int(11) DEFAULT NULL,
  `lat` decimal(11,8) DEFAULT NULL,
  `lng` decimal(11,8) DEFAULT NULL,
  `searchable` int(11) DEFAULT NULL,
  `reservationdays` int(11) DEFAULT NULL,
  `receivereserve` int(11) DEFAULT NULL,
  `options` text COLLATE utf8_unicode_ci,
  `units` text COLLATE utf8_unicode_ci,
  `image` varchar(4000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(300) COLLATE utf8_unicode_ci NOT NULL,
  `coupon_code` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `coupon_desc` varchar(300) COLLATE utf8_unicode_ci NOT NULL,
  `sub_images` varchar(800) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `facility_amenity`
--

CREATE TABLE `facility_amenity` (
  `facility_id` varchar(20) NOT NULL,
  `amenity` varchar(100) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `facility_images`
--

CREATE TABLE `facility_images` (
  `id` int(11) NOT NULL,
  `facility_id` int(11) NOT NULL,
  `path` varchar(127) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `facility_master`
--

CREATE TABLE `facility_master` (
  `auto_id` int(11) NOT NULL,
  `id` varchar(20) NOT NULL,
  `facility_owner_id` int(11) DEFAULT NULL,
  `title` varchar(100) NOT NULL,
  `description` varchar(1000) DEFAULT NULL,
  `url` varchar(100) NOT NULL,
  `distance` double DEFAULT NULL,
  `lowest_price` double DEFAULT NULL,
  `street` varchar(100) DEFAULT NULL,
  `locality` varchar(100) DEFAULT NULL,
  `region` varchar(100) DEFAULT NULL,
  `zip` varchar(100) DEFAULT NULL,
  `city` varchar(200) DEFAULT NULL,
  `state` varchar(200) DEFAULT NULL,
  `phone` varchar(200) DEFAULT NULL,
  `facility_promo` varchar(300) DEFAULT NULL,
  `lat` decimal(11,8) DEFAULT NULL,
  `lng` decimal(11,8) DEFAULT NULL,
  `searchable` int(11) NOT NULL DEFAULT '1',
  `status` int(11) DEFAULT '1',
  `reservationdays` int(11) DEFAULT '3',
  `receivereserve` int(11) DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `facility_owner`
--

CREATE TABLE `facility_owner` (
  `auto_id` int(11) NOT NULL,
  `companyname` varchar(300) DEFAULT NULL,
  `firstname` varchar(200) DEFAULT NULL,
  `lastname` varchar(200) DEFAULT NULL,
  `pwd` varchar(250) DEFAULT NULL,
  `emailid` varchar(200) NOT NULL,
  `logintype` varchar(100) DEFAULT NULL,
  `phone` varchar(200) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `forgotpwd`
--

CREATE TABLE `forgotpwd` (
  `id` int(11) NOT NULL,
  `uid` int(11) DEFAULT NULL,
  `emailid` varchar(200) NOT NULL,
  `code` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `image`
--

CREATE TABLE `image` (
  `auto_id` int(11) NOT NULL,
  `url_fullsize` varchar(100) DEFAULT NULL,
  `url_thumbsize` varchar(100) DEFAULT NULL,
  `facility_id` varchar(20) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `options`
--

CREATE TABLE `options` (
  `id` int(11) NOT NULL,
  `opt` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `reserve`
--

CREATE TABLE `reserve` (
  `id` int(11) NOT NULL,
  `cid` int(11) DEFAULT NULL,
  `reservefromdate` decimal(18,0) DEFAULT NULL,
  `reservetodate` decimal(18,0) DEFAULT NULL,
  `fid` int(11) DEFAULT NULL,
  `units` text COLLATE utf8_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `review`
--

CREATE TABLE `review` (
  `auto_id` int(11) NOT NULL,
  `facility_id` varchar(20) NOT NULL,
  `listing_avail_id` varchar(20) NOT NULL,
  `rating` double DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `message` varchar(1000) DEFAULT NULL,
  `excerpt` varchar(1000) DEFAULT NULL,
  `nickname` varchar(100) DEFAULT NULL,
  `timestamp` date DEFAULT NULL,
  `stars` varchar(100) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `unit`
--

CREATE TABLE `unit` (
  `auto_id` int(11) NOT NULL,
  `facility_id` varchar(20) NOT NULL,
  `size` varchar(50) NOT NULL,
  `price` double DEFAULT NULL,
  `description` varchar(1000) DEFAULT NULL,
  `promo` varchar(1000) DEFAULT NULL,
  `price_freq` varchar(100) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `units`
--

CREATE TABLE `units` (
  `id` int(11) NOT NULL,
  `units` varchar(200) NOT NULL,
  `images` text,
  `standard` int(2) DEFAULT NULL,
  `description` text,
  `price` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `unit_amenity`
--

CREATE TABLE `unit_amenity` (
  `unit_id` int(11) NOT NULL,
  `amenity` varchar(100) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admin_configuration`
--
ALTER TABLE `admin_configuration`
  ADD PRIMARY KEY (`name`);

--
-- Indexes for table `amenity_dictionary`
--
ALTER TABLE `amenity_dictionary`
  ADD PRIMARY KEY (`auto_id`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `facility`
--
ALTER TABLE `facility`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `facility_images`
--
ALTER TABLE `facility_images`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `facility_master`
--
ALTER TABLE `facility_master`
  ADD PRIMARY KEY (`auto_id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `facility_owner`
--
ALTER TABLE `facility_owner`
  ADD PRIMARY KEY (`auto_id`),
  ADD UNIQUE KEY `emailid` (`emailid`);

--
-- Indexes for table `forgotpwd`
--
ALTER TABLE `forgotpwd`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `image`
--
ALTER TABLE `image`
  ADD PRIMARY KEY (`auto_id`);

--
-- Indexes for table `options`
--
ALTER TABLE `options`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reserve`
--
ALTER TABLE `reserve`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `review`
--
ALTER TABLE `review`
  ADD PRIMARY KEY (`auto_id`);

--
-- Indexes for table `unit`
--
ALTER TABLE `unit`
  ADD PRIMARY KEY (`auto_id`);

--
-- Indexes for table `units`
--
ALTER TABLE `units`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `amenity_dictionary`
--
ALTER TABLE `amenity_dictionary`
  MODIFY `auto_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `facility`
--
ALTER TABLE `facility`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=362;

--
-- AUTO_INCREMENT for table `facility_images`
--
ALTER TABLE `facility_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `facility_master`
--
ALTER TABLE `facility_master`
  MODIFY `auto_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=489;

--
-- AUTO_INCREMENT for table `facility_owner`
--
ALTER TABLE `facility_owner`
  MODIFY `auto_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `forgotpwd`
--
ALTER TABLE `forgotpwd`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `image`
--
ALTER TABLE `image`
  MODIFY `auto_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2128;

--
-- AUTO_INCREMENT for table `options`
--
ALTER TABLE `options`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `reserve`
--
ALTER TABLE `reserve`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=163;

--
-- AUTO_INCREMENT for table `review`
--
ALTER TABLE `review`
  MODIFY `auto_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=358;

--
-- AUTO_INCREMENT for table `unit`
--
ALTER TABLE `unit`
  MODIFY `auto_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3791;

--
-- AUTO_INCREMENT for table `units`
--
ALTER TABLE `units`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=575;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
