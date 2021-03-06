RENAME TABLE lesturesy_lectures TO sturesy_lectures;

ALTER TABLE sturesy_lectures 
ADD COLUMN id int NOT NULL AUTO_INCREMENT FIRST, 
CHANGE COLUMN lecture lecture varchar(40) NOT NULL AFTER id,
CHANGE COLUMN password password varchar(40) NOT NULL AFTER lecture,
CHANGE COLUMN answercount answercount tinyint(4) NOT NULL DEFAULT '-1' AFTER password,
CHANGE COLUMN answertype answertype tinyint(4) NOT NULL DEFAULT '-1' AFTER answercount,
CHANGE COLUMN question question text NOT NULL AFTER answertype,
CHANGE COLUMN owner owner varchar(50) NOT NULL AFTER question,
CHANGE COLUMN date date datetime NOT NULL AFTER owner,
CHANGE COLUMN token token char(40) DEFAULT NULL AFTER date,
CHANGE COLUMN userpassword userpassword varchar(40) NOT NULL AFTER token, DROP PRIMARY KEY, ADD PRIMARY KEY (id), add UNIQUE (lecture);

ALTER TABLE sturesy_lectures DROP COLUMN answercount, DROP COLUMN answertype, DROP COLUMN question, DROP COLUMN userpassword;


DROP TABLE IF EXISTS lesturesy_votes;
DROP TABLE IF EXISTS lesturesy_questions;
DROP TABLE IF EXISTS lesturesy_answers;

DROP TABLE IF EXISTS `sturesy_question`;
CREATE TABLE `sturesy_question` (
  `lecture` int(11) NOT NULL,
  `type` varchar(20) NOT NULL,
  `question` text NOT NULL,
  `answers` text NOT NULL,
  `correctanswers` text NOT NULL,
  PRIMARY KEY (`lecture`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

DROP TABLE IF EXISTS `sturesy_votes`;
CREATE TABLE `sturesy_votes` (
  `lid` int(11) NOT NULL,
  `guid` varchar(60) NOT NULL DEFAULT '',
  `vote` text NOT NULL,
  `fetched` tinyint(2) NOT NULL DEFAULT '0',
  `date` datetime NOT NULL,
  PRIMARY KEY (`guid`,`lid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

