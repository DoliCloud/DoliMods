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
 *		\brief      Ensemble de fonctions de base pour le module SellYourSaas, DoliCloud part
 */




/**
 * getListOfLinks
 *
 * @param Object $object            Object
 * @param string $lastloginadmin    Last login admin
 * @param string $lastpassadmin     Last pass admin
 */
function getListOfLinks($object, $lastloginadmin, $lastpassadmin)
{
    global $conf;

	// Define links
    $links='';

    //if (empty($conf->global->DOLICLOUD_EXT_HOME)) $links='Error: DOLICLOUD_EXT_HOME not defined<br>';

	// Dolibarr instance login
	$url='https://'.$object->instance.'.on.dolicloud.com?username='.$lastloginadmin.'&amp;password='.$lastpassadmin;
	$link='<a href="'.$url.'" target="_blank" id="dollink">'.$url.'</a>';
	$links.='Dolibarr link: ';
	//print '<input type="text" name="dashboardconnectstring" value="'.dashboardconnectstring.'" size="100"><br>';
	$links.=$link.'<br>';

	$links.='<br>';

	// Dashboard
	$url='https://www.on.dolicloud.com/signIn/index?email='.$object->email.'&amp;password='.$object->password_web;	// Note that password may have change and not being the one of dolibarr admin user
	$link='<a href="'.$url.'" target="_blank" id="dashboardlink">'.$url.'</a>';
	$links.='Dashboard: ';
	$links.=$link.'<br>';

	$links.='<br>';

	// SFTP
	//$sftpconnectstring=$object->username_web.':'.$object->password_web.'@'.$object->hostname_web.':'.$conf->global->DOLICLOUD_EXT_HOME.'/'.$object->username_web.'/'.preg_replace('/_([a-zA-Z0-9]+)$/','',$object->database_db);
    $sftpconnectstring='sftp://'.$object->username_web.'@'.$object->hostname_web.$conf->global->DOLICLOUD_EXT_HOME.'/'.$object->username_web.'/'.preg_replace('/_([a-zA-Z0-9]+)$/','',$object->database_db);
    $links.='SFTP connect string: ';
	$links.='<input type="text" name="sftpconnectstring" id="sftpconnectstring" value="'.$sftpconnectstring.'" size="110"><br>';
	$links.=ajax_autoselect('sftpconnectstring');
	//$links.='<br>';

	// MySQL
	$mysqlconnectstring='mysql -A -u '.$object->username_db.' -p\''.$object->password_db.'\' -h '.$object->hostname_db.' -D '.$object->database_db;
	$links.='Mysql connect string: ';
	$links.='<input type="text" name="mysqlconnectstring" id="mysqlconnectstring" value="'.$mysqlconnectstring.'" size="110"><br>';
	$links.=ajax_autoselect('mysqlconnectstring');

	// MySQL backup
	/*$mysqlconnectstring='mysqldump -A -u '.$object->username_db.' -p\''.$object->password_db.'\' -h '.$object->hostname_db.' -D '.$object->database_db;
	$links.='Mysql connect string: ';
	$links.='<input type="text" name="mysqlconnectstring" value="'.$mysqlconnectstring.'" size="110"><br>';*/

	// JDBC
	$jdbcconnectstring='jdbc:mysql://'.$object->hostname_db.'/';
	//$jdbcconnectstring.=$object->database_db;
	$links.='JDBC connect string: ';
	$links.='<input type="text" name="jdbcconnectstring" id="jdbcconnectstring" value="'.$jdbcconnectstring.'" size="110"><br>';
	$links.=ajax_autoselect('jdbcconnectstring');

	$links.='<br>';
	$links.='<br>';

	$upgradestring=$conf->global->DOLICLOUD_SCRIPTS_PATH.'/rsync_instance.php '.$conf->global->DOLICLOUD_LASTSTABLEVERSION_DIR.' '.$object->instance;

	// Mysql Restore
	$mysqlresotrecommand='mysql -A -u '.$object->username_db.' -p\''.$object->password_db.'\' -h '.$object->hostname_db.' -D '.$object->database_db.' < filetorestore';
	$links.='Mysql overwrite database:<br>';
	$links.='<input type="text" id="mysqlrestorecommand" name="mysqlrestorecommand" value="'.$mysqlresotrecommand.'" class="quatrevingtpercent"><br>';
	$links.=ajax_autoselect("mysqlrestorecommand", 0);

	// Document restore
	$sftprestorestring='rsync -n -v -a dolibarr_documents/* '.$object->username_web.'@'.$object->hostname_web.':'.$object->fs_path.'/documents';
	$links.='Rsync to copy/overwrite document dir (remove -n to execute really):<br>';
	$links.='<input type="text" id="sftprestorestring" name="sftprestorestring" value="'.$sftprestorestring.'" class="quatrevingtpercent"><br>';
	$links.=ajax_autoselect("sftprestorestring", 0);
	$links.='<br>';

	// Deploy module
	$sftpdeploystring='rsync -n -v -a pathtohtdocsmodule/* '.$object->username_web.'@'.$object->hostname_web.':'.$object->fs_path.'/htdocs/namemodule';
	$links.='Rsync to install or overwrite module (remove -n to execute really):<br>';
	$links.='<input type="text" id="sftpdeploystring" name="sftpdeploystring" value="'.$sftpdeploystring.'" class="quatrevingtpercent"><br>';
	$links.=ajax_autoselect("sftpdeploystring", 0);
	$links.='<br>';

	// Upgrade link
	$upgradestringtoshow=$upgradestring.' test';
	$links.='Upgrade version line string (remplacer "test" par "confirmunlock" pour exécuter réellement)<br>';
	$links.='<input type="text" id="upgradestring" name="upgradestring" value="'.$upgradestringtoshow.'" class="quatrevingtpercent"><br>';
	$links.=ajax_autoselect("upgradestring", 0);
	$links.='<br>';

	return $links;
}


/**
 * getvalfromkey
 *
 * @param 	DoliDb	$db		Database handler
 * @param 	string	$param	Param
 * @param	string	$val	Val
 * @return	string			Value
 */
function getvalfromkey($db,$param,$val)
{
	$sql ="select ".$param." as val from dolicloud_saasplex.app_instance, dolicloud_saasplex.customer, dolicloud_saasplex.address, dolicloud_saasplex.country_region";
	$sql.=" where dolicloud_saasplex.address.country_id=dolicloud_saasplex.country_region.id AND";
	$sql.=" dolicloud_saasplex.customer.address_id=dolicloud_saasplex.address.id AND dolicloud_saasplex.app_instance.customer_id = dolicloud_saasplex.customer.id AND dolicloud_saasplex.customer.org_name = '".$db->escape($val)."'";
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
 * @param   string  $prefix     Prefix
 * @return  array				Array of tabs to shoc
 */
function dolicloud_prepare_head($object,$prefix='')
{
	global $langs, $conf;

	$h = 0;
	$head = array();

	$head[$h][0] = dol_buildpath('/sellyoursaas/backoffice/infoinstance'.$prefix.'.php',1).'?'.(get_class($object)=='Dolicloudcustomernew'?'instanceoldid='.$object->id:'id='.$object->id);
	$head[$h][1] = $langs->trans("InfoInstance");
	$head[$h][2] = 'infoinstance';
	$h++;

	$head[$h][0] = dol_buildpath('/sellyoursaas/backoffice/dolicloud_card_backup'.$prefix.'.php',1).'?'.(get_class($object)=='Dolicloudcustomernew'?'instanceoldid='.$object->id:'id='.$object->id);
	$head[$h][1] = $langs->trans("Backup");
	$head[$h][2] = 'backup';
	$h++;

	$head[$h][0] = dol_buildpath('/sellyoursaas/backoffice/dolicloud_card_upgrade'.$prefix.'.php',1).'?'.(get_class($object)=='Dolicloudcustomernew'?'instanceoldid='.$object->id:'id='.$object->id);
	$head[$h][1] = $langs->trans("UsefulLinks");
	$head[$h][2] = 'upgrade';
	$h++;

	$head[$h][0] = dol_buildpath('/sellyoursaas/backoffice/dolicloud_card_users'.$prefix.'.php',1).'?'.(get_class($object)=='Dolicloudcustomernew'?'instanceoldid='.$object->id:'id='.$object->id);
	$head[$h][1] = $langs->trans("Users");
	$head[$h][2] = 'users';
	$h++;

	$head[$h][0] = dol_buildpath('/sellyoursaas/backoffice/dolicloud_card_payments'.$prefix.'.php',1).'?'.(get_class($object)=='Dolicloudcustomernew'?'instanceoldid='.$object->id:'id='.$object->id);
	$head[$h][1] = $langs->trans("Payments");
	$head[$h][2] = 'payments';
	$h++;

	// Show more tabs from modules
    // Entries must be declared in modules descriptor with line
    // $this->tabs = array('entity:+tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to add new tab
    // $this->tabs = array('entity:-tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to remove a tab
    complete_head_from_modules($conf,$langs,$object,$head,$h,'contact');

    /*
    $head[$h][0] = dol_buildpath('/sellyoursaas/backoffice/dolicloud_info.php',1).'?id='.$object->id;
	$head[$h][1] = $langs->trans("Info");
	$head[$h][2] = 'info';
	$h++;
	*/

	return $head;
}
