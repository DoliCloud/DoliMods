CREATE TABLE llx_opensurvey_user_studs (
    id_users INTEGER unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(64) NOT NULL,
    id_sondage CHAR(16) NOT NULL,
    reponses text NOT NULL
) ENGINE=InnoDB;
