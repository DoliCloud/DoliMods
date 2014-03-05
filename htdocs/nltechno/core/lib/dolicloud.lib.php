<?php
/* Copyright (C) 2006-2012 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 * or see http://www.gnu.org/
 */

/**
 *	    \file       htdocs/core/lib/dolicloud.lib.php
 *		\brief      Ensemble de fonctions de base pour le module NLTechno, DoliCloud part
 */



/**
 * getvalfromkey
 *
 * @param 	DoliDb	$db		Database handler
 * @param 	string	$key	Key
 * @param	string	$param	param
 * @return	string			Value
 */
function getvalfromkey($db,$param,$val)
{
	$sql ="select ".$param." as val from dolicloud_saasplex.app_instance, dolicloud_saasplex.customer_account, dolicloud_saasplex.address, dolicloud_saasplex.country_region";
	$sql.=" where dolicloud_saasplex.address.country_id=dolicloud_saasplex.country_region.id AND";
	$sql.=" dolicloud_saasplex.customer_account.address_id=dolicloud_saasplex.address.id AND dolicloud_saasplex.app_instance.customer_account_id = dolicloud_saasplex.customer_account.id AND dolicloud_saasplex.customer_account.org_name = '".$db->escape($val)."'";
	//print $sql;
	$resql=$db->query($sql);
	if ($resql)
	{
		$obj=$db->fetch_object($resql);
		$ret=$obj->val;
	}
	else
	{
		dol_print_error($db,'Failed to get key sql='.$sql);
	}
	return $ret;
}


/**
 * Prepare array with list of tabs
 *
 * @param   Object	$object		Object related to tabs
 * @return  array				Array of tabs to shoc
 */
function dolicloud_prepare_head($object)
{
	global $langs, $conf;

	$h = 0;
	$head = array();

	$head[$h][0] = ($object->id?dol_buildpath('/nltechno/dolicloud/dolicloud_card.php',1).'?id='.$object->id:'');
	$head[$h][1] = $langs->trans("Card");
	$head[$h][2] = 'card';
	$h++;

	if ($object->id > 0)
	{
		$head[$h][0] = dol_buildpath('/nltechno/dolicloud/dolicloud_card_upgrade.php',1).'?id='.$object->id;
		$head[$h][1] = $langs->trans("Upgrade");
		$head[$h][2] = 'upgrade';
		$h++;

		$head[$h][0] = dol_buildpath('/nltechno/dolicloud/dolicloud_card_backup.php',1).'?id='.$object->id;
		$head[$h][1] = $langs->trans("Backup");
		$head[$h][2] = 'backup';
		$h++;

		$head[$h][0] = dol_buildpath('/nltechno/dolicloud/dolicloud_card_users.php',1).'?id='.$object->id;
		$head[$h][1] = $langs->trans("Users");
		$head[$h][2] = 'users';
		$h++;

		$head[$h][0] = dol_buildpath('/nltechno/dolicloud/dolicloud_card_payments.php',1).'?id='.$object->id;
		$head[$h][1] = $langs->trans("Payments");
		$head[$h][2] = 'payments';
		$h++;

		// Show more tabs from modules
	    // Entries must be declared in modules descriptor with line
	    // $this->tabs = array('entity:+tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to add new tab
	    // $this->tabs = array('entity:-tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to remove a tab
	    complete_head_from_modules($conf,$langs,$object,$head,$h,'contact');

	    /*
	    $head[$h][0] = dol_buildpath('/nltechno/dolicloud/dolicloud_info.php',1).'?id='.$object->id;
		$head[$h][1] = $langs->trans("Info");
		$head[$h][2] = 'info';
		$h++;
	*/
	}

	return $head;
}

?>