CREATE TABLE tx_libconnect_domain_model_subject (
	uid INT(11) NOT NULL AUTO_INCREMENT,
	pid INT(11) DEFAULT '0' NOT NULL,
	tstamp INT(11) DEFAULT '0' NOT NULL,
	crdate INT(11) DEFAULT '0' NOT NULL,
	cruser_id INT(11) DEFAULT '0' NOT NULL,
	deleted TINYINT(4) DEFAULT '0' NOT NULL,
	hidden TINYINT(4) DEFAULT '0' NOT NULL,
	title TINYTEXT NOT NULL,
	dbisid TINYTEXT NOT NULL,
	ezbnotation TINYTEXT NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);