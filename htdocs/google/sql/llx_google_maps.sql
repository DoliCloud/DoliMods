CREATE TABLE llx_google_maps (
	rowid INT NOT NULL AUTO_INCREMENT,
	fk_object INT NOT NULL,
	type_object INT NOT NULL,
	latitude FLOAT NOT NULL,
	longitude FLOAT NOT NULL,
	PRIMARY KEY (rowid)
) ENGINE = InnoDB;
