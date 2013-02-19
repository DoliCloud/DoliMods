CREATE TABLE llx_opensurvey_sondage (
       id_sondage CHAR(16) NOT NULL,
       commentaires text,
       mail_admin VARCHAR(128),
       nom_admin VARCHAR(64),
       titre text,
       id_sondage_admin CHAR(24),
       date_fin TIMESTAMP,
       format VARCHAR(2),
       mailsonde BOOLEAN DEFAULT '0'
) ENGINE=InnoDB;