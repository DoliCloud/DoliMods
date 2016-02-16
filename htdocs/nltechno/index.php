<?php
/* Copyright (C) 2008 Laurent Destailleur  <eldy@users.sourceforge.net>
 */

/**
 *   	\file       htdocs/nltechno/index.php
 *		\ingroup    google
 *		\brief      Main NLTechno area
 *		\author		Laurent Destailleur
 */

if (! empty($GLOBALS['CALLFORCONFIG']))
{
	include 'params.php';

	return;
}


$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");
if (! $res && file_exists("../../../dolibarr/htdocs/main.inc.php")) $res=include("../../../dolibarr/htdocs/main.inc.php");     // Used on dev env only
if (! $res && file_exists("../../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../../dolibarr/htdocs/main.inc.php");   // Used on dev env only
if (! $res && file_exists("../../../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../../../dolibarr/htdocs/main.inc.php");   // Used on dev env only
if (! $res) die("Include of main fails");
include_once DOL_DOCUMENT_ROOT.'/core/class/doleditor.class.php';
include_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';

$action=GETPOST('action');

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
//$langs->load("nltechno");
$langs->load("companies");
$langs->load("other");


// Get parameters
$socid = isset($_GET["socid"])?$_GET["socid"]:'';

// Protection
if (! $user->rights->nltechno->liens->voir)
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

print_fiche_titre("NLTechno information");
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
