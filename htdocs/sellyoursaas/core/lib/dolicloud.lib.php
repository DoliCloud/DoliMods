<?php
/* Copyright (C) 2006-2017 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 *		\brief      Ensemble de fonctions de base pour le module SellYourSaas
 */


/**
 *	Read customer instance to get nb_user, nb_gb and lastlogin_admin and lastpass_admin
 *
 *  @param	Contract	$contract		Contract
 */
function refreshContract(Contrat $contract)
{
	dol_syslog("Scan customer instance to get fresh data (remote v1 on v2)");

	if (empty($contract->array_options['options_hostname_db']) || empty($contract->array_options['options_username_db']))
	{
		return array('error'=>'Properties of customer instance database are unknown');
	}

	$dbcustomerinstance=getDoliDBInstance('mysqli', $contract->array_options['options_hostname_db'], $contract->array_options['options_username_db'], $contract->array_options['options_password_db'], $contract->array_options['options_database_db'], $contract->array_options['options_port_db']);

	if (is_object($dbcustomerinstance) && $dbcustomerinstance->connected)
	{
		// Get user/pass of last admin user
		$sql="SELECT login, pass, admin FROM llx_user WHERE statut = 1 ORDER BY statut DESC, datelastlogin DESC";
		$resql=$dbcustomerinstance->query($sql);

		if ($resql)
		{
			$nb_users = $dbcustomerinstance->num_rows($resql);
			$obj = $dbcustomerinstance->fetch_object($resql);
			if ($obj->admin)
			{
				$lastlogin_admin=$obj->login;
				$lastpass_admin=$obj->pass;
			}
			return array('nb_users'=>$nb_users, 'nb_gb'=>0, 'lastlogin_admin'=>$lastlogin_admin, 'lastpass_admin'=>$lastpass_admin);
		}
		else
		{
			return array('error'=>'Error, connect to customer instance is ok, but failed to read user table. '.$dbcustomerinstance->lasterror());
		}
	}
	else
	{
		return array('error'=>'Error failed to connect to customer instance.');
	}
}


/**
 * getListOfLinks
 *
 * @param	Object 	$object            	Object
 * @param	string 	$lastloginadmin    	Last login admin
 * @param	string 	$lastpassadmin     	Last pass admin
 * @param	int		$instanceoldid		Instance old id (defined if this is a old v1 object)
 */
function getListOfLinks($object, $lastloginadmin, $lastpassadmin, $instanceoldid=0)
{
    global $conf, $langs;

	// Define links
    $links='';

    //if (empty($conf->global->DOLICLOUD_EXT_HOME)) $links='Error: DOLICLOUD_EXT_HOME not defined<br>';

	// Dolibarr instance login
	$url='https://'.$object->hostname_os.'?username='.$lastloginadmin.'&amp;password='.$lastpassadmin;
	$link='<a href="'.$url.'" target="_blank" id="dollink">'.$url.'</a>';
	$links.='Dolibarr link (last logged admin): ';
	//print '<input type="text" name="dashboardconnectstring" value="'.dashboardconnectstring.'" size="100"><br>';
	$links.=$link.'<br>';

	$links.='<br>';

	// Dashboard
	if ($user->admin && ! empty($object->array_options['options_dolicloud']))
	{
		if ($object->array_options['options_dolicloud'] == 'yesv1')
		{
			$url='https://www.on.dolicloud.com/signIn/index?email='.$object->email.'&amp;password='.$object->password_web;	// Note that password may have change and not being the one of dolibarr admin user
		}
		if ($object->array_options['options_dolicloud'] == 'yesv2')
		{
			$dol_login_hash=dol_hash('sellyoursaas'.$object->id.dol_print_date(dol_now,'%Y%m%d%H','gmt'));	// hash is valid one hour
			$url=$conf->global->SELLYOURSAAS_ACCOUNT_URL.'?mode=dashboard&dol_login='.$object->id.'&mode=logout&dol_login_hash='.$dol_login_hash;	// Note that password may have change and not being the one of dolibarr admin user
		}
	}
	$link='<a href="'.$url.'" target="_blank" id="dashboardlink">'.$url.'</a>';
	$links.='Dashboard: ';
	$links.=$link.'<br>';

	$links.='<br>';

	// Home
	$homestring=$conf->global->DOLICLOUD_EXT_HOME.'/'.$object->username_os.'/'.preg_replace('/_([a-zA-Z0-9]+)$/','',$object->database_db);
	$links.='Home dir: ';
	$links.='<input type="text" name="homestring" id="homestring" value="'.$homestring.'" size="110"><br>';
	if ($conf->use_javascript_ajax) $links.=ajax_autoselect('homestring');
	//$links.='<br>';

	// SSH
    $sshconnectstring='ssh '.$object->username_os.'@'.$object->hostname_os;
    $links.='SSH connect string: ';
    $links.='<input type="text" name="sshconnectstring" id="sshconnectstring" value="'.$sshconnectstring.'" size="50">';
    if ($conf->use_javascript_ajax) $links.=ajax_autoselect('sshconnectstring');
    $links.=' &nbsp; '.$langs->trans("or").' SU: ';
    $sustring='su '.$object->username_os;
    $links.='<input type="text" name="sustring" id="sustring" value="'.$sustring.'" size="30"><br>';
    if ($conf->use_javascript_ajax) $links.=ajax_autoselect('sustring');
    $links.='<br>';

	// SFTP
	//$sftpconnectstring=$object->username_os.':'.$object->password_web.'@'.$object->hostname_os.':'.$conf->global->DOLICLOUD_EXT_HOME.'/'.$object->username_os.'/'.preg_replace('/_([a-zA-Z0-9]+)$/','',$object->database_db);
    $sftpconnectstring='sftp://'.$object->username_os.'@'.$object->hostname_os.$conf->global->DOLICLOUD_EXT_HOME.'/'.$object->username_os.'/'.preg_replace('/_([a-zA-Z0-9]+)$/','',$object->database_db);
    $links.='SFTP connect string: ';
	$links.='<input type="text" name="sftpconnectstring" id="sftpconnectstring" value="'.$sftpconnectstring.'" size="110"><br>';
	if ($conf->use_javascript_ajax) $links.=ajax_autoselect('sftpconnectstring');
	//$links.='<br>';

	// MySQL
	$mysqlconnectstring='mysql -A -C -u '.$object->username_db.' -p\''.$object->password_db.'\' -h '.$object->hostname_db.' -D '.$object->database_db;
	$links.='Mysql connect string: ';
	$links.='<input type="text" name="mysqlconnectstring" id="mysqlconnectstring" value="'.$mysqlconnectstring.'" size="110"><br>';
	if ($conf->use_javascript_ajax) $links.=ajax_autoselect('mysqlconnectstring');

	// MySQL backup
	/*$mysqlconnectstring='mysqldump -A -u '.$object->username_db.' -p\''.$object->password_db.'\' -h '.$object->hostname_db.' -D '.$object->database_db;
	$links.='Mysql connect string: ';
	$links.='<input type="text" name="mysqlconnectstring" value="'.$mysqlconnectstring.'" size="110"><br>';*/

	// JDBC
	$jdbcconnectstring='jdbc:mysql://'.$object->hostname_db.'/';
	//$jdbcconnectstring.=$object->database_db;
	$links.='JDBC connect string: ';
	$links.='<input type="text" name="jdbcconnectstring" id="jdbcconnectstring" value="'.$jdbcconnectstring.'" size="110"><br>';
	if ($conf->use_javascript_ajax) $links.=ajax_autoselect('jdbcconnectstring');

	$links.='<br>';
	$links.='<br>';

	$upgradestring=$conf->global->DOLICLOUD_SCRIPTS_PATH.'/rsync_instance.php '.$conf->global->DOLICLOUD_LASTSTABLEVERSION_DIR.' '.$object->instance;
	$purgestring=$conf->global->DOLICLOUD_SCRIPTS_PATH.'/../dev/initdata/dev/purge-data.php test xxx mysqli '.$object->hostname_db.' '.$object->username_db.' '.$object->password_db.' '.$object->database_db.' '.($object->database_port?$object->database_port:3306);

	// Mysql Backup
	$mysqlbackupcommand='mysqldump -C -u '.$object->username_db.' -p\''.$object->password_db.'\' -h '.$object->hostname_db.' '.$object->database_db.' > filebackup';
	$links.='Mysql backup database:<br>';
	$links.='<input type="text" id="mysqlbackupcommand" name="mysqlbackupcommand" value="'.$mysqlbackupcommand.'" class="quatrevingtpercent"><br>';
	if ($conf->use_javascript_ajax) $links.=ajax_autoselect("mysqlbackupcommand", 0);
	$links.='<br>';

	// Mysql Restore
	$mysqlresotrecommand='mysql -C -A -u '.$object->username_db.' -p\''.$object->password_db.'\' -h '.$object->hostname_db.' -D '.$object->database_db.' < filetorestore';
	$links.='Mysql overwrite database:<br>';
	$links.='<input type="text" id="mysqlrestorecommand" name="mysqlrestorecommand" value="'.$mysqlresotrecommand.'" class="quatrevingtpercent"><br>';
	if ($conf->use_javascript_ajax) $links.=ajax_autoselect("mysqlrestorecommand", 0);
	$links.='<br>';

	// Document restore
	$sftprestorestring='rsync -n -v -a --exclude \'*.cache\' dolibarr_documents/* '.$object->username_os.'@'.$object->hostname_os.':'.$object->fs_path.'/documents';
	$links.='Rsync to copy/overwrite document dir (remove -n to execute really):<br>';
	$links.='<input type="text" id="sftprestorestring" name="sftprestorestring" value="'.$sftprestorestring.'" class="quatrevingtpercent"><br>';
	if ($conf->use_javascript_ajax) $links.=ajax_autoselect("sftprestorestring", 0);
	$links.='<br>';

	// Deploy module
	$sftpdeploystring='rsync -n -v -a --exclude \'*.cache\' pathtohtdocsmodule/* '.$object->username_os.'@'.$object->hostname_os.':'.$object->fs_path.'/htdocs/namemodule';
	$links.='Rsync to install or overwrite module (remove -n to execute really):<br>';
	$links.='<input type="text" id="sftpdeploystring" name="sftpdeploystring" value="'.$sftpdeploystring.'" class="quatrevingtpercent"><br>';
	if ($conf->use_javascript_ajax) $links.=ajax_autoselect("sftpdeploystring", 0);
	$links.='<br>';

	// Upgrade link
	$upgradestringtoshow=$upgradestring.' test';
	$links.='Upgrade version line string (remplacer "test" par "confirmunlock" pour exécuter réellement)<br>';
	$links.='<input type="text" id="upgradestring" name="upgradestring" value="'.$upgradestringtoshow.'" class="quatrevingtpercent"><br>';
	if ($conf->use_javascript_ajax) $links.=ajax_autoselect("upgradestring", 0);
	$links.='<br>';

	// Upgrade link
	$purgestringtoshow=$purgestring;
	$links.='Purge command line string (remplacer "test" par "confirm" pour exécuter réellement)<br>';
	$links.='<input type="text" id="purgestring" name="purgestring" value="'.$purgestringtoshow.'" class="quatrevingtpercent"><br>';
	if ($conf->use_javascript_ajax) $links.=ajax_autoselect("purgestring", 0);
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

	$head[$h][0] = dol_buildpath('/sellyoursaas/backoffice/instance_info'.$prefix.'.php',1).'?'.(get_class($object)=='Dolicloud_customers'?'instanceoldid='.$object->id:'id='.$object->id);
	$head[$h][1] = $langs->trans("InfoInstance");
	$head[$h][2] = 'infoinstance';
	$h++;

	$head[$h][0] = dol_buildpath('/sellyoursaas/backoffice/instance_links'.$prefix.'.php',1).'?'.(get_class($object)=='Dolicloud_customers'?'instanceoldid='.$object->id:'id='.$object->id);
	$head[$h][1] = $langs->trans("UsefulLinks");
	$head[$h][2] = 'upgrade';
	$h++;

	$head[$h][0] = dol_buildpath('/sellyoursaas/backoffice/instance_users'.$prefix.'.php',1).'?'.(get_class($object)=='Dolicloud_customers'?'instanceoldid='.$object->id:'id='.$object->id);
	$head[$h][1] = $langs->trans("Users");
	$head[$h][2] = 'users';
	$h++;

	/*$head[$h][0] = dol_buildpath('/sellyoursaas/backoffice/dolicloud_card_payments'.$prefix.'.php',1).'?'.(get_class($object)=='Dolicloud_customers'?'instanceoldid='.$object->id:'id='.$object->id);
	$head[$h][1] = $langs->trans("Payments");
	$head[$h][2] = 'payments';
	$h++;*/

	$head[$h][0] = dol_buildpath('/sellyoursaas/backoffice/instance_backup'.$prefix.'.php',1).'?'.(get_class($object)=='Dolicloud_customers'?'instanceoldid='.$object->id:'id='.$object->id);
	$head[$h][1] = $langs->trans("Backup");
	$head[$h][2] = 'backup';
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
