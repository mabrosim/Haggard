-- phpMyAdmin SQL Dump
-- version 3.5.8
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 12, 2014 at 04:22 PM
-- Server version: 5.1.73-log
-- PHP Version: 5.3.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `haggard`
--
CREATE DATABASE `haggard` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `haggard`;

-- --------------------------------------------------------

--
-- Table structure for table `board`
--
-- Creation: Nov 26, 2013 at 07:33 AM
--

CREATE TABLE IF NOT EXISTS `board` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT 'Haggard board v2.0',
  `url` text,
  `email` varchar(255) DEFAULT NULL,
  `timezone` varchar(255) DEFAULT 'Europe/Helsinki',
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=170 ;

-- --------------------------------------------------------

--
-- Table structure for table `board_activity_stat`
--
-- Creation: Nov 26, 2013 at 07:49 AM
--

CREATE TABLE IF NOT EXISTS `board_activity_stat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `board_id` int(10) unsigned NOT NULL,
  `num` int(11) NOT NULL,
  `date` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `board_id` (`board_id`),
  KEY `date` (`date`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=58189 ;

-- --------------------------------------------------------

--
-- Table structure for table `board_setting`
--
-- Creation: Nov 26, 2013 at 07:49 AM
--

CREATE TABLE IF NOT EXISTS `board_setting` (
  `board_id` int(10) unsigned NOT NULL,
  `data` varchar(255) DEFAULT NULL,
  `value` varchar(255) DEFAULT '0',
  UNIQUE KEY `board_setting` (`board_id`,`data`),
  KEY `data` (`data`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `component`
--
-- Creation: Nov 26, 2013 at 07:33 AM
--

CREATE TABLE IF NOT EXISTS `component` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `board_id` int(10) unsigned DEFAULT NULL,
  `name` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `board_id` (`board_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=575 ;

-- --------------------------------------------------------

--
-- Table structure for table `cycle`
--
-- Creation: Nov 26, 2013 at 07:50 AM
--

CREATE TABLE IF NOT EXISTS `cycle` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `board_id` int(10) unsigned NOT NULL,
  `data` varchar(255) DEFAULT NULL,
  `start` datetime DEFAULT NULL,
  `stop` datetime DEFAULT NULL,
  `wip_limit` int(11) DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `board_id` (`board_id`),
  KEY `start` (`start`),
  KEY `active` (`active`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=203 ;

-- --------------------------------------------------------

--
-- Table structure for table `cycle_stat`
--
-- Creation: Nov 26, 2013 at 07:33 AM
--

CREATE TABLE IF NOT EXISTS `cycle_stat` (
  `cycle_id` int(10) unsigned NOT NULL,
  `phase_id` int(11) NOT NULL,
  `wip` int(11) NOT NULL,
  `created` datetime DEFAULT NULL,
  KEY `cycle_phase` (`cycle_id`,`phase_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `group_permission`
--
-- Creation: Nov 26, 2013 at 07:33 AM
--

CREATE TABLE IF NOT EXISTS `group_permission` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `permission_id` int(10) unsigned NOT NULL,
  `group_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `group_permission` (`permission_id`,`group_id`),
  KEY `group_id` (`group_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=414 ;

-- --------------------------------------------------------

--
-- Table structure for table `log`
--
-- Creation: Nov 26, 2013 at 07:33 AM
--

CREATE TABLE IF NOT EXISTS `log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `board_id` int(10) unsigned NOT NULL,
  `data` longtext,
  `date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `board_id` (`board_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=148276 ;

-- --------------------------------------------------------

--
-- Table structure for table `message`
--
-- Creation: Nov 26, 2013 at 07:33 AM
--

CREATE TABLE IF NOT EXISTS `message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `topic_id` int(11) NOT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `message` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `topic_id` (`topic_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=21 ;

-- --------------------------------------------------------

--
-- Table structure for table `message_topic`
--
-- Creation: Nov 26, 2013 at 07:33 AM
--

CREATE TABLE IF NOT EXISTS `message_topic` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `board_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `board_id` (`board_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=28 ;

-- --------------------------------------------------------

--
-- Table structure for table `pagegen`
--
-- Creation: Nov 26, 2013 at 07:33 AM
--

CREATE TABLE IF NOT EXISTS `pagegen` (
  `board_id` int(10) unsigned NOT NULL,
  `id` int(11) DEFAULT NULL,
  PRIMARY KEY (`board_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `permission`
--
-- Creation: Dec 12, 2013 at 07:12 AM
--

CREATE TABLE IF NOT EXISTS `permission` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `data` varchar(45) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `data` (`data`),
  KEY `data_2` (`data`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=21 ;

-- --------------------------------------------------------

--
-- Table structure for table `personal_setting`
--
-- Creation: Nov 26, 2013 at 07:33 AM
--

CREATE TABLE IF NOT EXISTS `personal_setting` (
  `user_id` int(10) unsigned NOT NULL,
  `setting` varchar(255) DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL,
  UNIQUE KEY `user_setting` (`user_id`,`setting`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `phase`
--
-- Creation: Nov 26, 2013 at 07:33 AM
--

CREATE TABLE IF NOT EXISTS `phase` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `board_id` int(10) unsigned NOT NULL,
  `name` varchar(32) DEFAULT NULL,
  `css` varchar(32) DEFAULT NULL,
  `help` text,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `force_comment` varchar(64) DEFAULT NULL,
  `wip_limit` int(11) NOT NULL DEFAULT '0',
  `ticket_limit` int(11) NOT NULL DEFAULT '0',
  `notify_empty` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `board_id` (`board_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1531 ;

-- --------------------------------------------------------

--
-- Table structure for table `phase_day_stat`
--
-- Creation: Nov 26, 2013 at 07:33 AM
--

CREATE TABLE IF NOT EXISTS `phase_day_stat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `board_id` int(10) unsigned NOT NULL,
  `phase` int(11) NOT NULL,
  `num` int(11) NOT NULL,
  `date` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `board_id` (`board_id`),
  KEY `phase` (`phase`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=416845 ;

-- --------------------------------------------------------

--
-- Table structure for table `phase_email_notification`
--
-- Creation: Nov 26, 2013 at 07:33 AM
--

CREATE TABLE IF NOT EXISTS `phase_email_notification` (
  `phase_id` int(11) NOT NULL,
  `group_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `phase_group_subscription` (`phase_id`,`group_id`),
  KEY `group_id` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `phase_release`
--
-- Creation: Nov 26, 2013 at 07:33 AM
--

CREATE TABLE IF NOT EXISTS `phase_release` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `board_id` int(10) unsigned NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `released` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `board_id` (`board_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=32 ;

-- --------------------------------------------------------

--
-- Table structure for table `phase_subscription`
--
-- Creation: Nov 26, 2013 at 07:33 AM
--

CREATE TABLE IF NOT EXISTS `phase_subscription` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `phase_id` int(11) NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `phase_user_subscription` (`phase_id`,`user_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=221 ;

-- --------------------------------------------------------

--
-- Table structure for table `release_ticket`
--
-- Creation: Nov 26, 2013 at 07:33 AM
--

CREATE TABLE IF NOT EXISTS `release_ticket` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `release_id` int(10) unsigned NOT NULL,
  `ticket_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `release_id` (`release_id`),
  KEY `ticket_id` (`ticket_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1052 ;

-- --------------------------------------------------------

--
-- Table structure for table `ticket`
--
-- Creation: Nov 26, 2013 at 08:01 AM
--

CREATE TABLE IF NOT EXISTS `ticket` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `board_id` int(10) unsigned NOT NULL,
  `data` varchar(255) NOT NULL,
  `info` text,
  `responsible` int(10) unsigned DEFAULT NULL,
  `wip` int(11) DEFAULT NULL,
  `cycle` int(10) unsigned NOT NULL DEFAULT '0',
  `phase` int(11) NOT NULL,
  `priority` int(11) DEFAULT '0',
  `reference_id` text,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `component` int(10) unsigned DEFAULT NULL,
  `last_change` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ticket_poll` (`phase`,`active`,`deleted`),
  KEY `board_id` (`board_id`),
  KEY `responsible` (`responsible`),
  KEY `component` (`component`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=24220 ;

-- --------------------------------------------------------

--
-- Table structure for table `ticket_comment`
--
-- Creation: Nov 26, 2013 at 07:33 AM
--

CREATE TABLE IF NOT EXISTS `ticket_comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `data` text,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ticket_id` (`ticket_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=17034 ;

-- --------------------------------------------------------

--
-- Table structure for table `ticket_email_subscription`
--
-- Creation: Nov 26, 2013 at 07:33 AM
--

CREATE TABLE IF NOT EXISTS `ticket_email_subscription` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_id` int(10) unsigned NOT NULL,
  `email` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ticket_email` (`ticket_id`,`email`),
  KEY `ticket_id` (`ticket_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ticket_history`
--
-- Creation: Nov 26, 2013 at 07:33 AM
--

CREATE TABLE IF NOT EXISTS `ticket_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `data` text,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ticket_id` (`ticket_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=77518 ;

-- --------------------------------------------------------

--
-- Table structure for table `ticket_link`
--
-- Creation: Nov 26, 2013 at 07:33 AM
--

CREATE TABLE IF NOT EXISTS `ticket_link` (
  `parent` int(10) unsigned NOT NULL,
  `child` int(10) unsigned NOT NULL,
  UNIQUE KEY `parent_child` (`parent`,`child`),
  KEY `child` (`child`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ticket_stat`
--
-- Creation: Nov 26, 2013 at 07:33 AM
--

CREATE TABLE IF NOT EXISTS `ticket_stat` (
  `ticket_id` int(10) unsigned NOT NULL,
  `cycle_id` int(10) unsigned NOT NULL,
  `old_phase` int(11) NOT NULL,
  `new_phase` int(11) NOT NULL,
  `created` datetime DEFAULT NULL,
  KEY `ticket_id` (`ticket_id`),
  KEY `created` (`created`),
  KEY `cycle_id` (`cycle_id`),
  KEY `old_phase` (`old_phase`),
  KEY `new_phase` (`new_phase`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ticket_subscription`
--
-- Creation: Nov 26, 2013 at 07:33 AM
--

CREATE TABLE IF NOT EXISTS `ticket_subscription` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ticket_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ticket_user_subscription` (`ticket_id`,`user_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=301 ;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--
-- Creation: Nov 26, 2013 at 07:33 AM
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `noe_account` varchar(8) DEFAULT NULL,
  `type` enum('USER','SYSTEM_ADMIN') NOT NULL DEFAULT 'USER',
  `alias` varchar(255) NOT NULL,
  `timezone` varchar(255) NOT NULL DEFAULT 'Europe/Helsinki',
  `nokiasite` varchar(255) NOT NULL,
  `last_login` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_email_username` (`email`,`noe_account`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1029 ;

-- --------------------------------------------------------

--
-- Table structure for table `user_board`
--
-- Creation: Nov 26, 2013 at 07:33 AM
--

CREATE TABLE IF NOT EXISTS `user_board` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `board_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_board` (`user_id`,`board_id`),
  KEY `user_id` (`user_id`),
  KEY `board_id` (`board_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1968 ;

-- --------------------------------------------------------

--
-- Table structure for table `user_day_stat`
--
-- Creation: Nov 26, 2013 at 07:33 AM
--

CREATE TABLE IF NOT EXISTS `user_day_stat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `board_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `num` int(11) NOT NULL,
  `date` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `board_id` (`board_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=614331 ;

-- --------------------------------------------------------

--
-- Table structure for table `user_group`
--
-- Creation: Nov 26, 2013 at 07:33 AM
--

CREATE TABLE IF NOT EXISTS `user_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `board_id` int(10) unsigned NOT NULL,
  `name` varchar(45) DEFAULT NULL,
  `description` text,
  PRIMARY KEY (`id`),
  KEY `board_id` (`board_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=91 ;

-- --------------------------------------------------------

--
-- Table structure for table `user_group_link`
--
-- Creation: Nov 26, 2013 at 07:33 AM
--

CREATE TABLE IF NOT EXISTS `user_group_link` (
  `user_id` int(10) unsigned NOT NULL,
  `group_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`group_id`),
  KEY `group_id` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `user_notification`
--
-- Creation: Nov 26, 2013 at 07:33 AM
--

CREATE TABLE IF NOT EXISTS `user_notification` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `board_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `type` varchar(16) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `link` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `status` enum('read','unread') CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT 'unread',
  `time` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `board_id` (`board_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=100443 ;

-- --------------------------------------------------------

--
-- Table structure for table `user_permission`
--
-- Creation: Nov 26, 2013 at 07:33 AM
--

CREATE TABLE IF NOT EXISTS `user_permission` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `board_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `permission_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `board_user_index` (`board_id`,`user_id`,`permission_id`),
  KEY `user_id` (`user_id`),
  KEY `permission_id` (`permission_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13077 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `board_activity_stat`
--
ALTER TABLE `board_activity_stat`
  ADD CONSTRAINT `board_activity_stat_ibfk_1` FOREIGN KEY (`board_id`) REFERENCES `board` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `board_setting`
--
ALTER TABLE `board_setting`
  ADD CONSTRAINT `board_setting_ibfk_1` FOREIGN KEY (`board_id`) REFERENCES `board` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `component`
--
ALTER TABLE `component`
  ADD CONSTRAINT `component_ibfk_1` FOREIGN KEY (`board_id`) REFERENCES `board` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `cycle`
--
ALTER TABLE `cycle`
  ADD CONSTRAINT `cycle_ibfk_1` FOREIGN KEY (`board_id`) REFERENCES `board` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `group_permission`
--
ALTER TABLE `group_permission`
  ADD CONSTRAINT `group_permission_ibfk_1` FOREIGN KEY (`permission_id`) REFERENCES `permission` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `group_permission_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `user_group` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `log`
--
ALTER TABLE `log`
  ADD CONSTRAINT `log_ibfk_1` FOREIGN KEY (`board_id`) REFERENCES `board` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `message`
--
ALTER TABLE `message`
  ADD CONSTRAINT `message_ibfk_1` FOREIGN KEY (`topic_id`) REFERENCES `message_topic` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `message_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `message_topic`
--
ALTER TABLE `message_topic`
  ADD CONSTRAINT `message_topic_ibfk_1` FOREIGN KEY (`board_id`) REFERENCES `board` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `message_topic_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `pagegen`
--
ALTER TABLE `pagegen`
  ADD CONSTRAINT `pagegen_ibfk_1` FOREIGN KEY (`board_id`) REFERENCES `board` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `personal_setting`
--
ALTER TABLE `personal_setting`
  ADD CONSTRAINT `personal_setting_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `phase`
--
ALTER TABLE `phase`
  ADD CONSTRAINT `phase_ibfk_1` FOREIGN KEY (`board_id`) REFERENCES `board` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `phase_day_stat`
--
ALTER TABLE `phase_day_stat`
  ADD CONSTRAINT `phase_day_stat_ibfk_1` FOREIGN KEY (`board_id`) REFERENCES `board` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `phase_day_stat_ibfk_2` FOREIGN KEY (`phase`) REFERENCES `phase` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `phase_email_notification`
--
ALTER TABLE `phase_email_notification`
  ADD CONSTRAINT `phase_email_notification_ibfk_1` FOREIGN KEY (`phase_id`) REFERENCES `phase` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `phase_email_notification_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `user_group` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `phase_release`
--
ALTER TABLE `phase_release`
  ADD CONSTRAINT `phase_release_ibfk_1` FOREIGN KEY (`board_id`) REFERENCES `board` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `phase_subscription`
--
ALTER TABLE `phase_subscription`
  ADD CONSTRAINT `phase_subscription_ibfk_1` FOREIGN KEY (`phase_id`) REFERENCES `phase` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `phase_subscription_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `release_ticket`
--
ALTER TABLE `release_ticket`
  ADD CONSTRAINT `release_ticket_ibfk_1` FOREIGN KEY (`release_id`) REFERENCES `phase_release` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `release_ticket_ibfk_2` FOREIGN KEY (`ticket_id`) REFERENCES `ticket` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `ticket`
--
ALTER TABLE `ticket`
  ADD CONSTRAINT `ticket_ibfk_1` FOREIGN KEY (`board_id`) REFERENCES `board` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ticket_ibfk_5` FOREIGN KEY (`phase`) REFERENCES `phase` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Constraints for table `ticket_comment`
--
ALTER TABLE `ticket_comment`
  ADD CONSTRAINT `ticket_comment_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `ticket` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ticket_comment_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `ticket_email_subscription`
--
ALTER TABLE `ticket_email_subscription`
  ADD CONSTRAINT `ticket_email_subscription_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `ticket` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `ticket_history`
--
ALTER TABLE `ticket_history`
  ADD CONSTRAINT `ticket_history_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `ticket` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ticket_history_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `ticket_link`
--
ALTER TABLE `ticket_link`
  ADD CONSTRAINT `ticket_link_ibfk_1` FOREIGN KEY (`parent`) REFERENCES `ticket` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ticket_link_ibfk_2` FOREIGN KEY (`child`) REFERENCES `ticket` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `ticket_stat`
--
ALTER TABLE `ticket_stat`
  ADD CONSTRAINT `ticket_stat_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `ticket` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ticket_stat_ibfk_2` FOREIGN KEY (`cycle_id`) REFERENCES `cycle` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ticket_stat_ibfk_3` FOREIGN KEY (`old_phase`) REFERENCES `phase` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ticket_stat_ibfk_4` FOREIGN KEY (`new_phase`) REFERENCES `phase` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `ticket_subscription`
--
ALTER TABLE `ticket_subscription`
  ADD CONSTRAINT `ticket_subscription_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `ticket` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ticket_subscription_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_board`
--
ALTER TABLE `user_board`
  ADD CONSTRAINT `user_board_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_board_ibfk_2` FOREIGN KEY (`board_id`) REFERENCES `board` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_day_stat`
--
ALTER TABLE `user_day_stat`
  ADD CONSTRAINT `user_day_stat_ibfk_1` FOREIGN KEY (`board_id`) REFERENCES `board` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_day_stat_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_group`
--
ALTER TABLE `user_group`
  ADD CONSTRAINT `user_group_ibfk_1` FOREIGN KEY (`board_id`) REFERENCES `board` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_group_link`
--
ALTER TABLE `user_group_link`
  ADD CONSTRAINT `user_group_link_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_group_link_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `user_group` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_notification`
--
ALTER TABLE `user_notification`
  ADD CONSTRAINT `user_notification_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_notification_ibfk_2` FOREIGN KEY (`board_id`) REFERENCES `board` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_permission`
--
ALTER TABLE `user_permission`
  ADD CONSTRAINT `user_permission_ibfk_1` FOREIGN KEY (`board_id`) REFERENCES `board` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_permission_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_permission_ibfk_3` FOREIGN KEY (`permission_id`) REFERENCES `permission` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
