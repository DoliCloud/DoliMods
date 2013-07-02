-- ============================================================================
-- Copyright (C) 2012 Ferran Marcet <fmarcet@2byte.es>
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


ALTER TABLE llx_pos_places ADD INDEX idx_places_fk_ticket (fk_ticket);

ALTER TABLE llx_pos_ticket ADD CONSTRAINT fk_places_fk_ticket       FOREIGN KEY (fk_ticket) REFERENCES llx_pos_ticket (rowid);