-- phpMyAdmin SQL Dump
-- version 2.9.2
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Jul 23, 2007 at 01:43 PM
-- Server version: 5.0.27
-- PHP Version: 5.2.1
-- 
-- Database: `phpraider`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `phpraider_config`
-- 

DROP TABLE IF EXISTS `phpraider_config`;
CREATE TABLE `phpraider_config` (
  `name` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL
) TYPE=InnoDB;

-- 
-- Dumping data for table `phpraider_config`
-- 

INSERT INTO `phpraider_config` (`name`, `value`) VALUES 
('game', 'WoW'),
('min_level', '0'),
('max_level', '2'),
('min_raiders', '0'),
('max_raiders', '0'),
('multi_class', '0'),
('allow_anonymous', '1'),
('auto_queue', '0'),
('debug_mode', '0'),
('default_group', '0'),
('disable_site', '0'),
('disable_site_message', ''),
('disable_freeze', '0'),
('report_max', '25'),
('language', 'english'),
('template', 'default'),
('authentication', ''),
('date_format', 'm/d/y'),
('time_format', 'h:ia'),
('timezone', '-600'),
('dst', '100'),
('admin_email', 'you@yourdomain.com'),
('admin_name', 'site admin'),
('site_url', 'http://localhost/phpraider/'),
('guild_name', ''),
('guild_server', ''),
('guild_master', '');

-- --------------------------------------------------------

-- 
-- Table structure for table `phpraider_config_auth`
-- 

DROP TABLE IF EXISTS `phpraider_config_auth`;
CREATE TABLE `phpraider_config_auth` (
  `name` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL
) TYPE=InnoDB;

ALTER TABLE `phpraider_raid_templates`
  DROP `start_time`,
  DROP `invite_time`;