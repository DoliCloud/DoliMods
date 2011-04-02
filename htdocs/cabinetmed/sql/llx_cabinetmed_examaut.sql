-- ============================================================================
-- Copyright (C) 2011 Laurent Destailleur  <eldy@users.sourceforge.net>
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
-- $Id: llx_cabinetmed_examaut.sql,v 1.1 2011/04/02 11:24:27 eldy Exp $
-- ===========================================================================

-- DROP TABLE llx_cabinetmed_examaut
CREATE TABLE llx_cabinetmed_examaut (
  rowid              integer AUTO_INCREMENT PRIMARY KEY,
  fk_soc             integer,
  dateexam           date NOT NULL,
  motifconsprinc     varchar(64),
  diaglesprinc       varchar(64),
  motifconssec       text,
  diaglessec         text,
) ENGINE=innodb;
