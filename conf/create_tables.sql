CREATE TABLE IF NOT EXISTS `reports` (
	`report_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`category_id` SMALLINT NOT NULL,
	`question` VARCHAR(256) NOT NULL,
	`comment` VARCHAR(512)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `categories` (
	`category_id` SMALLINT NOT NULL PRIMARY KEY,
	`category_name` VARCHAR(32) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT IGNORE INTO `categories` VALUES 
	(1, 'default'),
	(2, 'Anime'),
	(3, 'Geography'),
	(4, 'History'),
	(5, 'LOTR-Books'),
	(6, 'LOTR-Movies'),
	(7, 'Movies'),
	(8, 'Naruto'),
	(9, 'ScienceAndNature'),
	(10, 'Simpsons'),
	(11, 'Stargate');
