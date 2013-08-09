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
 */

/**
 *	    \file       htdocs/skincoloreditor/admin/quickeditor.php
 *      \ingroup    skincoloreditor
 *      \brief      Page to setup module SkincolorEditor
 */

define('NOCSRFCHECK',1);

$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");
if (! $res && file_exists("../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../dolibarr/htdocs/main.inc.php");     // Used on dev env only
if (! $res && file_exists("../../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../../dolibarr/htdocs/main.inc.php");   // Used on dev env only
if (! $res && file_exists("../../../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../../../dolibarr/htdocs/main.inc.php");   // Used on dev env only
if (! $res) die("Include of main fails");
require_once(DOL_DOCUMENT_ROOT."/core/lib/admin.lib.php");
require_once(DOL_DOCUMENT_ROOT.'/core/class/html.formadmin.class.php');
require_once(DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php');


if (!$user->admin) accessforbidden();

$langs->load("admin");
$langs->load("other");
$langs->load("skincoloreditor@skincoloreditor");

$def = array();
$action=GETPOST("action");
$actionsave=GETPOST("save");



/*
 * Actions
 */

if (preg_match('/^set/',$action))
{
    // This is to force to add a new param after css urls to force new file loading
    // This set must be done before calling llxHeader().
    $_SESSION['dol_resetcache']=dol_print_date(dol_now(),'dayhourlog');
}

if ($action == 'set')
{
	$name = GETPOST("name");
	$value = GETPOST("value");
	$res = dolibarr_set_const($db, $name, $value,'chaine',0,'',$conf->entity);

	if (! $res > 0) $error++;
 	if (! $error)
    {
        $mesg = "<font class=\"ok\">".$langs->trans("SetupSaved")."</font>";
    }
    else
    {
        $mesg = "<font class=\"error\">".$langs->trans("Error")."</font>";
    }
}

if ($action == 'setcolor')
{
	$res = dolibarr_set_const($db, 'THEME_ELDY_RGB', GETPOST('THEME_ELDY_RGB'),'chaine',0,'',$conf->entity);
	$res = dolibarr_set_const($db, 'THEME_ELDY_FONT_SIZE1', GETPOST('THEME_ELDY_FONT_SIZE1'),'chaine',0,'',$conf->entity);
	$res = dolibarr_set_const($db, 'THEME_ELDY_USE_HOVER', GETPOST('THEME_ELDY_USE_HOVER'),'chaine',0,'',$conf->entity);

	if (! $res > 0) $error++;
 	if (! $error)
    {
        $mesg = "<font class=\"ok\">".$langs->trans("SetupSaved")."</font>";
    }
    else
    {
        $mesg = "<font class=\"error\">".$langs->trans("Error")."</font>";
    }
}



/**
 * View
 */

$formother=new FormOther($db);

llxHeader('','SkinColorEditor',$linktohelp);

$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php">'.$langs->trans("BackToModuleList").'</a>';
print_fiche_titre($langs->trans("SkinColorEditorSetup"),$linkback,'setup');
print '<br>';

print $langs->trans("SkinColorEditorDesc").'<br>';
print '<br>';


$head=array();
$h=0;

$head[$h][0] = dol_buildpath("/skincoloreditor/admin/quickeditor.php",1);
$head[$h][1] = $langs->trans("SkinColorEditorFastEditor");
$head[$h][2] = 'fasteditor';
$h++;

$head[$h][0] = 'about.php';
$head[$h][1] = $langs->trans("About");
$head[$h][2] = 'tababout';
$h++;

dol_fiche_head($head,'fasteditor');


print '<br>';

print $langs->trans("ActivateColorPersonalizing").': &nbsp; ';
$name='THEME_ELDY_ENABLE_PERSONALIZED';
if (empty($conf->global->$name))
{
    print '<a href="'.$_SERVER["PHP_SELF"].'?action=set&amp;name='.$name.'&amp;value=1">';
    print img_picto($langs->trans("Disabled"),'switch_off');
    print '</a>';
}
else
{
    print '<a href="'.$_SERVER["PHP_SELF"].'?action=set&amp;name='.$name.'&amp;value=0">';
    print img_picto($langs->trans("Enabled"),'switch_on');
    print '</a>';
}


print '<br>';

if ($conf->theme != 'eldy')
{
	print '<br><br>';

	print '<div class="warning">'.img_warning().' '.$langs->trans("WarningSkinMustBeEldy",$conf->theme).'</div>';
}
else if (! empty($user->conf->THEME_ELDY_ENABLE_PERSONALIZED))
{
	print '<br><br>';

	print '<div class="warning">'.img_warning().' '.$langs->trans("YourUseHasPersonalized",dol_buildpath('/skincoloreditor/usercolors.php',1).'?id='.$user->id,$langs->transnoentitiesnoconv("ColorEditor")).'</div>';
}
print '<br>';

dol_htmloutput_mesg($mesg);


if (! empty($conf->global->THEME_ELDY_ENABLE_PERSONALIZED))
{
    /*
    $head[$h][0] = dol_buildpath("/skincoloreditor/admin/advancededitor.php",1);
    $head[$h][1] = $langs->trans("SkinColorEditorAdvancedEditor");
    $head[$h][2] = 'advancededitor';
    $h++;
	*/


    print '<form name="formcolor" method="POST" action="'.$_SERVER["PHP_SELF"].'">';
    print '<input type="hidden" name="action" value="setcolor">';
    print $langs->trans("SelectMainColor").' ';
    $defcolor=dechex(235).dechex(235).dechex(235);
    if (isset($conf->global->THEME_ELDY_RGB)) $defcolor=$conf->global->THEME_ELDY_RGB;

    // Color
    print $formother->selectColor($defcolor,'THEME_ELDY_RGB','formcolor',1).'<br><br>';

    // Font size
    print $langs->trans("FontSize").': <input type="text" class="flat" name="THEME_ELDY_FONT_SIZE1" size="4" value="'.$conf->global->THEME_ELDY_FONT_SIZE1.'"><br>';

    // Use hover
    print $langs->trans("UseHoverOnLists").': <input type="checkbox" class="flat" name="THEME_ELDY_USE_HOVER" '.(empty($conf->global->THEME_ELDY_USE_HOVER)?'':' checked="checked"').'"><br>';

    print '<br>';
    print '<div align="center"><input type="submit" class="button" name="save" value="'.$langs->trans("Save").'"></div>';
    print '</form>';
}


dol_fiche_end();


llxFooter();

if (is_object($db)) $db->close();
?>
