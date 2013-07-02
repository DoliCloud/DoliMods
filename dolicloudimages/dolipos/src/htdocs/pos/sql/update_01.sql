ALTER TABLE llx_pos_ticket ADD fk_place integer DEFAULT 0 AFTER fk_soc;
ALTER TABLE llx_pos_ticket CHANGE fk_place fk_place INT( 11 ) NULL;  
ALTER TABLE llx_pos_ticketdet ADD note TEXT NULL; 
ALTER TABLE llx_pos_cash ADD barcode TINYINT NOT NULL DEFAULT '0' AFTER tactil; 
ALTER TABLE llx_pos_ticketdet ADD localtax1_type INT NULL AFTER localtax1_tx;
ALTER TABLE llx_pos_ticketdet ADD localtax2_type INT NULL AFTER localtax2_tx;  
create table llx_pos_facture(rowid integer AUTO_INCREMENT PRIMARY KEY, fk_cash integer NOT NULL; fk_facture integer NOT NULL);
ALTER TABLE llx_pos_facture ADD fk_control_cash INT NULL;
ALTER TABLE llx_pos_facture ADD fk_place INT NULL;