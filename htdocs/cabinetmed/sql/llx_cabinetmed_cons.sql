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

-- DROP TABLE llx_cabinetmed_cons
CREATE TABLE llx_cabinetmed_cons (
  rowid              integer AUTO_INCREMENT PRIMARY KEY,
  fk_soc             integer,
  datecons           date NOT NULL,
  typepriseencharge  varchar(8),
  motifconsprinc     varchar(64),
  diaglesprinc       varchar(64),
  motifconssec       text,
  diaglessec         text,
  hdm                text,
  examenclinique     text,
  examenprescrit     text,
  traitementprescrit text,
  comment            text,
  typevisit          varchar(8) NOT NULL,
  infiltration       varchar(255),
  codageccam         varchar(16),
  montant_cheque     double(24,8),
  montant_espece     double(24,8),
  montant_carte      double(24,8),
  montant_tiers      double(24,8),
  banque             varchar(128),
  date_c             datetime NOT NULL,
  tms                timestamp,
  fk_user            integer,
  fk_user_m          integer,
  fk_agenda			 integer
) ENGINE=innodb;
