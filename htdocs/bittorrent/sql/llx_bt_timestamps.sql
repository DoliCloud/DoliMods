
-- --------------------------------------------------------

-- 
-- 
CREATE TABLE llx_bt_timestamps (
info_hash char(40) not null, 
sequence int unsigned not null auto_increment, 
bytes bigint unsigned not null, 
delta smallint unsigned not null, 
primary key(sequence), 
key sorting (info_hash)
) ENGINE = innodb;
