DROP TABLE IF EXISTS `phpraider_raid_templates_limits`;
CREATE TABLE `phpraider_raid_templates_limits` (
  `raid_id` int(11) NOT NULL default '0',
  `role_id` int(11) NOT NULL default '0',
  `raid_limit` int(11) NOT NULL default '0'
) TYPE=InnoDB;

ALTER TABLE `phpraider_config` ADD UNIQUE ( `name` );
ALTER TABLE `phpraider_config_auth` ADD UNIQUE ( `name` );