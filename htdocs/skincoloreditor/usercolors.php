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
 *	    \file       htdocs/skincoloreditor/usercolors.php
 *      \ingroup    skincoloreditor
 *      \brief      Page to setup SkincolorEditor for user
 */

define('NOCSRFCHECK',1);

$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");
if (! $res && @file_exists("../../../../main.inc.php")) $res=@include("../../../../main.inc.php");
if (! $res && @file_exists("../../../../../main.inc.php")) $res=@include("../../../../../main.inc.php");
if (! $res && preg_match('/\/(?:custom|nltechno)([^\/]*)\//',$_SERVER["PHP_SELF"],$reg)) $res=@include("../../../dolibarr".$reg[1]."/htdocs/main.inc.php"); // Used on dev env only
if (! $res) die("Include of main fails");
require_once(DOL_DOCUMENT_ROOT."/core/lib/usergroups.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/admin.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/functions2.lib.php");
require_once(DOL_DOCUMENT_ROOT.'/core/class/html.formadmin.class.php');
require_once(DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php');


$langs->load("users");
$langs->load("admin");
$langs->load("other");
$langs->load("skincoloreditor@skincoloreditor");

$id = GETPOST('id', 'int');

// Security check
$socid=0;
if ($user->societe_id > 0) $socid = $user->societe_id;
$feature2 = (($socid && $user->rights->user->self->creer)?'':'user');
if ($user->id == $id) $feature2=''; // A user can always read its own card
$result = restrictedArea($user, 'user', $id, '&user', $feature2);

$fuser = new User($db);
$fuser->fetch($id);
$fuser->getrights();

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
    $tab[$name]=$value;
	$res = dol_set_user_param($db, $conf, $fuser, $tab);

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
    $tab['THEME_ELDY_RGB']=GETPOST('THEME_ELDY_RGB','alpha');
    $tab['THEME_ELDY_FONT_SIZE1']=GETPOST('THEME_ELDY_FONT_SIZE1','alpha');
    $tab['THEME_ELDY_USE_HOVER']=GETPOST('THEME_ELDY_USE_HOVER','alpha');

    $listofkey=array('THEME_ELDY_BACKTABCARD1');
    foreach($listofkey as $key)
    {
    	if (isset($_POST[$key])) $tab[$key]=GETPOST($key,'alpha');
    }

    $res = dol_set_user_param($db, $conf, $fuser, $tab);

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

$form=new Form($db);
$formother=new FormOther($db);

llxHeader('','SkinColorEditor',$linktohelp);

$head = user_prepare_head($fuser);

$title = $langs->trans("User");
dol_fiche_head($head, 'tabskincoloreditors', $title, 0, 'user');

print '<table class="border" width="100%">';

// Ref
print '<tr><td width="25%" valign="top">'.$langs->trans("Ref").'</td>';
print '<td colspan="2">';
print $form->showrefnav($fuser,'id','',$user->rights->user->user->lire || $user->admin);
print '</td>';
print '</tr>';

// LAstname
print '<tr><td width="25%" valign="top">'.$langs->trans("LastName").'</td>';
print '<td colspan="2">'.$fuser->lastname.'</td>';
print "</tr>\n";

// Firstname
print '<tr><td width="25%" valign="top">'.$langs->trans("FirstName").'</td>';
print '<td colspan="2">'.$fuser->firstname.'</td>';
print "</tr>\n";

print '</table>';

dol_fiche_end();

print '<br>';

print $langs->trans("SkinColorEditorDescUser").'<br>';
print '<br>';

print $langs->trans("ActivateColorPersonalizingUser").': &nbsp; ';
$name='THEME_ELDY_ENABLE_PERSONALIZED';
if (empty($fuser->conf->$name))
{
	if (empty($dolibarr_main_demo))
	{
		print '<a href="'.$_SERVER["PHP_SELF"].'?action=set&amp;name='.$name.'&amp;value=1&amp;id='.$fuser->id.'">';
	    print img_picto($langs->trans("Disabled"),'switch_off');
	    print '</a>';
	}
	else
	{
	    print '<a href="#">';
	    print img_picto($langs->trans("DisabledInDemoMode"),'switch_off');
	    print '</a>';
	}
}
else
{
    print '<a href="'.$_SERVER["PHP_SELF"].'?action=set&amp;name='.$name.'&amp;value=0&amp;id='.$fuser->id.'">';
    print img_picto($langs->trans("Enabled"),'switch_on');
    print '</a>';
}
print '<br><br><br>';

if ($conf->theme != 'eldy')
{
    print '<div class="warning">'.img_warning().' '.$langs->trans("WarningSkinMustBeEldy",$conf->theme).'</div>';
}

dol_htmloutput_mesg($mesg);


if (! empty($fuser->conf->THEME_ELDY_ENABLE_PERSONALIZED))
{
    print '<form name="formcolor" method="POST" action="'.$_SERVER["PHP_SELF"].'">';
    print '<input type="hidden" name="action" value="setcolor">';
    print '<input type="hidden" name="id" value="'.$fuser->id.'">';

    dol_fiche_head();

    // Color
    print $langs->trans("SelectMainColor").' ';
    $defcolor=$conf->global->THEME_ELDY_RGB;
    if (isset($fuser->conf->THEME_ELDY_RGB)) $defcolor=$fuser->conf->THEME_ELDY_RGB;
    print $formother->selectColor($defcolor,'THEME_ELDY_RGB').'<br>';

    // Font size
    print $langs->trans("FontSize").': <input type="text" class="flat" name="THEME_ELDY_FONT_SIZE1" size="4" value="'.$fuser->conf->THEME_ELDY_FONT_SIZE1.'"><br>';

    // Use hover
    print $langs->trans("UseHoverOnLists").': <input type="checkbox" class="flat" name="THEME_ELDY_USE_HOVER" '.(empty($fuser->conf->THEME_ELDY_USE_HOVER)?'':' checked="checked"').'"><br>';

    print '<br><br><br>';
	print $langs->trans("ForceSpecificValue").':<br><br>';

    // Force specific value
    print $langs->trans("SelectTabColor2").' ';
    $defcolor=$conf->global->THEME_ELDY_BACKTABCARD2;
    if (isset($fuser->conf->THEME_ELDY_BACKTABCARD2)) $defcolor=$fuser->conf->THEME_ELDY_BACKTABCARD2;
    print $formother->selectColor($defcolor,'THEME_ELDY_BACKTABCARD2').'<br>';
	print $langs->trans("SelectTabColor1").' ';
    $defcolor=$conf->global->THEME_ELDY_BACKTABCARD1;
    if (isset($fuser->conf->THEME_ELDY_BACKTABCARD1)) $defcolor=$fuser->conf->THEME_ELDY_BACKTABCARD1;
    print $formother->selectColor($defcolor,'THEME_ELDY_BACKTABCARD1').'<br>';

    dol_fiche_end();


    print '<div align="center"><input type="submit" class="button" name="save" value="'.$langs->trans("Save").'"></div>';

    print '</form>';
}


llxFooter();

if (is_object($db)) $db->close();
