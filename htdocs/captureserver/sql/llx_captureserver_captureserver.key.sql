-- Copyright (C) ---Put here your own copyright and developer email---
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
-- along with this program.  If not, see http://www.gnu.org/licenses/.


-- BEGIN MODULEBUILDER INDEXES
ALTER TABLE llx_captureserver_captureserver ADD INDEX idx_captureserver_captureserver_rowid (rowid);
ALTER TABLE llx_captureserver_captureserver ADD INDEX idx_captureserver_captureserver_ref (ref);
ALTER TABLE llx_captureserver_captureserver ADD INDEX idx_captureserver_captureserver_type (type);
-- END MODULEBUILDER INDEXES

ALTER TABLE llx_captureserver_captureserver ADD UNIQUE INDEX uk_captureserver_captureserver_uk(entity, ref);

