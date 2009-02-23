
-- --------------------------------------------------------

-- 
-- 

CREATE TABLE llx_bt_namemap (
info_hash char(40) NOT NULL default "", 
filename varchar(250) NOT NULL default "", 
url varchar(250) NOT NULL default "", 
size bigint(20) unsigned NOT NULL, 
pubDate varchar(25) NOT NULL default "", 
PRIMARY KEY(info_hash)
) ENGINE = innodb;
