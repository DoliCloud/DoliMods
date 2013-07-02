-- ===================================================================
-- Copyright (C) 2011 Juanjo Menent <jmenent@2byte.es>
--
-- This program is free software; you can redistribute it and/or modify
-- it under the terms of the GNU General Public License as published by
-- the Free Software Foundation; either version 2 of the License, or
-- (at your option) any later version.
--
-- This program is distributed in the hope that it will be useful,
-- but WITHOUT ANY WARRANTY; without even the implied warranty of
-- MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
-- GNU General Public License for more details.
--
-- You should have received a copy of the GNU General Public License
-- along with this program; if not, write to the Free Software
-- Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
--
-- $Id: llx_pos_cash.sql,v 1.4 2011-08-22 10:34:40 jmenent Exp $
-- ===================================================================

create table llx_pos_cash
(
  rowid           	integer AUTO_INCREMENT PRIMARY KEY,
  entity            integer  DEFAULT 1 NOT NULL,
  code				varchar(3),
  name				varchar(30),
  tactil			tinyint not null default 0,
  barcode			tinyint not null default 0,
  fk_paycash     	integer,
  fk_modepaycash    integer,
  fk_paybank      	integer,
  fk_modepaybank    integer,
  fk_warehouse		integer,
  fk_device			integer,
  fk_soc			integer,
  is_used			tinyint default 0,
  fk_user_u  		integer,
  fk_user_c		    integer,
  fk_user_m		    integer,
  datec				datetime,
  datea				datetime,
  is_closed 		tinyint  DEFAULT 0 --(0=Open, 1=Closed)
)ENGINE=innodb;
