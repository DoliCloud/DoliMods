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
-- ===================================================================

-- One row per row of llx_c_ziptown added by the module; the import in mode "replace" reads them by country and import key.
ALTER TABLE llx_openstreetmap_ziptown ADD UNIQUE INDEX uk_openstreetmap_ziptown (fk_ziptown);
ALTER TABLE llx_openstreetmap_ziptown ADD INDEX idx_openstreetmap_ziptown_pays (fk_pays, import_key);
