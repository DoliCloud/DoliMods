-- ============================================================================
-- Copyright (C) 2011 Juanjo Menent <jmenent@2byte.es>
--
-- This program is free software; you can redistribute it and/or modify
-- it under the terms of the GNU General Public License as published by
-- the Free Software Foundation; either version 2 of the License, or
-- (at your option) any later version.
--
-- This program is distributed in the hope that it will be useful,
-- but WITHOUT ANY WARRANTY; without even the implied warranty of
-- MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
-- GNU General Public License for more details.
--
-- You should have received a copy of the GNU General Public License
-- along with this program; if not, write to the Free Software
-- Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
--
-- $Id: llx_pos_ticket.key.sql,v 1.1 2011-08-04 16:33:26 jmenent Exp $
-- ============================================================================


ALTER TABLE llx_pos_ticket ADD UNIQUE INDEX idx_ticket_uk_facnumber (facnumber, entity);

ALTER TABLE llx_pos_ticket ADD INDEX idx_ticket_fk_soc (fk_soc);
ALTER TABLE llx_pos_ticket ADD INDEX idx_ticket_fk_user_author (fk_user_author);
ALTER TABLE llx_pos_ticket ADD INDEX idx_ticket_fk_user_valid (fk_user_valid);
ALTER TABLE llx_pos_ticket ADD INDEX idx_ticket_fk_ticket_source (fk_ticket_source);
ALTER TABLE llx_pos_ticket ADD INDEX idx_ticket_fk_place (fk_place);

ALTER TABLE llx_pos_ticket ADD CONSTRAINT fk_ticket_fk_soc            FOREIGN KEY (fk_soc) REFERENCES llx_societe (rowid);
ALTER TABLE llx_pos_ticket ADD CONSTRAINT fk_ticket_fk_user_author    FOREIGN KEY (fk_user_author) REFERENCES llx_user (rowid);
ALTER TABLE llx_pos_ticket ADD CONSTRAINT fk_ticket_fk_user_valid     FOREIGN KEY (fk_user_valid)  REFERENCES llx_user (rowid);
ALTER TABLE llx_pos_ticket ADD CONSTRAINT fk_ticket_fk_ticket_source  FOREIGN KEY (fk_ticket_source) REFERENCES llx_ticket (rowid);
ALTER TABLE llx_pos_ticket ADD CONSTRAINT fk_ticket_fk_place		  FOREIGN KEY (fk_place) REFERENCES llx_pos_places (rowid);