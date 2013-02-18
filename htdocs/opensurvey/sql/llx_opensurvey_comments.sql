CREATE TABLE llx_opensurvey_comments (
    id_comment INTEGER unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
    id_sondage CHAR(16) NOT NULL,
    comment text NOT NULL,
    usercomment text
) ENGINE=InnoDB;

