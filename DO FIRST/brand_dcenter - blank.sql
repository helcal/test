-- phpMyAdmin SQL Dump
-- version 2.11.11.3
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 10, 2012 at 04:20 PM
-- Server version: 5.5.24
-- PHP Version: 5.3.15

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `vanilla_dcenter`
--

-- --------------------------------------------------------

--
-- Table structure for table `assigned_domain`
--

DROP TABLE IF EXISTS `assigned_domain`;
CREATE TABLE IF NOT EXISTS `assigned_domain` (
  `index` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) NOT NULL COMMENT 'The dealer userID',
  `address` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `domain_verfied` int(11) NOT NULL DEFAULT '0',
  `verif_tbl` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`index`),
  UNIQUE KEY `address` (`address`),
  KEY `userID` (`userID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `assigned_domain`
--


-- --------------------------------------------------------

--
-- Table structure for table `brand_access`
--

DROP TABLE IF EXISTS `brand_access`;
CREATE TABLE IF NOT EXISTS `brand_access` (
  `index` int(11) NOT NULL AUTO_INCREMENT,
  `brand_master_id` int(11) NOT NULL,
  `userID` int(11) NOT NULL COMMENT 'Logged in user may be allowed to manage the brand',
  `brand_access` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`index`),
  KEY `userID` (`userID`),
  KEY `brand_master_id` (`brand_master_id`),
  KEY `brand_access` (`brand_access`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

--
-- Dumping data for table `brand_access`
--


-- --------------------------------------------------------

--
-- Table structure for table `brand_master`
--

DROP TABLE IF EXISTS `brand_master`;
CREATE TABLE IF NOT EXISTS `brand_master` (
  `brand_master_id` int(11) NOT NULL AUTO_INCREMENT,
  `token` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `brand_name` varchar(40) COLLATE utf8_unicode_ci NOT NULL COMMENT 'used for folders and tbl names',
  `real_brand_name` varchar(40) COLLATE utf8_unicode_ci NOT NULL COMMENT 'real name with special chars',
  PRIMARY KEY (`brand_master_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6 ;

--
-- Dumping data for table `brand_master`
--


-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

DROP TABLE IF EXISTS `groups`;
CREATE TABLE IF NOT EXISTS `groups` (
  `groupID` int(11) NOT NULL AUTO_INCREMENT,
  `uid` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `group_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `brand_master_id` int(11) NOT NULL,
  `currency` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `enable` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`groupID`),
  KEY `brand_master_id` (`brand_master_id`),
  KEY `enable` (`enable`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `groups`
--


-- --------------------------------------------------------

--
-- Table structure for table `group_ownership`
--

DROP TABLE IF EXISTS `group_ownership`;
CREATE TABLE IF NOT EXISTS `group_ownership` (
  `groupID` int(11) NOT NULL COMMENT 'there should not be a duplicate record of the same groupID and userID',
  `userID` int(11) NOT NULL COMMENT 'Group Owner',
  KEY `groupID` (`groupID`,`userID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `group_ownership`
--


-- --------------------------------------------------------

--
-- Table structure for table `guid`
--

DROP TABLE IF EXISTS `guid`;
CREATE TABLE IF NOT EXISTS `guid` (
  `guid` int(11) NOT NULL AUTO_INCREMENT,
  `token` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `brand_master_id` int(11) NOT NULL,
  `tbl` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `type` int(11) NOT NULL COMMENT '1=cat, 2=item, 3=home, 4=contact, 5=news, 6=legal, 7=others, 2000=shopping cart',
  PRIMARY KEY (`guid`),
  UNIQUE KEY `token` (`token`),
  KEY `brand_master_id` (`brand_master_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2500007 ;

--
-- Dumping data for table `guid`
--


-- --------------------------------------------------------

--
-- Table structure for table `module_access`
--

DROP TABLE IF EXISTS `module_access`;
CREATE TABLE IF NOT EXISTS `module_access` (
  `index` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) NOT NULL,
  `mod_name` text COLLATE utf8_unicode_ci NOT NULL,
  `access` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`index`),
  KEY `userID` (`userID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=65 ;

--
-- Dumping data for table `module_access`
--

INSERT INTO `module_access` (`index`, `userID`, `mod_name`, `access`) VALUES
(1, 1, 'ircp_settings.php', 1),
(2, 1, 'language_manager.php', 1),
(3, 1, 'brand_page_links.php', 0),
(4, 1, 'specification_search_manager.php', 1),
(5, 1, 'edit_profile.php', 0),
(6, 1, 'signup.php', 1),
(7, 1, 'gmcp.php', 0),
(8, 1, 'manage_cats.php', 1),
(9, 1, 'search.php', 1),
(10, 1, 'one_time_password.php', 1),
(11, 1, 'dealer_manager.php', 1),
(12, 1, 'news_manager.php', 1),
(13, 1, 'manage_images.php', 1),
(14, 1, 'module_access.php', 0),
(15, 1, 'group_manager.php', 1),
(16, 1, 'bulk_upload_longines.php', 0),
(17, 1, 'manufacturer_signup.php', 0),
(18, 1, 'bulk_upload_tacori.php', 0),
(19, 1, 'send_password.php', 0),
(20, 1, 'dealer_settings.php', 0),
(21, 1, 'home.php', 0),
(22, 1, 'gmcp.php.20120102', 0),
(23, 1, 'brand_access.php', 0),
(24, 1, 'manage_brands.php', 0),
(25, 1, 'edit_item.php', 1),
(26, 1, 'bulk_upload.php', 0),
(27, 1, 'brand_settings.php', 1),
(28, 1, 'manage_users_admin.php', 0),
(29, 1, 'ishowcase_auto_setup.php', 0),
(58, 1, 'quote_request_manager.php', 0),
(59, 1, 'manage_customers.php', 0),
(60, 1, 'shopping_cart_manager.php', 1),
(64, 1, 'auto_updates.php', 1),
(65, 1, 'manage_users_data_provider.php', 1);

-- --------------------------------------------------------

--
-- Table structure for table `owner_verif_`
--

DROP TABLE IF EXISTS `owner_verif_`;
CREATE TABLE IF NOT EXISTS `owner_verif_` (
  `owner_verif_id` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) NOT NULL COMMENT 'Data Provider userID (Owner of verif_name)',
  `verif_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL COMMENT 'belongs to data recipient',
  `notes` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `brand_master_id` int(11) NOT NULL,
  PRIMARY KEY (`owner_verif_id`),
  KEY `userID` (`userID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `owner_verif_`
--


-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `userID` int(11) NOT NULL AUTO_INCREMENT,
  `owner_userID` int(11) NOT NULL DEFAULT '0',
  `enable` int(11) NOT NULL DEFAULT '0',
  `fname` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `lname` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `username` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `password` text COLLATE utf8_unicode_ci NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `company` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`userID`),
  UNIQUE KEY `username_uniq` (`username`),
  KEY `enable` (`enable`),
  KEY `owner_userID` (`owner_userID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`userID`, `owner_userID`, `enable`, `fname`, `lname`, `username`, `password`, `date`, `company`, `phone`, `email`) VALUES
(1, 0, 1, 'admin', 'admin', 'admin', 'dc647eb65e6711e155375218212b3964', '2012-07-01 23:35:35', 'i-Showcase', '888-888-8888', 'test-admin@ishowcaseinc.com');

-- --------------------------------------------------------

--
-- Table structure for table `users_gi`
--

DROP TABLE IF EXISTS `users_gi`;
CREATE TABLE IF NOT EXISTS `users_gi` (
  `userID` int(11) NOT NULL,
  `need_webmaster` tinyint(1) NOT NULL DEFAULT '0',
  `need_assistance` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='General Information for users';

--
-- Dumping data for table `users_gi`
--


-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

DROP TABLE IF EXISTS `user_roles`;
CREATE TABLE IF NOT EXISTS `user_roles` (
  `index` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) NOT NULL DEFAULT '0',
  `admin` int(11) NOT NULL DEFAULT '0',
  `data_provider` int(11) NOT NULL DEFAULT '0',
  `data_recipient` int(11) NOT NULL DEFAULT '0',
  `group_manager` int(11) NOT NULL DEFAULT '0',
  `front_end` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`index`),
  UNIQUE KEY `userID` (`userID`),
  KEY `admin` (`admin`),
  KEY `data_provider` (`data_provider`),
  KEY `data_recipient` (`data_recipient`),
  KEY `group_manager` (`group_manager`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

--
-- Dumping data for table `user_roles`
--

INSERT INTO `user_roles` (`index`, `userID`, `admin`, `data_provider`, `data_recipient`, `group_manager`, `front_end`) VALUES
(1, 1, 1, 1, 1, 0,0);


            CREATE TABLE `promotions_types` (
              `promo_type_id` int(11) NOT NULL AUTO_INCREMENT,
              `apply_to` int(11) NOT NULL DEFAULT '1' COMMENT '1:shopping cart, 2:promotion,3:product',
              `promo_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
              `status` int(11) NOT NULL DEFAULT '1' COMMENT '0: disable 1:enable',
              PRIMARY KEY (`promo_type_id`)
            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

                INSERT INTO `promotions_types` (`promo_type_id`, `apply_to`, `promo_name`, `status`) VALUES
                (1, 1, 'Free Shipping', 1),
                (2, 1, 'Subtotal', 1),
                (3, 3, '% OFF (Product)', 0),
                (4, 1, '$ OFF', 1),
                (5, 3, 'Buy X Get Y Free', 1),
                (6, 2, 'Promotion Codes', 1),
                (7, 1, 'Flate rate shipping', 1),
                (8, 3, 'Flate rate shipping (Per Item)', 0),
                (9, 3, '$ OFF (Product)', 0),
                (10, 1, '% OFF', 1),
                (11, 3, 'Promotion Codes on Item (Flat Rate)', 0),
                (12, 3, 'Promotion Codes on Item (%)', 0),
                ('13', '2', 'Promotion Codes (%)', '0');

      CREATE TABLE IF NOT EXISTS `promotions` (
        `promo_id` int(11) NOT NULL AUTO_INCREMENT,
        `promo_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
        `promo_type_id` int(11) NOT NULL,
        `date` date NOT NULL,
        `expiration` date DEFAULT NULL,
        `customer_list_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
        `state_list_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
        `country_list_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
        `max_uses` int(11) DEFAULT NULL,
        `promo_values` int(11) DEFAULT NULL,
        `subtotal` int(11) DEFAULT NULL,
        `guid_list_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
        `promo_code_list_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
        `status` int(11) NOT NULL COMMENT '0:disable 1:enable 2:pending' DEFAULT '2',
        PRIMARY KEY (`promo_id`),
        KEY `promo_type_id` (`promo_type_id`),
        KEY `promo_name` (`promo_name`),
        KEY `customer_list_id` (`customer_list_id`),
        KEY `state_list_id` (`state_list_id`),
        KEY `status` (`status`),
        KEY `guid_list_id` (`guid_list_id`),
        KEY `promo_code_list_id` (`promo_code_list_id`)
      ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

      CREATE TABLE IF NOT EXISTS `promotions_guid_list` (
        `guid_list_id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL COMMENT 'List name',
        `guid_list` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'guid list comma separated',
        `status` int(11) NOT NULL DEFAULT '1' COMMENT '1:Enable 0:Disable',
        PRIMARY KEY (`guid_list_id`)
      ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
