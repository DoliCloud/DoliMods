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
--
-- Script run at each activation of the module, after the creation of tables, to upgrade the tables of a previous version.
-- An error because the change was already done (column already exists, ...) is ignored.


ALTER TABLE llx_autoblog_website ADD COLUMN url_style_example VARCHAR(255);

-- The page alias was stored into ref, which is now the ID of the record
ALTER TABLE llx_autoblog_blogtopic ADD COLUMN pageurl VARCHAR(128);
UPDATE llx_autoblog_blogtopic SET pageurl = ref, ref = rowid WHERE pageurl IS NULL;
