-- Copyright (C) 2022  Laurent Destailleur     <eldy@users.sourceforge.net>
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
-- along with this program. If not, see <https://www.gnu.org/licenses/>.
--
--

--
-- Do not put any comment at end of lines.
--

--
-- List of all managed triggered events (used for trigger agenda automatic events and for notification)
--

-- actions enabled by default (constant created for that) when we enable module agenda
insert into llx_c_action_trigger (code,label,description,elementtype,rang) values ('COMPANY_SENTBYSMS','SMS Sent by email','Executed when a sms is sent','societe',1);
insert into llx_c_action_trigger (code,label,description,elementtype,rang) values ('MEMBER_SENTBYSMS','SMS Sent by email','Executed when a sms is sent','member',22);
