-- ============================================================================
-- Copyright (C) 2011 Laurent Destailleur  <eldy@users.sourceforge.net>
--
-- This program is free software; you can redistribute it and/or modify
-- it under the terms of the GNU General Public License as published by
-- the Free Software Foundation; either version 3 of the License, or
-- (at your option) any later version.
--
-- This program is distributed in the hope that it will be useful,
-- but WITHOUT ANY WARRANTY; without even the implied warranty of
-- MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
-- GNU General Public License for more details.
--
-- You should have received a copy of the GNU General Public License
-- along with this program. If not, see <http://www.gnu.org/licenses/>.
-- ===========================================================================

-- DROP TABLE llx_cabinetmed_examaut
CREATE TABLE llx_cabinetmed_examaut (
  rowid              integer AUTO_INCREMENT PRIMARY KEY,
  fk_soc             integer,
  fk_user            integer,
  dateexam           date NOT NULL,
  examprinc          varchar(64),
  examsec            text,
  concprinc          varchar(64),
  concsec            text,
  tms                timestamp
) ENGINE=innodb;
