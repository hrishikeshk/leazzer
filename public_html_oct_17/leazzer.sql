-- phpMyAdmin SQL Dump
-- version 4.7.7
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 04, 2019 at 05:28 AM
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
DROP DATABASE IF EXISTS `leazzer`;
CREATE DATABASE IF NOT EXISTS `leazzer` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `leazzer`;

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

DROP TABLE IF EXISTS `admin`;
CREATE TABLE IF NOT EXISTS `admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(200) NOT NULL,
  `password` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `admin_configuration`
--

DROP TABLE IF EXISTS `admin_configuration`;
CREATE TABLE IF NOT EXISTS `admin_configuration` (
  `name` varchar(255) NOT NULL,
  `data_value` longtext NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `amenity_dictionary`
--

DROP TABLE IF EXISTS `amenity_dictionary`;
CREATE TABLE IF NOT EXISTS `amenity_dictionary` (
  `auto_id` int(11) NOT NULL AUTO_INCREMENT,
  `option_id` int(11) NOT NULL,
  `equivalent` varchar(100) NOT NULL,
  PRIMARY KEY (`auto_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

DROP TABLE IF EXISTS `customer`;
CREATE TABLE IF NOT EXISTS `customer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lastname` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `emailid` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pwd` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `logintype` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `facility`
--

DROP TABLE IF EXISTS `facility`;
CREATE TABLE IF NOT EXISTS `facility` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `sub_images` varchar(800) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `facility_amenity`
--

DROP TABLE IF EXISTS `facility_amenity`;
CREATE TABLE IF NOT EXISTS `facility_amenity` (
  `facility_id` varchar(20) NOT NULL,
  `amenity` varchar(100) NOT NULL,
  KEY `fac_amenity_id` (`facility_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `facility_images`
--

DROP TABLE IF EXISTS `facility_images`;
CREATE TABLE IF NOT EXISTS `facility_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `facility_id` int(11) NOT NULL,
  `path` varchar(127) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `facility_master`
--

DROP TABLE IF EXISTS `facility_master`;
CREATE TABLE IF NOT EXISTS `facility_master` (
  `auto_id` int(11) NOT NULL AUTO_INCREMENT,
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
  `receivereserve` int(11) DEFAULT '1',
  `pdispc` int(11) DEFAULT NULL,
  `pdismo` int(11) DEFAULT NULL,
  `pdispcfm` int(11) DEFAULT NULL,
  `pdispcfmfd` int(11) DEFAULT NULL,
  `pdispcfd` int(11) DEFAULT NULL,
  `pdismofd` int(11) DEFAULT NULL,
  `pdismofm` int(11) DEFAULT NULL,
  PRIMARY KEY (`auto_id`),
  UNIQUE KEY `id` (`id`),
  KEY `slatlng` (`searchable`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `facility_owner`
--

DROP TABLE IF EXISTS `facility_owner`;
CREATE TABLE IF NOT EXISTS `facility_owner` (
  `auto_id` int(11) NOT NULL AUTO_INCREMENT,
  `companyname` varchar(300) DEFAULT NULL,
  `firstname` varchar(200) DEFAULT NULL,
  `lastname` varchar(200) DEFAULT NULL,
  `pwd` varchar(250) DEFAULT NULL,
  `emailid` varchar(200) NOT NULL,
  `logintype` varchar(100) DEFAULT NULL,
  `phone` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`auto_id`),
  UNIQUE KEY `emailid` (`emailid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `forgotpwd`
--

DROP TABLE IF EXISTS `forgotpwd`;
CREATE TABLE IF NOT EXISTS `forgotpwd` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `emailid` varchar(200) NOT NULL,
  `code` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `image`
--

DROP TABLE IF EXISTS `image`;
CREATE TABLE IF NOT EXISTS `image` (
  `auto_id` int(11) NOT NULL AUTO_INCREMENT,
  `url_fullsize` varchar(100) DEFAULT NULL,
  `url_thumbsize` varchar(100) DEFAULT NULL,
  `facility_id` varchar(20) NOT NULL,
  PRIMARY KEY (`auto_id`),
  KEY `img_fac_id_2` (`facility_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `options`
--

DROP TABLE IF EXISTS `options`;
CREATE TABLE IF NOT EXISTS `options` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `opt` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `reserve`
--

DROP TABLE IF EXISTS `reserve`;
CREATE TABLE IF NOT EXISTS `reserve` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cid` int(11) DEFAULT NULL,
  `reservefromdate` decimal(18,0) DEFAULT NULL,
  `reservetodate` decimal(18,0) DEFAULT NULL,
  `fid` int(11) DEFAULT NULL,
  `units` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `review`
--

DROP TABLE IF EXISTS `review`;
CREATE TABLE IF NOT EXISTS `review` (
  `auto_id` int(11) NOT NULL AUTO_INCREMENT,
  `facility_id` varchar(20) NOT NULL,
  `listing_avail_id` varchar(20) NOT NULL,
  `rating` double DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `message` varchar(1000) DEFAULT NULL,
  `excerpt` varchar(1000) DEFAULT NULL,
  `nickname` varchar(100) DEFAULT NULL,
  `timestamp` date DEFAULT NULL,
  `stars` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`auto_id`),
  KEY `review_fac_id` (`facility_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `unit`
--

DROP TABLE IF EXISTS `unit`;
CREATE TABLE IF NOT EXISTS `unit` (
  `auto_id` int(11) NOT NULL AUTO_INCREMENT,
  `facility_id` varchar(20) NOT NULL,
  `size` varchar(50) NOT NULL,
  `price` double DEFAULT NULL,
  `description` varchar(1000) DEFAULT NULL,
  `promo` varchar(1000) DEFAULT NULL,
  `price_freq` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`auto_id`),
  KEY `unit_facId_size_2` (`facility_id`,`size`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `units`
--

DROP TABLE IF EXISTS `units`;
CREATE TABLE IF NOT EXISTS `units` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `units` varchar(200) NOT NULL,
  `images` text,
  `standard` int(2) DEFAULT NULL,
  `description` text,
  `price` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `units_size` (`units`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `unit_amenity`
--

DROP TABLE IF EXISTS `unit_amenity`;
CREATE TABLE IF NOT EXISTS `unit_amenity` (
  `unit_id` int(11) NOT NULL,
  `amenity` varchar(100) NOT NULL,
  KEY `ua_unit_id_amenity` (`unit_id`,`amenity`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
