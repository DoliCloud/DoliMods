<?php
/* Copyright (C) 2014       Mário Batista       <mariorbatista@gmail.com> ISCTE-UL Moss
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
 *	\file       htdocs/saftpt/class/html.formsaftpt.class.php
 *  \ingroup    saftpt
 *	\brief      File of class to build HTML component for saftpt
 */

class FormSaftPt
{
	var $db;
	var $error;



	/**
	 *	Constructor
	 *
	 *	@param	DoliDB	$db		Database handler
	 */
	function __construct($db)
	{
		$this->db = $db;

		return 1;
	}


	/**
	 *  Return combo list with tax exemption
	 *
	 *  @param  string	$selected   	Title preselected
	 * 	@param	string	$htmlname		Name of HTML select combo field
	 *  @return	string					String with HTML select
	 */
	function select_taxexemption($selected='',$htmlname='taxexemption_code')
	{
		global $conf,$langs,$user;
		$langs->load("dict");

		$out='';

		$sql = "SELECT rowid, code, label, active FROM ".MAIN_DB_PREFIX."c_taxexemption";
		$sql.= " WHERE active = 1";

		dol_syslog("Form::select_taxexemption sql=".$sql);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			$out.= '<select class="flat" name="'.$htmlname.'">';
			$out.= '<option value="">&nbsp;</option>';
			$num = $this->db->num_rows($resql);
			$i = 0;
			if ($num)
			{
				while ($i < $num)
				{
					$obj = $this->db->fetch_object($resql);
					if ($selected == $obj->code)
					{
						$out.= '<option value="'.$obj->code.'" selected="selected">';
					}
					else
					{
						$out.= '<option value="'.$obj->code.'">';
					}
					
					$out.= ($langs->trans("TaxExemption".$obj->code)!="TaxExemption".$obj->code ? $langs->trans("TaxExemption".$obj->code) : ($obj->label!='-'?$obj->code.'-' .$obj->label:''));
					$out.= '</option>';
					$i++;
				}
			}
			$out.= '</select>';
			if ($user->admin) $out.= info_admin($langs->trans("YouCanChangeValuesForThisListFromDictionnarySetup"),1);
		}
		else
		{
			dol_print_error($this->db);
		}

		return $out;
	}

}

?>
