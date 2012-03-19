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
-- $Id: llx_cabinetmed_patient.sql,v 1.1 2011/02/12 18:36:57 eldy Exp $
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
  sa.alert_antemed       smallint,
  sa.alert_antechirgen   smallint,
  sa.alert_antechirortho smallint,
  sa.alert_anterhum      smallint,
  sa.alert_other         smallint,
  sa.alert_traitclass    smallint,
  sa.alert_traitallergie smallint,
  sa.alert_traitintol    smallint,
  sa.alert_traitspec     smallint,
  sa.alert_note          smallint
) ENGINE=innodb;
