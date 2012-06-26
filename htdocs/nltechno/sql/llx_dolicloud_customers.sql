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


CREATE TABLE llx_dolicloud_customers
(
	rowid integer AUTO_INCREMENT PRIMARY KEY,
	instance varchar(128),
	organization varchar(128),
	email varchar(256),
	plan varchar(128),
	date_registration datetime,
	date_endfreeperiod datetime,
	status integer default 0,
	partner varchar(128),
	total_invoiced double(6,3),
	total_payed double(6,3),
	tms timestamp,
	hostname_web varchar(128),
	username_web varchar(128),
	password_web varchar(128),
	hostname_db varchar(128),
	database_db varchar(128),
	port_db integer,
	username_db varchar(128),
	password_db varchar(128),
	lastcheck datetime,
	nbofusers integer,
	lastlogin datetime,
	lastpass varchar(128),
	date_lastlogin datetime,
	modulesenabled varchar(10000)
) ENGINE = innodb;
