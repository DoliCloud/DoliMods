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

-- DROP TABLE llx_cabinetmed_exambio
CREATE TABLE llx_cabinetmed_exambio (
  rowid              integer AUTO_INCREMENT PRIMARY KEY,
  fk_soc             integer,
  fk_user            integer,
  dateexam           date NOT NULL,
  resultat           text,
  conclusion         text,
  comment            text,
  suivipr_ad         integer,
  suivipr_ag         integer,
  suivipr_vs         integer,
  suivipr_eva        integer,
  suivipr_das28      double,
  suivipr_err        integer,
  suivisa_fat        integer,
  suivisa_dax        integer,
  suivisa_dpe        integer,
  suivisa_dpa        integer,
  suivisa_rno        integer,
  suivisa_dma        integer,
  suivisa_basdai     double,
  tms                timestamp
) ENGINE=innodb;
