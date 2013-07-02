-- ===================================================================
-- Copyright (C) 2011      Juanjo Menent        <jmenent@2byte.es>
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
-- $Id: llx_pos_ticket.sql,v 1.1 2011-08-04 16:33:26 jmenent Exp $
-- ===================================================================


create table llx_pos_ticket
(
  rowid               	integer AUTO_INCREMENT PRIMARY KEY,

  ticketnumber        	varchar(30)        	NOT NULL,
  type       			integer,
  entity              	integer  DEFAULT 1 	NOT NULL,

  fk_cash			  	integer,
  fk_soc              	integer            	NOT NULL,
  fk_place			    integer				DEFAULT 0,
  date_creation		  	datetime,
  date_ticket         	date, 
  date_closed		  	datetime,
  tms                 	timestamp,
  paye                	smallint DEFAULT 0 	NOT NULL,
  remise_percent      	real     			DEFAULT 0,
  remise_absolute     	real     			DEFAULT 0, 
  remise              	real     			DEFAULT 0,
  
  customer_pay			double(24,8)		DEFAULT 0,
  difpayment			double(24,8)		DEFAULT 0,	

  tva                 	double(24,8)     	DEFAULT 0,
  localtax1			  	double(24,8)     	DEFAULT 0,
  localtax2           	double(24,8)     	DEFAULT 0,
  total_ht            	double(24,8)     	DEFAULT 0,
  total_ttc           	double(24,8)     	DEFAULT 0,

  fk_statut           	smallint 			DEFAULT 0 NOT NULL,

  fk_user_author      	integer,
  fk_user_close       	integer,

  fk_facture          	integer,
  fk_ticket_source		integer,

  fk_mode_reglement   	integer,

  fk_control			integer,
  
  note                	text,
  note_public         	text,
  model_pdf           	varchar(255),
  import_key          	varchar(14)
  
)ENGINE=innodb;
