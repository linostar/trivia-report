CREATE TABLE IF NOT EXISTS `reasons` (
	`reason_id` SMALLINT NOT NULL,
	`reason_name` VARCHAR(32) NOT NULL,
	PRIMARY KEY (`reason_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT IGNORE INTO `reasons` VALUES 
	(1, 'Duplicate question'),
	(2, 'Wrong category'),
	(3, 'Incorrect answer'),
	(4, 'Typo in question'),
	(5, 'Typo in answer'),
	(6, 'Muliple possible answers'),
	(99, 'Other');

CREATE TABLE IF NOT EXISTS `reports` (
	`report_id` INT NOT NULL AUTO_INCREMENT,
	`reason_id` SMALLINT NOT NULL DEFAULT 99,
	`question` VARCHAR(256) NOT NULL,
	`comment` VARCHAR(512),
	`state` SMALLINT NOT NULL DEFAULT 0,
	 PRIMARY KEY (`report_id`),
	 INDEX (`reason_id`),
	 FOREIGN KEY (`reason_id`) REFERENCES `reasons` (`reason_id`) ON DELETE SET DEFAULT
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
