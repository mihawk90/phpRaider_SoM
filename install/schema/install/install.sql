-- -----------------------------------------------------
-- Table `phpraider_announcements`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `phpraider_announcements` ;
CREATE  TABLE IF NOT EXISTS `phpraider_announcements` (
  `announcement_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `announcement_title` VARCHAR(45) NOT NULL DEFAULT '' ,
  `announcement_msg` TEXT NOT NULL ,
  `announcement_timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ,
  `profile_id` INT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`announcement_id`) )
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Dumping data for table `phpraider_announcements`
-- -----------------------------------------------------

INSERT INTO `phpraider_announcements` (`announcement_id`, `announcement_title`, `announcement_msg`, `profile_id`) VALUES 
(1, 'phpRaider Install Successful!', 'Congratulations! You have successfully completely an installation of phpRaider. Your first step is to visit the <a href="index.php?option=com_configuration">configuration</a> page to customize your installation and to install a new game package. All game packages, support, information, and documentation can be found at <a href="http://www.phpraider.com">http://www.phpraider.com</a>. Enjoy phpRaider!\r\n\r\n- SpiffyJr ', 1);

-- -----------------------------------------------------
-- Table `phpraider_attribute`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `phpraider_attribute` ;
CREATE  TABLE IF NOT EXISTS `phpraider_attribute` (
  `attribute_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `att_name` VARCHAR(255) NOT NULL DEFAULT '' ,
  `att_type` VARCHAR(45) NOT NULL DEFAULT '' ,
  `att_min` INT NOT NULL DEFAULT 0 ,
  `att_max` INT NOT NULL DEFAULT 0 ,
  `att_hover` TINYINT(1)  NOT NULL DEFAULT FALSE ,
  `att_show` TINYINT(1)  NOT NULL DEFAULT FALSE ,
  `att_icon` VARCHAR(45) NOT NULL DEFAULT '' ,
  PRIMARY KEY (`attribute_id`) )
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `phpraider_attribute_data`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `phpraider_attribute_data` ;
CREATE  TABLE IF NOT EXISTS `phpraider_attribute_data` (
  `attribute_id` INT UNSIGNED NOT NULL DEFAULT 0,
  `character_id` INT UNSIGNED NOT NULL DEFAULT 0,
  `att_value` VARCHAR(255) NOT NULL DEFAULT '' )
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `phpraider_character`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `phpraider_character` ;
CREATE  TABLE IF NOT EXISTS `phpraider_character` (
  `character_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `profile_id` INT UNSIGNED NOT NULL DEFAULT 0,
  `class_id` INT UNSIGNED NOT NULL DEFAULT 0,
  `role_id` INT UNSIGNED NOT NULL DEFAULT 0,
  `gender_id` INT UNSIGNED NOT NULL DEFAULT 0,
  `guild_id` INT UNSIGNED NOT NULL DEFAULT 0,
  `race_id` INT UNSIGNED NOT NULL DEFAULT 0,
  `char_level` INT UNSIGNED NOT NULL DEFAULT 0,
  `char_name` VARCHAR(45) NOT NULL DEFAULT '',
  PRIMARY KEY (`character_id`) )
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `phpraider_class`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `phpraider_class` ;
CREATE  TABLE IF NOT EXISTS `phpraider_class` (
  `class_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `class_color` VARCHAR(45) NOT NULL DEFAULT '' ,
  `class_name` VARCHAR(45) NOT NULL DEFAULT '' ,
  PRIMARY KEY (`class_id`) )
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `phpraider_config`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `phpraider_config` ;
CREATE  TABLE IF NOT EXISTS `phpraider_config` (
  `name` VARCHAR(255) NOT NULL DEFAULT '' ,
  `value` VARCHAR(255) NOT NULL DEFAULT '' ,
  UNIQUE INDEX `name_UNIQUE` (`name` ASC) )
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Dumping data for table `phpraider_config`
-- -----------------------------------------------------
INSERT INTO `phpraider_config` (`name`, `value`) VALUES 
('game', ''),
('min_level', '0'),
('max_level', '0'),
('min_raiders', '0'),
('max_raiders', '0'),
('multi_class', '0'),
('allow_anonymous', '0'),
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

-- -----------------------------------------------------
-- Table `phpraider_config_auth`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `phpraider_config_auth` ;
CREATE  TABLE IF NOT EXISTS `phpraider_config_auth` (
  `name` VARCHAR(255) NOT NULL DEFAULT '' ,
  `value` VARCHAR(255) NOT NULL DEFAULT '' ,
  UNIQUE INDEX `name_UNIQUE` (`name` ASC) )
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `phpraider_definitions`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `phpraider_definitions` ;
CREATE  TABLE IF NOT EXISTS `phpraider_definitions` (
  `class_id` INT UNSIGNED NOT NULL DEFAULT 0 ,
  `race_id` INT UNSIGNED NOT NULL DEFAULT 0 )
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `phpraider_gender`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `phpraider_gender` ;
CREATE  TABLE IF NOT EXISTS `phpraider_gender` (
  `gender_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `gender_name` VARCHAR(45) NOT NULL DEFAULT '' ,
  PRIMARY KEY (`gender_id`) )
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `phpraider_groups`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `phpraider_groups` ;
CREATE  TABLE IF NOT EXISTS `phpraider_groups` (
  `group_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `group_name` VARCHAR(45) NOT NULL DEFAULT '' ,
  PRIMARY KEY (`group_id`) )
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Dumping data for table `phpraider_groups`
-- -----------------------------------------------------
INSERT INTO `phpraider_groups` (`group_id`, `group_name`) VALUES 
(1, 'Superuser');

-- -----------------------------------------------------
-- Table `phpraider_guild`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `phpraider_guild` ;
CREATE  TABLE IF NOT EXISTS `phpraider_guild` (
  `guild_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `guild_name` VARCHAR(45) NOT NULL DEFAULT '' ,
  `guild_tag` VARCHAR(45) NOT NULL DEFAULT '' ,
  `guild_master` VARCHAR(45) NOT NULL DEFAULT '' ,
  PRIMARY KEY (`guild_id`) )
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `phpraider_permissions`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `phpraider_permissions` ;
CREATE  TABLE IF NOT EXISTS `phpraider_permissions` (
  `permission_name` VARCHAR(45) NOT NULL DEFAULT '' ,
  `permission_value` TINYINT(1)  NOT NULL DEFAULT FALSE ,
  `group_id` INT UNSIGNED NOT NULL DEFAULT 0)
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Dumping data for table `phpraider_permissions`
-- -----------------------------------------------------
INSERT INTO `phpraider_permissions` (`permission_name`, `permission_value`, `group_id`) VALUES 
('allow_signup', 1, 1),
('view_members', 1, 1),
('view_raids', 1, 1),
('view_roster', 1, 1),
('view_history_own', 1, 1),
('edit_characters_own', 1, 1),
('edit_announcements_own', 1, 1),
('edit_raids_own', 1, 1),
('edit_subscriptions_own', 1, 1),
('allow_backups', 1, 1),
('edit_configuration', 1, 1),
('edit_attributes', 1, 1),
('edit_definitions', 1, 1),
('edit_genders', 1, 1),
('edit_guilds', 1, 1),
('edit_groups', 1, 1),
('edit_meetings', 1, 1),
('edit_permissions', 1, 1),
('edit_roles', 1, 1),
('view_history_any', 1, 1),
('edit_announcements_any', 1, 1),
('edit_characters_any', 1, 1),
('delete_members', 1, 1),
('edit_raids_any', 1, 1),
('edit_subscriptions_any', 1, 1),
('edit_raid_templates', 1, 1);

-- -----------------------------------------------------
-- Table `phpraider_profile`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `phpraider_profile` ;
CREATE  TABLE IF NOT EXISTS `phpraider_profile` (
  `profile_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `username` VARCHAR(45) NOT NULL DEFAULT '' ,
  `password` VARCHAR(45) NOT NULL DEFAULT '' ,
  `join_date` VARCHAR(45) NOT NULL DEFAULT '' ,
  `raids_attended` INT UNSIGNED NOT NULL DEFAULT 0 ,
  `raids_cancelled` INT UNSIGNED NOT NULL DEFAULT 0 ,
  `group_id` INT UNSIGNED NOT NULL DEFAULT 0 ,
  `dst` TINYINT(1)  NOT NULL DEFAULT FALSE ,
  `timezone` VARCHAR(45) NOT NULL DEFAULT '0' ,
  `user_email` VARCHAR(255) NOT NULL DEFAULT '' ,
  PRIMARY KEY (`profile_id`) )
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `phpraider_race`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `phpraider_race` ;
CREATE  TABLE IF NOT EXISTS `phpraider_race` (
  `race_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `race_name` VARCHAR(45) NOT NULL DEFAULT '' ,
  PRIMARY KEY (`race_id`) )
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `phpraider_raid`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `phpraider_raid` ;
CREATE  TABLE IF NOT EXISTS `phpraider_raid` (
  `raid_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `expired` TINYINT(1)  NOT NULL DEFAULT FALSE ,
  `location` VARCHAR(45) NOT NULL DEFAULT '' ,
  `raid_leader` VARCHAR(45) NOT NULL DEFAULT '' ,
  `invite_time` VARCHAR(45) NOT NULL DEFAULT '' ,
  `maximum` TINYINT NOT NULL DEFAULT 0 ,
  `description` TEXT NOT NULL ,
  `start_time` VARCHAR(45) NOT NULL DEFAULT '' ,
  `freeze_time` INT UNSIGNED NOT NULL DEFAULT 0 ,
  `maximum_level` INT UNSIGNED NOT NULL DEFAULT 0 ,
  `minimum_level` INT UNSIGNED NOT NULL DEFAULT 0 ,
  `use_icon` TINYINT(1)  NOT NULL DEFAULT FALSE ,
  `use_name` TINYINT(1)  NOT NULL DEFAULT FALSE ,
  `icon_name` VARCHAR(45) NOT NULL DEFAULT '' ,
  `profile_id` INT UNSIGNED NOT NULL DEFAULT 0 ,
  `recurring` VARCHAR(45) NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`raid_id`) )
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `phpraider_raid_limits`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `phpraider_raid_limits` ;
CREATE  TABLE IF NOT EXISTS `phpraider_raid_limits` (
  `raid_id` INT UNSIGNED NOT NULL DEFAULT 0,
  `role_id` INT UNSIGNED NOT NULL DEFAULT 0,
  `raid_limit` INT UNSIGNED NOT NULL DEFAULT 0)
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `phpraider_raid_templates`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `phpraider_raid_templates` ;
CREATE  TABLE IF NOT EXISTS `phpraider_raid_templates` (
  `raid_template_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `icon_name` VARCHAR(45) NOT NULL DEFAULT '',
  `location` VARCHAR(45) NOT NULL DEFAULT '',
  `maximum` INT UNSIGNED NOT NULL DEFAULT 0,
  `description` TEXT NOT NULL,
  `start_time` CHAR(4) NOT NULL DEFAULT '0000',
  `invite_time` CHAR(4) NOT NULL DEFAULT '0000',
  `freeze_time` INT UNSIGNED NOT NULL DEFAULT 0,
  `minimum_level` INT UNSIGNED NOT NULL DEFAULT 0,
  `maximum_level` INT UNSIGNED NOT NULL DEFAULT 0,
  `use_icon` TINYINT(1)  NOT NULL DEFAULT FALSE,
  `use_name` TINYINT(1)  NOT NULL DEFAULT FALSE,
  PRIMARY KEY (`raid_template_id`) )
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `phpraider_raid_templates_limits`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `phpraider_raid_templates_limits` ;
CREATE  TABLE IF NOT EXISTS `phpraider_raid_templates_limits` (
  `raid_id` INT UNSIGNED NOT NULL DEFAULT 0,
  `role_id` INT UNSIGNED NOT NULL DEFAULT 0,
  `raid_limit` INT UNSIGNED NOT NULL DEFAULT 0 )
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `phpraider_role`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `phpraider_role` ;
CREATE  TABLE IF NOT EXISTS `phpraider_role` (
  `role_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `role_name` VARCHAR(255) NOT NULL DEFAULT '' ,
  `body_color` VARCHAR(255) NOT NULL DEFAULT '' ,
  `header_color` VARCHAR(255) NOT NULL DEFAULT '' ,
  `font_color` VARCHAR(255) NOT NULL DEFAULT '' ,
  PRIMARY KEY (`role_id`) )
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `phpraider_signups`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `phpraider_signups` ;
CREATE  TABLE IF NOT EXISTS `phpraider_signups` (
  `raid_id` INT UNSIGNED NOT NULL DEFAULT 0 ,
  `character_id` INT UNSIGNED NOT NULL DEFAULT 0 ,
  `cancel` TINYINT(1)  NOT NULL DEFAULT FALSE ,
  `queue` TINYINT(1)  NOT NULL DEFAULT FALSE ,
  `profile_id` INT UNSIGNED NOT NULL DEFAULT 0 ,
  `role_id` INT UNSIGNED NOT NULL DEFAULT 0 ,
  `comments` VARCHAR(255) NOT NULL DEFAULT '' ,
  `timestamp` VARCHAR(255) NOT NULL DEFAULT '' ,
  `class_id` INT UNSIGNED NOT NULL DEFAULT 0 )
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `phpraider_subclass`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `phpraider_subclass` ;

CREATE  TABLE IF NOT EXISTS `phpraider_subclass` (
  `character_id` INT UNSIGNED NOT NULL DEFAULT 0 ,
  `class_id` INT UNSIGNED NOT NULL DEFAULT 0 )
ENGINE = InnoDB;
