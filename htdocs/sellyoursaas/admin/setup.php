<?php
/* Copyright (C) 2012 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 *     \file       htdocs/sellyoursaas/admin/sellyoursaas.php
 *     \brief      Page administration module SellYourSaas
 */


if (! defined('NOSCANPOSTFORINJECTION')) define('NOSCANPOSTFORINJECTION','1');		// Do not check anti CSRF attack test


// Load Dolibarr environment
$res=0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (! $res && ! empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) $res=@include($_SERVER["CONTEXT_DOCUMENT_ROOT"]."/main.inc.php");
// Try main.inc.php into web root detected using web root caluclated from SCRIPT_FILENAME
$tmp=empty($_SERVER['SCRIPT_FILENAME'])?'':$_SERVER['SCRIPT_FILENAME'];$tmp2=realpath(__FILE__); $i=strlen($tmp)-1; $j=strlen($tmp2)-1;
while($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i]==$tmp2[$j]) { $i--; $j--; }
if (! $res && $i > 0 && file_exists(substr($tmp, 0, ($i+1))."/main.inc.php")) $res=@include(substr($tmp, 0, ($i+1))."/main.inc.php");
if (! $res && $i > 0 && file_exists(dirname(substr($tmp, 0, ($i+1)))."/main.inc.php")) $res=@include(dirname(substr($tmp, 0, ($i+1)))."/main.inc.php");
// Try main.inc.php using relative path
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");
if (! $res) die("Include of main fails");

require_once(DOL_DOCUMENT_ROOT."/core/lib/admin.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/files.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/images.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/class/html.formother.class.php");
require_once(DOL_DOCUMENT_ROOT."/categories/class/categorie.class.php");

// Access control
if (! $user->admin) accessforbidden();

// Parameters
$action = GETPOST('action', 'alpha');
$backtopage = GETPOST('backtopage', 'alpha');

$langs->load("admin");
$langs->load("errors");
$langs->load("install");
$langs->load("sellyoursaas@sellyoursaas");

//exit;


/*
 * Actions
 */

if ($action == 'set')
{
	$error=0;

	if (! $error)
	{
		dolibarr_set_const($db,"SELLYOURSAAS_NAME",GETPOST("SELLYOURSAAS_NAME"),'chaine',0,'',$conf->entity);

		dolibarr_set_const($db,"SELLYOURSAAS_MAIN_DOMAIN_NAME",GETPOST("SELLYOURSAAS_MAIN_DOMAIN_NAME"),'chaine',0,'',$conf->entity);
		dolibarr_set_const($db,"SELLYOURSAAS_SUB_DOMAIN_NAMES",GETPOST("SELLYOURSAAS_SUB_DOMAIN_NAMES"),'chaine',0,'',$conf->entity);
		dolibarr_set_const($db,"SELLYOURSAAS_SUB_DOMAIN_IP",GETPOST("SELLYOURSAAS_SUB_DOMAIN_IP"),'chaine',0,'',$conf->entity);

		dolibarr_set_const($db,"SELLYOURSAAS_MAIN_EMAIL",GETPOST("SELLYOURSAAS_MAIN_EMAIL"),'chaine',0,'',$conf->entity);
		dolibarr_set_const($db,"SELLYOURSAAS_SUPERVISION_EMAIL",GETPOST("SELLYOURSAAS_SUPERVISION_EMAIL"),'chaine',0,'',$conf->entity);
		dolibarr_set_const($db,"SELLYOURSAAS_NOREPLY_EMAIL",GETPOST("SELLYOURSAAS_NOREPLY_EMAIL"),'chaine',0,'',$conf->entity);

		$dir=GETPOST("DOLICLOUD_SCRIPTS_PATH");
		if (! dol_is_dir($dir)) setEventMessage($langs->trans("ErrorDirNotFound",$dir),'warnings');
		dolibarr_set_const($db,"DOLICLOUD_SCRIPTS_PATH",GETPOST("DOLICLOUD_SCRIPTS_PATH"),'chaine',0,'',$conf->entity);

		$dir=GETPOST("DOLICLOUD_LASTSTABLEVERSION_DIR");
		if (! dol_is_dir($dir)) setEventMessage($langs->trans("ErrorDirNotFound",$dir),'warnings');
		dolibarr_set_const($db,"DOLICLOUD_LASTSTABLEVERSION_DIR",GETPOST("DOLICLOUD_LASTSTABLEVERSION_DIR"),'chaine',0,'',$conf->entity);

		$dir=GETPOST("DOLICLOUD_INSTANCES_PATH");
		if (! dol_is_dir($dir)) setEventMessage($langs->trans("ErrorDirNotFound",$dir),'warnings');
		dolibarr_set_const($db,"DOLICLOUD_INSTANCES_PATH",GETPOST("DOLICLOUD_INSTANCES_PATH"),'chaine',0,'',$conf->entity);

		$dir=GETPOST("DOLICLOUD_BACKUP_PATH");
		if (! dol_is_dir($dir)) setEventMessage($langs->trans("ErrorDirNotFound",$dir),'warnings');
		dolibarr_set_const($db,"DOLICLOUD_BACKUP_PATH",GETPOST("DOLICLOUD_BACKUP_PATH"),'chaine',0,'',$conf->entity);

		dolibarr_set_const($db,"SELLYOURSAAS_DEFAULT_PRODUCT",GETPOST("SELLYOURSAAS_DEFAULT_PRODUCT"),'chaine',0,'',$conf->entity);
		dolibarr_set_const($db,"SELLYOURSAAS_DEFAULT_PRODUCT_FOR_USERS",GETPOST("SELLYOURSAAS_DEFAULT_PRODUCT_FOR_USERS"),'chaine',0,'',$conf->entity);

		dolibarr_set_const($db,"SELLYOURSAAS_DEFAULT_CUSTOMER_CATEG",GETPOST("SELLYOURSAAS_DEFAULT_CUSTOMER_CATEG"),'chaine',0,'',$conf->entity);
		dolibarr_set_const($db,"SELLYOURSAAS_DEFAULT_RESELLER_CATEG",GETPOST("SELLYOURSAAS_DEFAULT_RESELLER_CATEG"),'chaine',0,'',$conf->entity);
		dolibarr_set_const($db,"SELLYOURSAAS_DEFAULT_PRODUCT_CATEG",GETPOST("SELLYOURSAAS_DEFAULT_PRODUCT_CATEG"),'chaine',0,'',$conf->entity);

		dolibarr_set_const($db,"SELLYOURSAAS_REFS_URL",GETPOST("SELLYOURSAAS_REFS_URL"),'chaine',0,'',$conf->entity);

		dolibarr_set_const($db,"SELLYOURSAAS_ACCOUNT_URL",GETPOST("SELLYOURSAAS_ACCOUNT_URL"),'chaine',0,'',$conf->entity);

		dolibarr_set_const($db,"SELLYOURSAAS_REMOTE_SERVER_IP_FOR_INSTANCES",GETPOST("SELLYOURSAAS_REMOTE_SERVER_IP_FOR_INSTANCES"),'chaine',0,'',$conf->entity);

		dolibarr_set_const($db,"SELLYOURSAAS_MYACCOUNT_FOOTER",GETPOST("SELLYOURSAAS_MYACCOUNT_FOOTER",'none'),'chaine',0,'',$conf->entity);
		dolibarr_set_const($db,"SELLYOURSAAS_PUBLIC_KEY",GETPOST("SELLYOURSAAS_PUBLIC_KEY",'none'),'chaine',0,'',$conf->entity);

		dolibarr_set_const($db,"SELLYOURSAAS_ANONYMOUSUSER",GETPOST("SELLYOURSAAS_ANONYMOUSUSER",'none'),'chaine',0,'',$conf->entity);

		dolibarr_set_const($db,"SELLYOURSAAS_NBDAYS_BEFORE_TRIAL_END_FOR_SOFT_ALERT",GETPOST("SELLYOURSAAS_NBDAYS_BEFORE_TRIAL_END_FOR_SOFT_ALERT",'int'),'chaine',0,'',$conf->entity);
		dolibarr_set_const($db,"SELLYOURSAAS_NBDAYS_BEFORE_TRIAL_END_FOR_HARD_ALERT",GETPOST("SELLYOURSAAS_NBDAYS_BEFORE_TRIAL_END_FOR_HARD_ALERT",'int'),'chaine',0,'',$conf->entity);
		dolibarr_set_const($db,"SELLYOURSAAS_NBDAYS_AFTER_EXPIRATION_BEFORE_TRIAL_UNDEPLOYMENT",GETPOST("SELLYOURSAAS_NBDAYS_AFTER_EXPIRATION_BEFORE_TRIAL_UNDEPLOYMENT",'int'),'chaine',0,'',$conf->entity);
		dolibarr_set_const($db,"SELLYOURSAAS_NBDAYS_AFTER_EXPIRATION_BEFORE_PAID_UNDEPLOYMENT",GETPOST("SELLYOURSAAS_NBDAYS_AFTER_EXPIRATION_BEFORE_PAID_UNDEPLOYMENT",'int'),'chaine',0,'',$conf->entity);


		$varforimage='logo'; $dirforimage=$conf->mycompany->dir_output.'/logos/';
		if ($_FILES[$varforimage]["tmp_name"])
		{
			if (preg_match('/([^\\/:]+)$/i',$_FILES[$varforimage]["name"],$reg))
			{
				$original_file=$reg[1];

				$isimage=image_format_supported($original_file);
				if ($isimage >= 0)
				{
					dol_syslog("Move file ".$_FILES[$varforimage]["tmp_name"]." to ".$dirforimage.$original_file);
					if (! is_dir($dirforimage))
					{
						dol_mkdir($dirforimage);
					}
					$result=dol_move_uploaded_file($_FILES[$varforimage]["tmp_name"],$dirforimage.$original_file,1,0,$_FILES[$varforimage]['error']);
					if ($result > 0)
					{
						dolibarr_set_const($db, "SELLYOURSAAS_LOGO",$original_file,'chaine',0,'',$conf->entity);

						// Create thumbs of logo (Note that PDF use original file and not thumbs)
						if ($isimage > 0)
						{
							// Create thumbs
							//$object->addThumbs($newfile);    // We can't use addThumbs here yet because we need name of generated thumbs to add them into constants. TODO Check if need such constants. We should be able to retreive value with get...

							// Create small thumb, Used on logon for example
							$imgThumbSmall = vignette($dirforimage.$original_file, $maxwidthsmall, $maxheightsmall, '_small', $quality);
							if (image_format_supported($imgThumbSmall) >= 0 && preg_match('/([^\\/:]+)$/i',$imgThumbSmall,$reg))
							{
								$imgThumbSmall = $reg[1];    // Save only basename
								dolibarr_set_const($db, "SELLYOURSAAS_LOGO_SMALL",$imgThumbSmall,'chaine',0,'',$conf->entity);
							}
							else dol_syslog($imgThumbSmall);

							// Create mini thumb, Used on menu or for setup page for example
							$imgThumbMini = vignette($dirforimage.$original_file, $maxwidthmini, $maxheightmini, '_mini', $quality);
							if (image_format_supported($imgThumbMini) >= 0 && preg_match('/([^\\/:]+)$/i',$imgThumbMini,$reg))
							{
								$imgThumbMini = $reg[1];     // Save only basename
								dolibarr_set_const($db, "SELLYOURSAAS_LOGO_MINI",$imgThumbMini,'chaine',0,'',$conf->entity);
							}
							else dol_syslog($imgThumbMini);
						}
						else dol_syslog("ErrorImageFormatNotSupported",LOG_WARNING);
					}
					else if (preg_match('/^ErrorFileIsInfectedWithAVirus/',$result))
					{
						$error++;
						$langs->load("errors");
						$tmparray=explode(':',$result);
						setEventMessages($langs->trans('ErrorFileIsInfectedWithAVirus',$tmparray[1]), null, 'errors');
					}
					else
					{
						$error++;
						setEventMessages($langs->trans("ErrorFailedToSaveFile"), null, 'errors');
					}
				}
				else
				{
					$error++;
					$langs->load("errors");
					setEventMessages($langs->trans("ErrorBadImageFormat"), null, 'errors');
				}
			}
		}

	}
}

if ($action == 'setstratus5')
{
	$error=0;

	if (! $error)
	{
		$dir=GETPOST("DOLICLOUD_EXT_HOME");
		dolibarr_set_const($db,"DOLICLOUD_EXT_HOME",GETPOST("DOLICLOUD_EXT_HOME"),'chaine',0,'',$conf->entity);

		dolibarr_set_const($db,"DOLICLOUD_DATABASE_HOST",GETPOST("DOLICLOUD_DATABASE_HOST"),'chaine',0,'',$conf->entity);
		dolibarr_set_const($db,"DOLICLOUD_DATABASE_PORT",GETPOST("DOLICLOUD_DATABASE_PORT"),'chaine',0,'',$conf->entity);
		dolibarr_set_const($db,"DOLICLOUD_DATABASE_NAME",GETPOST("DOLICLOUD_DATABASE_NAME"),'chaine',0,'',$conf->entity);
		dolibarr_set_const($db,"DOLICLOUD_DATABASE_USER",GETPOST("DOLICLOUD_DATABASE_USER"),'chaine',0,'',$conf->entity);
		dolibarr_set_const($db,"DOLICLOUD_DATABASE_PASS",GETPOST("DOLICLOUD_DATABASE_PASS"),'chaine',0,'',$conf->entity);

		setEventMessage($langs->trans("Saved"),'mesgs');
	}
}

if ($action == 'removelogo')
{
	require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';

	$logofile=$conf->mycompany->dir_output.'/logos/'.$conf->global->SELLYOURSAAS_LOGO;
	if ($conf->global->SELLYOURSAAS_LOGO != '') dol_delete_file($logofile);
	dolibarr_del_const($db, "SELLYOURSAAS_LOGO",$conf->entity);

	$logosmallfile=$conf->mycompany->dir_output.'/logos/thumbs/'.$conf->global->SELLYOURSAAS_LOGO_SMALL;
	if ($conf->global->SELLYOURSAAS_LOGO_SMALL != '') dol_delete_file($logosmallfile);
	dolibarr_del_const($db, "SELLYOURSAAS_LOGO_SMALL",$conf->entity);

	$logominifile=$conf->mycompany->dir_output.'/logos/thumbs/'.$conf->global->SELLYOURSAAS_LOGO_MINI;
	if ($conf->global->SELLYOURSAAS_LOGO_MINI != '') dol_delete_file($logominifile);
	dolibarr_del_const($db, "SELLYOURSAAS_LOGO_MINI",$conf->entity);
}


/*
 * View
 */

$formother=new FormOther($db);
$form=new Form($db);

$help_url="";
llxHeader("",$langs->trans("SellYouSaasSetup"),$help_url);

$linkback='<a href="'.($backtopage?$backtopage:DOL_URL_ROOT.'/admin/modules.php?restore_lastsearch_values=1').'">'.$langs->trans("BackToModuleList").'</a>';
print_fiche_titre($langs->trans('SellYouSaasSetup'),$linkback,'setup');

//$head=array();
//dol_fiche_head($head, 'serversetup', $langs->trans("SellYourSaas"), -1);

print $langs->trans("SellYouSaasDesc")."<br>\n";
print "<br>\n";

$error=0;


print '<form enctype="multipart/form-data" method="POST" action="'.$_SERVER["PHP_SELF"].'" name="form_index">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="set">';

print '<table class="noborder" width="100%">';
print '<tr class="liste_titre">';
print '<td class="titlefield">'.$langs->trans("Parameter").'</td><td>'.$langs->trans("Value").'</td>';
print '<td>'.$langs->trans("Examples").'<div class="floatright"><input type="submit" class="button" value="'.$langs->trans("Save").'"></div></td>';
print "</tr>\n";

print '<tr class="oddeven"><td>'.$langs->trans("SellYourSaasName").'</td>';
print '<td>';
print '<input type="text" name="SELLYOURSAAS_NAME" value="'.$conf->global->SELLYOURSAAS_NAME.'">';
print '</td>';
print '<td>My SaaS service</td>';
print '</tr>';

print '<tr class="oddeven"><td>'.$langs->trans("SellYourSaasMainDomain").'</td>';
print '<td>';
print '<input type="text" name="SELLYOURSAAS_MAIN_DOMAIN_NAME" value="'.$conf->global->SELLYOURSAAS_MAIN_DOMAIN_NAME.'">';
print '</td>';
print '<td>mysaas.com</td>';
print '</tr>';

print '<tr class="oddeven"><td>'.$langs->trans("SellYourSaasSubDomains").'</td>';
print '<td>';
print '<input type="text" name="SELLYOURSAAS_SUB_DOMAIN_NAMES" value="'.$conf->global->SELLYOURSAAS_SUB_DOMAIN_NAMES.'">';
print '</td>';
print '<td>with.mydomain.com,on.myotherdomain.com...</td>';
print '</tr>';

print '<tr class="oddeven"><td>'.$langs->trans("SellYourSaasSubDomainsIP").'</td>';
print '<td>';
print '<input type="text" name="SELLYOURSAAS_SUB_DOMAIN_IP" value="'.$conf->global->SELLYOURSAAS_SUB_DOMAIN_IP.'">';
print '</td>';
print '<td>192.168.0.1,123.456.789.012...</td>';
print '</tr>';

print '<tr class="oddeven"><td>'.$langs->trans("SellYourSaasMainEmail").'</td>';
print '<td>';
print '<input type="text" name="SELLYOURSAAS_MAIN_EMAIL" value="'.$conf->global->SELLYOURSAAS_MAIN_EMAIL.'">';
print '</td>';
print '<td>contact@mysaas.com</td>';
print '</tr>';

print '<tr class="oddeven"><td>'.$langs->trans("SellYourSaasSupervisionEmail").'</td>';
print '<td>';
print '<input type="text" name="SELLYOURSAAS_SUPERVISION_EMAIL" value="'.$conf->global->SELLYOURSAAS_SUPERVISION_EMAIL.'">';
print '</td>';
print '<td>supervision@mysaas.com</td>';
print '</tr>';

print '<tr class="oddeven"><td>'.$langs->trans("SellYourSaasNoReplyEmail").'</td>';
print '<td>';
print '<input type="text" name="SELLYOURSAAS_NOREPLY_EMAIL" value="'.$conf->global->SELLYOURSAAS_NOREPLY_EMAIL.'">';
print '</td>';
print '<td>noreply@mysaas.com</td>';
print '</tr>';

print '<tr class="oddeven"><td>'.$langs->trans("DirForScriptPath").'</td>';
print '<td>';
print '<input class="minwidth500" type="text" name="DOLICLOUD_SCRIPTS_PATH" value="'.$conf->global->DOLICLOUD_SCRIPTS_PATH.'">';
print '</td>';
print '<td>'.dol_buildpath('sellyoursaas/scripts').'</td>';
print '</tr>';

print '<tr class="oddeven"><td>'.$langs->trans("DirForLastStableVersionOfDolibarr").'</td>';
print '<td>';
print '<input class="minwidth500" type="text" name="DOLICLOUD_LASTSTABLEVERSION_DIR" value="'.$conf->global->DOLICLOUD_LASTSTABLEVERSION_DIR.'">';
print '</td>';
print '<td>'.$dolibarr_main_data_root.'/sellyoursaas/git/dolibarr_x.y</td>';
print '</tr>';

print '<tr class="oddeven"><td>'.$langs->trans("DirForDoliCloudInstances").'</td>';
print '<td>';
print '<input size="40" type="text" name="DOLICLOUD_INSTANCES_PATH" value="'.$conf->global->DOLICLOUD_INSTANCES_PATH.'">';
print '</td>';
print '<td>/home/jail/home</td>';
print '</tr>';

print '<tr class="oddeven"><td>'.$langs->trans("DirForDoliCloudBackupInstances").'</td>';
print '<td>';
print '<input size="40" type="text" name="DOLICLOUD_BACKUP_PATH" value="'.$conf->global->DOLICLOUD_BACKUP_PATH.'">';
print '</td>';
print '<td>/home/jail/backup</td>';
print '</tr>';

print '<tr class="oddeven"><td>'.$langs->trans("DefaultProductForInstances").'</td>';
print '<td>';
$defaultproductid=$conf->global->SELLYOURSAAS_DEFAULT_PRODUCT;
print $form->select_produits($defaultproductid, 'SELLYOURSAAS_DEFAULT_PRODUCT');
print '</td>';
print '<td>My SaaS service for instance</td>';
print '</tr>';

print '<tr class="oddeven"><td>'.$langs->trans("DefaultProductForUsers").'</td>';
print '<td>';
$defaultproductforusersid=$conf->global->SELLYOURSAAS_DEFAULT_PRODUCT_FOR_USERS;
print $form->select_produits($defaultproductforusersid, 'SELLYOURSAAS_DEFAULT_PRODUCT_FOR_USERS');
print '</td>';
print '<td>My SaaS service for users</td>';
print '</tr>';

print '<tr class="oddeven"><td>'.$langs->trans("DefaultCategoryForSaaSCustomers").'</td>';
print '<td>';
$defaultcustomercategid=$conf->global->SELLYOURSAAS_DEFAULT_CUSTOMER_CATEG;
print $formother->select_categories(Categorie::TYPE_CUSTOMER, $defaultcustomercategid, 'SELLYOURSAAS_DEFAULT_CUSTOMER_CATEG', 0, 1, 'miwidth300');
print '</td>';
print '<td>SaaS Customers</td>';
print '</tr>';

print '<tr class="oddeven"><td>'.$langs->trans("DefaultCategoryForSaaSResellers").'</td>';
print '<td>';
$defaultcustomercategid=$conf->global->SELLYOURSAAS_DEFAULT_RESELLER_CATEG;
print $formother->select_categories(Categorie::TYPE_SUPPLIER, $defaultcustomercategid, 'SELLYOURSAAS_DEFAULT_RESELLER_CATEG', 0, 1, 'miwidth300');
print '</td>';
print '<td>SaaS Resellers</td>';
print '</tr>';

print '<tr class="oddeven"><td>'.$langs->trans("DefaultCategoryForSaaSServices").'</td>';
print '<td>';
$defaultproductcategid=$conf->global->SELLYOURSAAS_DEFAULT_PRODUCT_CATEG;
print $formother->select_categories(Categorie::TYPE_PRODUCT, $defaultproductcategid, 'SELLYOURSAAS_DEFAULT_PRODUCT_CATEG', 0, 1, 'miwidth300');
print '</td>';
print '<td>SaaS Products</td>';
print '</tr>';

print '<tr class="oddeven"><td>'.$langs->trans("RefsUrl", DOL_DOCUMENT_ROOT.'/sellyoursaas/git');
print '</td>';
print '<td>';
print '<input class="minwidth300" type="text" name="SELLYOURSAAS_REFS_URL" value="'.$conf->global->SELLYOURSAAS_REFS_URL.'">';
print '</td>';
print '<td>https://mysaas.com/refs</td>';
print '</tr>';

print '<tr class="oddeven"><td>'.$langs->trans("SellYourSaasAccountUrl").'</td>';
print '<td>';
print '<input class="minwidth300" type="text" name="SELLYOURSAAS_ACCOUNT_URL" value="'.$conf->global->SELLYOURSAAS_ACCOUNT_URL.'">';
print '</td>';
print '<td>https://myaccount.mysaas.com (the virtual host must link to <strong>'.dol_buildpath('sellyoursaas/myaccount').'</strong>)</td>';
print '</tr>';

print '<tr class="oddeven"><td>'.$langs->trans("SellYourSaasRemoteServerIpForDeployement").'</td>';
print '<td>';
print '<input class="minwidth300" type="text" name="SELLYOURSAAS_REMOTE_SERVER_IP_FOR_INSTANCES" value="'.$conf->global->SELLYOURSAAS_REMOTE_SERVER_IP_FOR_INSTANCES.'">';
print '</td>';
print '<td>127.0.0.1</td>';
print '</tr>';

print '<tr class="oddeven"><td>'.$langs->trans("FooterContent").'</td>';
print '<td>';
print '<textarea name="SELLYOURSAAS_MYACCOUNT_FOOTER" class="quatrevingtpercent" rows="3">'.$conf->global->SELLYOURSAAS_MYACCOUNT_FOOTER.'</textarea>';
print '</td>';
print '<td>&lt;script&gt;Your google analytics code&lt;/script&gt;</td>';
print '</tr>';

print '<tr class="oddeven"><td>'.$langs->trans("SSHPublicKey").'</td>';
print '<td>';
print '<textarea name="SELLYOURSAAS_PUBLIC_KEY" class="quatrevingtpercent" rows="3">'.$conf->global->SELLYOURSAAS_PUBLIC_KEY.'</textarea>';
print '</td>';
print '<td>Your SSH public key deployed into each new instance</td>';
print '</tr>';

print '<tr class="oddeven"><td>'.$langs->trans("AnonymousUser").'</td>';
print '<td>';
print $form->select_dolusers($conf->global->SELLYOURSAAS_ANONYMOUSUSER, 'SELLYOURSAAS_ANONYMOUSUSER', 1);
print '</td>';
print '<td>User used for all anonymous action (registering, actions from customer dashboard, ...)</td>';
print '</tr>';

print '<tr class="oddeven"><td>'.$langs->trans("SELLYOURSAAS_NBDAYS_BEFORE_TRIAL_END_FOR_SOFT_ALERT").'</td>';
print '<td>';
print '<input class="maxwidth50" type="text" name="SELLYOURSAAS_NBDAYS_BEFORE_TRIAL_END_FOR_SOFT_ALERT" value="'.$conf->global->SELLYOURSAAS_NBDAYS_BEFORE_TRIAL_END_FOR_SOFT_ALERT.'">';
print '</td>';
print '<td>7</td>';
print '</tr>';

print '<tr class="oddeven"><td>'.$langs->trans("SELLYOURSAAS_NBDAYS_BEFORE_TRIAL_END_FOR_HARD_ALERT").'</td>';
print '<td>';
print '<input class="maxwidth50" type="text" name="SELLYOURSAAS_NBDAYS_BEFORE_TRIAL_END_FOR_HARD_ALERT" value="'.$conf->global->SELLYOURSAAS_NBDAYS_BEFORE_TRIAL_END_FOR_HARD_ALERT.'">';
print '</td>';
print '<td>2</td>';
print '</tr>';

print '<tr class="oddeven"><td>'.$langs->trans("SELLYOURSAAS_NBDAYS_AFTER_EXPIRATION_BEFORE_TRIAL_UNDEPLOYMENT").'</td>';
print '<td>';
print '<input class="maxwidth50" type="text" name="SELLYOURSAAS_NBDAYS_AFTER_EXPIRATION_BEFORE_TRIAL_UNDEPLOYMENT" value="'.$conf->global->SELLYOURSAAS_NBDAYS_AFTER_EXPIRATION_BEFORE_TRIAL_UNDEPLOYMENT.'">';
print '</td>';
print '<td>15</td>';
print '</tr>';

print '<tr class="oddeven"><td>'.$langs->trans("SELLYOURSAAS_NBDAYS_AFTER_EXPIRATION_BEFORE_PAID_UNDEPLOYMENT").'</td>';
print '<td>';
print '<input class="maxwidth50" type="text" name="SELLYOURSAAS_NBDAYS_AFTER_EXPIRATION_BEFORE_PAID_UNDEPLOYMENT" value="'.$conf->global->SELLYOURSAAS_NBDAYS_AFTER_EXPIRATION_BEFORE_PAID_UNDEPLOYMENT.'">';
print '</td>';
print '<td>120</td>';
print '</tr>';

// Logo
print '<tr class="oddeven hideonsmartphone"><td><label for="logo">'.$langs->trans("Logo").' (png,jpg)</label></td><td>';
print '<table width="100%" class="nobordernopadding"><tr class="nocellnopadd"><td valign="middle" class="nocellnopadd">';
print '<input type="file" class="flat class=minwidth200" name="logo" id="logo">';
print '</td><td class="nocellnopadd" valign="middle" align="right">';
if (! empty($conf->global->SELLYOURSAAS_LOGO_MINI)) {
	print '<a href="'.$_SERVER["PHP_SELF"].'?action=removelogo">'.img_delete($langs->trans("Delete")).'</a>';
	if (file_exists($conf->mycompany->dir_output.'/logos/thumbs/'.$conf->global->SELLYOURSAAS_LOGO_MINI)) {
		print ' &nbsp; ';
		print '<img src="'.DOL_URL_ROOT.'/viewimage.php?modulepart=mycompany&amp;file='.urlencode('/thumbs/'.$conf->global->SELLYOURSAAS_LOGO_MINI).'">';
	}
} else {
	print '<img height="30" src="'.DOL_URL_ROOT.'/public/theme/common/nophoto.png">';
}
print '</td></tr></table>';
print '</td><td>';
print '</td></tr>';



print '</table>';

print "</form>\n";


print "<br>";


// Param
$var=true;
print '<form action="'.$_SERVER["PHP_SELF"].'" method="post">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="setstratus5">';

print '<strong>DoliCloud V1</strong>';
print '<table class="noborder" width="100%">';
print '<tr class="liste_titre">';
print '<td class="titlefield">'.$langs->trans("Parameter").'</td><td>'.$langs->trans("Value").'</td>';
print '<td>'.$langs->trans("Examples").'</td>';
print '<td align="right"><input type="submit" class="button" value="'.$langs->trans("Save").'"></td>';
print "</tr>\n";

print '<tr class="oddeven"><td>'.$langs->trans("DirForDoliCloudInstances").' (remote dir)</td>';
print '<td>';
print '<input size="40" type="text" name="DOLICLOUD_EXT_HOME" value="'.$conf->global->DOLICLOUD_EXT_HOME.'">';
print '</td>';
print '<td>/home/jail/home</td>';
print '<td>&nbsp;</td>';
print '</tr>';

print '<tr class="oddeven"><td>'.$langs->trans("DatabaseServer").'</td>';
print '<td>';
print '<input size="40" type="text" name="DOLICLOUD_DATABASE_HOST" value="'.$conf->global->DOLICLOUD_DATABASE_HOST.'">';
print '</td>';
print '<td>localhost</td>';
print '<td>&nbsp;</td>';
print '</tr>';

print '<tr class="oddeven"><td>'.$langs->trans("DatabasePort").'</td>';
print '<td>';
print '<input size="40" type="text" name="DOLICLOUD_DATABASE_PORT" value="'.$conf->global->DOLICLOUD_DATABASE_PORT.'">';
print '</td>';
print '<td>3306</td>';
print '<td>&nbsp;</td>';
print '</tr>';

print '<tr class="oddeven"><td>'.$langs->trans("DatabaseName").'</td>';
print '<td>';
print '<input size="40" type="text" name="DOLICLOUD_DATABASE_NAME" value="'.$conf->global->DOLICLOUD_DATABASE_NAME.'">';
print '</td>';
print '<td>dolicloud_saasplex</td>';
print '<td>&nbsp;</td>';
print '</tr>';

print '<tr class="oddeven"><td>'.$langs->trans("DatabaseUser").'</td>';
print '<td>';
print '<input size="40" type="text" name="DOLICLOUD_DATABASE_USER" value="'.$conf->global->DOLICLOUD_DATABASE_USER.'">';
print '</td>';
print '<td>dolicloud</td>';
print '<td>&nbsp;</td>';
print '</tr>';

print '<tr class="oddeven"><td>'.$langs->trans("DatabasePassword").'</td>';
print '<td>';
print '<input size="40" type="text" name="DOLICLOUD_DATABASE_PASS" value="'.$conf->global->DOLICLOUD_DATABASE_PASS.'">';
print '</td>';
print '<td></td>';
print '<td>&nbsp;</td>';
print '</tr>';

print '</table>';

print '</form>';

print '<br>';


//dol_fiche_end();


llxfooter();

$db->close();
