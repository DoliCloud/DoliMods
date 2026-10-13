-- ===================================================================
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
-- Cache of geocoded addresses (address -> latitude/longitude).
-- A row is searched again when the address of the object differs from the stored address.
-- ===================================================================

CREATE TABLE llx_openstreetmap_geo (
	rowid INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
	fk_object INTEGER NOT NULL,
	type_object varchar(16) NOT NULL,
	latitude double NULL,
	longitude double NULL,
	address varchar(255),
	source varchar(16),				-- 'pdok' or 'nominatim'
	result_code varchar(16),		-- 'OK', 'ZERO_RESULTS' or an error code
	result_label varchar(255),
	tms timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;
