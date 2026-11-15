-- Copyright (C) 2026 Laurent Destailleur <eldy@users.sourceforge.net>
--
-- This program is free software: you can redistribute it and/or modify
-- it under the terms of the GNU General Public License as published by
-- the Free Software Foundation, either version 3 of the License, or
-- (at your option) any later version.
--
-- This program is distributed in the hope that it will be useful,
-- but WITHOUT ANY WARRANTY; without even the implied warranty of
-- MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
-- GNU General Public License for more details.
--
-- You should have received a copy of the GNU General Public License
-- along with this program.  If not, see https://www.gnu.org/licenses/.


CREATE TABLE llx_autoblog_blogtopic(
	rowid INTEGER AUTO_INCREMENT PRIMARY KEY NOT NULL,
	entity INTEGER DEFAULT 1 NOT NULL,
	ref VARCHAR(128) NOT NULL,
	pageurl VARCHAR(128),
	label VARCHAR(255) NOT NULL,
	instructions TEXT,
	keywords VARCHAR(255),
	fk_website INTEGER NOT NULL,
	lang VARCHAR(6),
	date_planned DATETIME,
	content MEDIUMTEXT,
	description VARCHAR(255),
	date_generation DATETIME,
	date_publication DATETIME,
	fk_websitepage INTEGER,
	nb_attempts INTEGER DEFAULT 0 NOT NULL,
	last_error TEXT,
	note_private TEXT,
	date_creation DATETIME NOT NULL,
	tms TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	fk_user_creat INTEGER NOT NULL,
	fk_user_modif INTEGER,
	import_key VARCHAR(14),
	status INTEGER DEFAULT 0 NOT NULL
) ENGINE=innodb;
