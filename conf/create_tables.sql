CREATE TABLE IF NOT EXISTS `categories` (
	`category_id` SMALLINT NOT NULL,
	`category_name` VARCHAR(32) NOT NULL,
	PRIMARY KEY (`category_id`)
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

CREATE TABLE IF NOT EXISTS `reports` (
	`report_id` INT NOT NULL AUTO_INCREMENT,
	`category_id` SMALLINT NOT NULL DEFAULT 1,
	`question` VARCHAR(256) NOT NULL,
	`comment` VARCHAR(512),
	 PRIMARY KEY (`report_id`),
	 INDEX (`category_id`),
	 FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE SET DEFAULT
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
