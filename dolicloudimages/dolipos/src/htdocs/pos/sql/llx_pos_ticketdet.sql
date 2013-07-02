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
-- $Id: llx_pos_ticketdet.sql,v 1.1 2011-08-04 16:33:26 jmenent Exp $
-- ===================================================================

create table llx_pos_ticketdet
(
  rowid               integer    		AUTO_INCREMENT PRIMARY KEY,
  fk_ticket           integer    		NOT NULL,
  fk_parent_line	  integer	 		NULL,
  fk_product          integer    		NULL,
  description         text,
  tva_tx              double(6,3),
  localtax1_tx        double(6,3) 		DEFAULT 0,
  localtax2_tx	      double(6,3) 		DEFAULT 0,
  qty                 real,	
  remise_percent      real      		DEFAULT 0,
  remise              real      		DEFAULT 0,
  fk_remise_except    integer   		NULL,
  subprice            double(24,8),	
  price               double(24,8),	
  total_ht            double(24,8),
  total_tva           double(24,8),
  total_localtax1     double(24,8)  	DEFAULT 0,
  total_localtax2     double(24,8)		DEFAULT 0,	
  total_ttc           double(24,8),	
  product_type		  integer    		DEFAULT 0,
  date_start          datetime   		DEFAULT NULL,
  date_end            datetime   		DEFAULT NULL,
  info_bits		      integer    		DEFAULT 0,
  fk_code_ventilation integer    		DEFAULT 0 NOT NULL,
  fk_export_compta    integer    		DEFAULT 0 NOT NULL,
  rang                integer    		DEFAULT 0,
  import_key          varchar(14),
  note				  text				NULL	
)ENGINE=innodb;

