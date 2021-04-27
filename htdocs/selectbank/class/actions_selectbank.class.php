<?php
/* Copyright (C) 2011-2012	Laurent Destailleur	<eldy@users.sourceforge.net>
 * Copyright (C) 2012		Regis Houssin		<regis.houssin@capnetworks.com>
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
 *	\file       htdocs/concatpdf/class/actions_concatpdf.class.php
 *	\ingroup    societe
 *	\brief      File to control actions
 */
require_once DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php";


/**
 *	Class to manage hooks for module SelectBank
 */
class ActionsSelectBank
{
	var $db;
	var $error;
	var $errors=array();

	/**
	 *	Constructor
	 *
	 *  @param		DoliDB		$db      Database handler
	 */
	function __construct($db)
	{
		$this->db = $db;
	}


	/**
	 * Complete doc forms
	 *
	 * @param	array	$parameters		Array of parameters
	 * @param	object	$object			Object
	 * @return	string					HTML content to add by hook
	 */
	function formBuilddocOptions($parameters, &$object)
	{
		global $langs, $user, $conf, $form;
		global $form;

		$langs->load("selectbank@selectbank");

		$out='';

		$morefiles=array();

		if (in_array($parameters['modulepart'], array('invoice','facture','propal','commande','order')) && ($object->mode_reglement_code == 'VIR' || empty($object->mode_reglement_code))) {
			$selectedbank=empty($object->fk_bank)?(isset($_POST['fk_bank'])?$_POST['fk_bank']:$conf->global->FACTURE_RIB_NUMBER):$object->fk_bank;

			$statut='0';$filtre='';
			$listofbankaccounts=array();
			$sql = "SELECT rowid, label, bank";
			$sql.= " FROM ".MAIN_DB_PREFIX."bank_account";
			$sql.= " WHERE clos = '".$statut."'";
			$sql.= " AND entity IN (".getEntity('bank_account', 1).")";
			if ($filtre) $sql.=" AND ".$filtre;
			$sql.= " ORDER BY label";
			dol_syslog(get_class($this)."::formBuilddocOptions sql=".$sql);
			$result = $this->db->query($sql);
			if ($result) {
				$num = $this->db->num_rows($result);
				$i = 0;
				if ($num) {
					while ($i < $num) {
						$obj = $this->db->fetch_object($result);
						$listofbankaccounts[$obj->rowid]=$obj->label;
						$i++;
					}
				}
			} else dol_print_error($this->db);

			$out.='<tr class="liste_titre">';
			$out.='<td align="left" colspan="4" valign="top" class="formdoc">';
			$out.='<span class="valignmiddle inline-block">';
			$out.=$langs->trans("BankAccount").' (pdf) ';
			$out.='</span> ';
			$out.='<span class="valignmiddle inline-block">';
			$out.= $form->selectarray('fk_bank', $listofbankaccounts, $selectedbank, (count($listofbankaccounts)>1?1:0));	// This info will be set into object->fk_bank into action "buildoc" before generating document.
			$out.='</span>';
		}
		$out.='</td></tr>';

		$this->resprints = $out;

		return 0;
	}
}
