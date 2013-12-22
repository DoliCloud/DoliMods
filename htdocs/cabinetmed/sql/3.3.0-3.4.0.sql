--
-- Be carefull to requests order.
-- This file must be loaded by calling /install/index.php page
-- when current version is 3.4.0 or higher. 
--
-- To rename a table:       ALTER TABLE llx_table RENAME TO llx_table_new;
-- To add a column:         ALTER TABLE llx_table ADD COLUMN newcol varchar(60) NOT NULL DEFAULT '0' AFTER existingcol;
-- To rename a column:      ALTER TABLE llx_table CHANGE COLUMN oldname newname varchar(60);
-- To drop a column:        ALTER TABLE llx_table DROP COLUMN oldname;
-- To change type of field: ALTER TABLE llx_table MODIFY COLUMN name varchar(60);
-- To drop a foreign key:   ALTER TABLE llx_table DROP FOREIGN KEY fk_name;
-- To restrict request to Mysql version x.y use -- VMYSQLx.y
-- To restrict request to Pgsql version x.y use -- VPGSQLx.y


-- -- VPGSQL8.2 DELETE FROM llx_usergroup_user      WHERE fk_user      NOT IN (SELECT rowid from llx_user);
-- -- VMYSQL4.1 DELETE FROM llx_usergroup_user      WHERE fk_usergroup NOT IN (SELECT rowid from llx_usergroup);

DELETE from llx_const where name='MAIN_FORCETHEMEDIR';
DELETE from llx_const where name='MAIN_MENUFRONT_SMARTPHONE_FORCED';
DELETE from llx_const where name='MAIN_MENUFRONT_STANDARD_FORCED';
DELETE from llx_const where name='MAIN_MENU_SMARTPHONE_FORCED';
DELETE from llx_const where name='MAIN_MENU_STANDARD_FORCED';

DELETE from llx_const where name='SOCIETE_DISABLE_BUILDDOC';
DELETE from llx_const where name='SOCIETE_DISABLE_BANKACCOUNT';
DELETE from llx_const where name='SOCIETE_DISABLE_CONTACTS';
DELETE from llx_const where name='SOCIETE_DISABLE_PARENTCOMPANY';


-- + duplicate table llx_societe into llx_cabinetmed_societe
-- CREATE TABLE llx_cabinetmed_societe SELECT * FROM llx_societe;
-- VMYSQL4.1 CREATE TABLE llx_cabinetmed_societe LIKE llx_societe;
--INSERT INTO llx_cabinetmed_societe SELECT * FROM llx_societe;
