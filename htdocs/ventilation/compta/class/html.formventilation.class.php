<?php
/*
 * Copyright (C) 2013  Florian Henry   <florian.henry@open-concept.pro>
 * 
*
*
* This program is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 3 of the License, or
* (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

/**
 * \file ventilation/compte/class/html.formvenitlation.class.php
 * \brief Class for HML form
 */
class FormVentilation extends Form {
	var $db;
	var $error;
	
	/**
	 * Constructor
	 * 
	 * @param DoliDB $db handler
	 */
	function __construct($db) {
		$this->db = $db;
		return 1;
	}
	
	/**
	 *	Return select filer with date of transaction
	 *
	 *  @param	string	$htmlname 		name of input
	 *  @param	string	$selectedkey	selected default value
	 *  @param	int		$custid 		customerid
	 *  @param	int 	$shopid 		shopid
	 *  @param	string 	$type 			'histoshop' or 'histocust' or ''
	 *	@return	string					HTML select input
	 */
	function select_bookkeeping_importkey ($htmlname='importkey',$selectedkey) {
	
		global $langs;
	
		$date_array=array();
	
		$sql='SELECT DISTINCT import_key from '.MAIN_DB_PREFIX.'bookkeeping ';
		$sql.=' ORDER BY import_key DESC';
	
		
		$out='<SELECT name="'.$htmlname.'">';
		
		dol_syslog(get_class($this)."::select_bookkeeping_importkey sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			$i=0;
			$num = $this->db->num_rows($resql);
				
			while ($i<$num)
			{
				$obj = $this->db->fetch_object($resql);
				
				$selected='';
				if ($selectedkey==$obj->import_key) {
					$selected=' selected="selected" ';
				}
	
				$out.='<OPTION value="'.$obj->import_key.'"'.$selected.'>'.$obj->import_key.'</OPTION>';
	
				$i++;
			}
	
		}else {
			$this->error="Error ".$this->db->lasterror();
			dol_syslog(get_class($this)."::select_bookkeeping_importkey ".$this->error, LOG_ERR);
			return -1;
		}
		
		$out.='</SELECT>';
	
		return $out;
	}
	
}
