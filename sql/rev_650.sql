ALTER TABLE `sturesy`.`sturesy_lectures` ADD COLUMN `email` varchar(80) NOT NULL AFTER `owner`;
ALTER TABLE `sturesy`.`sturesy_lectures` ADD UNIQUE `token`(token);