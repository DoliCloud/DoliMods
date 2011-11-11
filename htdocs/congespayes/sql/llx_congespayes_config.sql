CREATE TABLE llx_congespayes_config 
(
rowid    INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY,
name     VARCHAR( 255 ) NOT NULL UNIQUE,
value    TEXT NULL
) 
type=innodb;