-- ===================================================================
-- Copyright (C) 2004-2017 Laurent Destailleur  <eldy@users.sourceforge.net>
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
--
-- ===================================================================

CREATE TABLE llx_google_maps (
	rowid INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
	fk_object INTEGER NOT NULL,
	type_object varchar(16) NOT NULL,
	latitude FLOAT NULL,
	longitude FLOAT NULL,
	address varchar(255),
	result_on_degraded_address TINYINT DEFAULT 0,		-- Set 1 if we force search on a degraded address (town only for example) because first try return ZERO_RESULT
	result_code varchar(16),
	result_label varchar(255),
	tms timestamp
) ENGINE=InnoDB;
