
-- --------------------------------------------------------

-- 
-- 

CREATE TABLE llx_ns_targets (
rowid INTEGER autoincrement,
label varchar(64) NOT NULL, 
targetcode varchar(16) NOT NULL, 
langcode varchar(5) default 'en_US', 
url varchar(250) NOT NULL default "", 
login varchar(128), 
pass varchar(128), 
position INTEGER default 0, 
) ENGINE = innodb;
