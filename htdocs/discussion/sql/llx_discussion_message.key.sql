-- Copyright (C) 2024		Alice Adminson				<myemail@mycompany.com>
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


-- BEGIN MODULEBUILDER INDEXES
ALTER TABLE llx_discussion_message ADD INDEX idx_discussion_message_rowid (rowid);
-- END MODULEBUILDER INDEXES

--ALTER TABLE llx_discussion_message ADD UNIQUE INDEX uk_discussion_message_fieldxy(fieldx, fieldy);

--ALTER TABLE llx_discussion_message ADD CONSTRAINT llx_discussion_message_fk_field FOREIGN KEY (fk_field) REFERENCES llx_discussion_myotherobject(rowid);
