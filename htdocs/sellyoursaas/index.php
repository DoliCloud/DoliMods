<?php
/* Copyright (C) 2008 Laurent Destailleur  <eldy@users.sourceforge.net>
 */

/**
 *   	\file       htdocs/sellyoursaas/index.php
 *		\ingroup    google
 *		\brief      Main SellYourSaas area
 *		\author		Laurent Destailleur
 */

if (! empty($GLOBALS['CALLFORCONFIG']))
{
	include 'params.php';
	return;
}

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
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");
if (! $res) die("Include of main fails");

include_once DOL_DOCUMENT_ROOT.'/core/class/doleditor.class.php';
include_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';

$action=GETPOST('action','aZ09');

$langs->load("sellyoursaas@sellyoursaas");


/*
 * Actions
 */

if ($action == 'update')
{
	dolibarr_set_const($db,"NLTECHNO_NOTE",GETPOST("NLTECHNO_NOTE"),'chaine',0,'',$conf->entity);
}


/*
 * View
 */



// Load traductions files
//$langs->load("sellyoursaas");
$langs->load("companies");
$langs->load("other");


// Get parameters
$socid = isset($_GET["socid"])?$_GET["socid"]:'';

// Protection
if (! $user->rights->sellyoursaas->liens->voir)
{
	accessforbidden();
	exit;
}


/***************************************************
* PAGE
*
* Put here all code to build page
****************************************************/

llxHeader();

$form=new Form($db);

print_fiche_titre($langs->trans("SellYourSaasHomePage"));
print '<br>';

if ($action != 'edit')
{
	print dol_htmlcleanlastbr($conf->global->NLTECHNO_NOTE);

	print '<div class="tabsAction">';

	print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?action=edit">'.$langs->trans("Edit").'</a></div>';

	print '</div>';
}
else
{
	print '<form action="'.$_SERVER["PHP_SELF"].'" method="POST">';
	print '<input type="hidden" name="action" value="update">';
	$doleditor=new DolEditor('NLTECHNO_NOTE',$conf->global->NLTECHNO_NOTE,'',480,'Full');
	print $doleditor->Create(1);
	print '<br>';
	print '<input class="button" type="submit" name="'.$langs->trans("Save").'">';
	print '</form>';
}

llxFooter();

$db->close();
