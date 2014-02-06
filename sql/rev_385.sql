DROP TABLE IF EXISTS `lesturesy_answers`;
CREATE TABLE `lesturesy_answers` (
`lecture` varchar(40) NOT NULL,
`answernumber` int NOT NULL,
`answertext` varchar(60) NOT NULL,
PRIMARY KEY (`answernumber`,`lecture`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

DROP TABLE IF EXISTS `lesturesy_docent_question`;
CREATE TABLE `lesturesy_docent_question` (
`lecture` varchar(40) NOT NULL,
`question` text NOT NULL,
`fetched` tinyint(2) NOT NULL DEFAULT '0',
PRIMARY KEY (`lecture`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;


ALTER TABLE `lesturesy_votes` MODIFY `guid` varchar(60);
ALTER TABLE `lesturesy_lectures` ADD COLUMN `userpassword` varchar(40) NOT NULL;