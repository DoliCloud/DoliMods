CREATE TABLE llx_congespayes_users 
(
fk_user     INT( 11 ) NOT NULL PRIMARY KEY,
nb_conges   FLOAT( 5 ) NOT NULL DEFAULT '0'
) 
type=innodb;