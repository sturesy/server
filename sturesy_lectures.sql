

SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `sturesy_lectures`;
CREATE TABLE `sturesy_lectures` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lecture` varchar(40) NOT NULL,
  `password` varchar(40) NOT NULL,
  `owner` varchar(50) NOT NULL,
  `email` varchar(80) NOT NULL,
  `date` datetime NOT NULL,
  `token` char(40) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lecture` (`lecture`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

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