-- ===================================================================
-- Copyright (C) 2010-2011 Laurent Destailleur <eldy@users.sourceforge.net>
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
-- along with this program. If not, see <http://www.gnu.org/licenses/>.
-- ===================================================================


CREATE TABLE llx_monitoring_probes 
(
	rowid integer AUTO_INCREMENT PRIMARY KEY,
	typeprot varchar(16) NOT NULL DEFAULT 'GET' after rowid,	
	title varchar(64) NOT NULL, 
    groupname varchar(64) NULL, 
	url varchar(250) NOT NULL,
	url_params text NULL,
    useproxy integer default 0,
    checkkey varchar(250),	
    maxval integer,  
	frequency integer default 60, 
	active integer default 1,
	status integer default 0,
	lastreset datetime,
	oldesterrortext text,
	oldesterrordate datetime
) ENGINE = innodb;

-- lastreset = last date of status change
-- 