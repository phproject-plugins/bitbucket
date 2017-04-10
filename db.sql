CREATE TABLE `bitbucket_commit` (
	`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`hash` CHAR(40) NOT NULL,
	`message` VARCHAR(45) NOT NULL,
	`issue_id` INT(10) UNSIGNED NULL,
	`user_id` INT(10) UNSIGNED NULL,
	`insert_date` DATETIME NOT NULL,
	PRIMARY KEY (`id`),
	INDEX `bitbucket_commit_issue_id_idx` (`issue_id` ASC),
	INDEX `bitbucket_commit_hash` (`hash` ASC),
	CONSTRAINT `bitbucket_commit_issue_id` FOREIGN KEY (`issue_id`)
		REFERENCES `issue` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
	CONSTRAINT `bitbucket_commit_user_id` FOREIGN KEY (`user_id`)
		REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
);
