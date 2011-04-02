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
-- $Id: llx_cabinetmed_exambio.sql,v 1.1 2011/04/02 11:24:27 eldy Exp $
-- ===========================================================================

-- DROP TABLE llx_cabinetmed_exambio
CREATE TABLE llx_cabinetmed_exambio (
  rowid              integer AUTO_INCREMENT PRIMARY KEY,
  fk_soc             integer,
  dateexam           date NOT NULL,
  resultat           text,
  conclusion         text,
  comment            text,
  suivipr_ad         integer,
  suivipr_ag         integer,
  suivipr_vs         integer,
  suivipr_eva        integer,
  suivipr_err        integer,
  --suivipr_das        double(24,8),
  suivisa_fat        integer,
  suivisa_dax        integer,
  suivisa_dpe        integer,
  suivisa_dpa        integer,
  suivisa_rno        integer,
  suivisa_dma        integer,
  suivisa_basdai     integer
) ENGINE=innodb;
