-- ===================================================================
-- Copyright (C) 2010 Laurent Destailleur <eldy@users.sourceforge.net>
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
-- ===================================================================


CREATE TABLE llx_submitew_targets 
(
	rowid integer AUTO_INCREMENT PRIMARY KEY,
	label varchar(64) NOT NULL, 
	targetcode varchar(16) NOT NULL,
	langcode varchar(5) default 'en_US', 
	url varchar(250), 
	login varchar(128), 
	pass varchar(128), 
	comment varchar(250), 
	position INTEGER default 0,
	titlelength integer default 32,
    descshortlength integer default 256,
    desclonglength integer default 2000
) ENGINE = innodb;
