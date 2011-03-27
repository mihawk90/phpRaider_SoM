ALTER TABLE `phpraider_announcements`
	DROP `announcement_poster`,
	CHANGE `announcement_timestamp` `announcement_timestamp` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;

ALTER TABLE `phpraider_raid`
	CHANGE `use_icon` `use_icon` int(1) NOT NULL default '0',
	CHANGE `use_name` `use_name` int(1) NOT NULL default '0';
