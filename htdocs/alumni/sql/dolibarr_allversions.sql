--
-- Script run when an upgrade of Dolibarr is done. Whatever is the Dolibarr version.
--

ALTER TABLE llx_alumni_survey ADD COLUMN ip varchar(64);
ALTER TABLE llx_alumni_survey ADD COLUMN status integer DEFAULT 0;
