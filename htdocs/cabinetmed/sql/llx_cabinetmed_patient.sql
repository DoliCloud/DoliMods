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

CREATE TABLE llx_cabinetmed_patient (
  rowid              integer AUTO_INCREMENT PRIMARY KEY,
  note_antemed       text,
  note_antechirgen   text,
  note_antechirortho text,
  note_anterhum      text,
  note_other         text,
  note_traitclass    text,
  note_traitallergie text,
  note_traitintol    text,
  note_traitspec     text,
  alert_antemed       smallint,
  alert_antechirgen   smallint,
  alert_antechirortho smallint,
  alert_anterhum      smallint,
  alert_other         smallint,
  alert_traitclass    smallint,
  alert_traitallergie smallint,
  alert_traitintol    smallint,
  alert_traitspec     smallint,
  alert_note          smallint
) ENGINE=innodb;
