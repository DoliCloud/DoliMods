
-- --------------------------------------------------------

-- 
-- 
CREATE TABLE llx_bt_summary (
info_hash char(40) NOT NULL default "",
dlbytes bigint unsigned NOT NULL default 0,
seeds int unsigned NOT NULL default 0, 
leechers int unsigned NOT NULL default 0, 
finished int unsigned NOT NULL default 0, 
lastcycle int unsigned NOT NULL default "0", 
lastSpeedCycle int unsigned NOT NULL DEFAULT "0", 
speed bigint unsigned NOT NULL default 0, 
piecelength int(11) NOT NULL default -1, 
numpieces int(11) NOT NULL default 0, 
PRIMARY KEY (info_hash)
) ENGINE = innodb;
