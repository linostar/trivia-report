CREATE TABLE IF NOT EXISTS `reasons` (
	`reason_id` SMALLINT NOT NULL AUTO_INCREMENT,
	`reason_name` VARCHAR(32) NOT NULL,
	PRIMARY KEY (`reason_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT IGNORE INTO `reasons` VALUES 
	(1, 'Incorrect answer'),
	(2, 'Wrong category'),
	(3, 'Duplicate question'),
	(4, 'Typo in question'),
	(5, 'Typo in answer'),
	(6, 'Multiple possible answers'),
	(7, 'Other');

CREATE TABLE IF NOT EXISTS `reports` (
	`report_id` INT NOT NULL AUTO_INCREMENT,
	`reason_id` SMALLINT NOT NULL DEFAULT 99,
	`theme_id` SMALLINT NOT NULL DEFAULT 1,
	`question` VARCHAR(256) NOT NULL,
	`comment` VARCHAR(512),
	`state` SMALLINT NOT NULL DEFAULT 0,
	`date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	 PRIMARY KEY (`report_id`),
	 INDEX (`reason_id`),
	 FOREIGN KEY (`reason_id`) REFERENCES `reasons` (`reason_id`) ON DELETE SET DEFAULT
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
