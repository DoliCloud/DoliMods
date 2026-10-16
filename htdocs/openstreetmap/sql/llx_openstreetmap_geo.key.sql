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

-- The cache is kept per entity (multicompany): a shared third party has one row per company.
-- Installations before 4.0.4 have the old key without entity: it is removed first (no error if missing).
ALTER TABLE llx_openstreetmap_geo DROP INDEX uk_openstreetmap_geo;
ALTER TABLE llx_openstreetmap_geo ADD UNIQUE INDEX uk_openstreetmap_geo (entity, fk_object, type_object);
