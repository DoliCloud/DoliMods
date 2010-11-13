-- ===================================================================
-- Copyright (C) 2010 Laurent Destailleur <eldy@users.sourceforge.net>
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
-- $Id: llx_submiteverywhere_targets.sql,v 1.2 2010/11/13 19:50:56 eldy Exp $
-- ===================================================================


CREATE TABLE llx_submiteverywhere_targets 
(
rowid integer AUTO_INCREMENT PRIMARY KEY,
label varchar(64) NOT NULL, 
targetcode varchar(16) NOT NULL,
langcode varchar(5) default 'en_US', 
url varchar(250) NOT NULL default '', 
login varchar(128), 
pass varchar(128), 
comment varchar(250), 
position INTEGER default 0
) ENGINE = innodb;
