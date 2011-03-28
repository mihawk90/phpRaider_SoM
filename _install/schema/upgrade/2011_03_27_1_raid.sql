## add RSS Feed-Support Column on raid-table
ALTER TABLE `phpraider_raid`  ADD COLUMN `raid_create_time` VARCHAR(45) NOT NULL AFTER `recurring`;