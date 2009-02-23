
-- --------------------------------------------------------

-- 
-- 

CREATE TABLE llx_bt_webseedfiles (
info_hash char(40) default NULL, 
filename char(250) NOT NULL default "",
startpiece int(11) NOT NULL default 0,
endpiece int(11) NOT NULL default 0,
startpieceoffset int(11) NOT NULL default 0,
fileorder int(11) NOT NULL default 0,
UNIQUE KEY fileseq (info_hash,fileorder)
) ENGINE = innodb;
