
-- --------------------------------------------------------


CREATE TABLE llx_submiteverywhere_targets (
rowid integer AUTO_INCREMENT PRIMARY KEY,
label varchar(64) NOT NULL, 
targetcode varchar(16) NOT NULL, 		-- dig, twitter, facebook, web, ...
langcode varchar(5) default 'en_US', 
url varchar(250) NOT NULL default '', 
login varchar(128), 
pass varchar(128), 
comment varchar(250), 
position INTEGER default 0
) ENGINE = innodb;
