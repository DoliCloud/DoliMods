-- ============================================================================
-- Copyright (C) 2007 Laurent Destailleur  <eldy@users.sourceforge.net>
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

CREATE TABLE llx_surveys_answers (
  rowid             integer AUTO_INCREMENT PRIMARY KEY,
  fk_question       integer NOT NULL,
  ip_adresse        varchar(15) NOT NULL default '',
  datec             date NOT NULL,
  rep1              decimal(6,0) default NULL,
  rep2              decimal(6,0) default NULL,
  rep3              decimal(6,0) default NULL,
  rep4              decimal(6,0) default NULL
)ENGINE=innodb;
