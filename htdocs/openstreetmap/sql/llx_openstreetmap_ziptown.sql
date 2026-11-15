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
-- Rows of the dictionary of zip codes and towns of Dolibarr (llx_c_ziptown) added by the import of the module
-- OpenStreetMap. Only these rows can be removed by an import in mode "replace"; rows of other sources are never changed.
CREATE TABLE llx_openstreetmap_ziptown (
	rowid INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
	fk_ziptown INTEGER NOT NULL,		-- rowid in llx_c_ziptown
	fk_pays INTEGER NOT NULL,			-- country of the row
	import_key varchar(32) NOT NULL,	-- id of the last import that had this zip code and town
	tms timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;
