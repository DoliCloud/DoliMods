<?php
/* Copyright (C) 2011-2013 Regis Houssin  <regis.houssin@capnetworks.com>
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
 *  \file       /milestone/admin/milestone.php
 *  \ingroup    milestone
 *  \brief      Administration/configuration of Milestone module
 */

$res=@include "../../main.inc.php";					// For root directory
if (! $res && file_exists($_SERVER['DOCUMENT_ROOT']."/main.inc.php"))
	$res=@include $_SERVER['DOCUMENT_ROOT']."/main.inc.php"; // Use on dev env only
if (! $res) $res=@include "../../../main.inc.php";		// For "custom" directory

require '../lib/milestone.lib.php';

$langs->load("admin");
$langs->load("milestone@milestone");

// Security check
if (! $user->admin)
	accessforbidden();

$action	= GETPOST('action', 'alpha');


/*
 * Action
 */
if (preg_match('/set_(.*)/',$action,$reg))
{
    $code=$reg[1];
    if (dolibarr_set_const($db, $code, 1, 'chaine', 0, '', $conf->entity) > 0)
    {
        Header("Location: ".$_SERVER["PHP_SELF"]);
        exit;
    }
    else
    {
        dol_print_error($db);
    }
}

if (preg_match('/del_(.*)/',$action,$reg))
{
    $code=$reg[1];
    if (dolibarr_del_const($db, $code, $conf->entity) > 0)
    {
        Header("Location: ".$_SERVER["PHP_SELF"]);
        exit;
    }
    else
    {
        dol_print_error($db);
    }
}

/*
 * View
 */

llxHeader('',$langs->trans("MilestoneSetup"));

$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php">'.$langs->trans("BackToModuleList").'</a>';
print_fiche_titre($langs->trans("MilestoneSetup"), $linkback, 'milestone@milestone');

print '<br>';

$head = milestoneadmin_prepare_head();

dol_fiche_head($head, 'options', $langs->trans("ModuleSetup"));

$var=true;
print '<table class="noborder" width="100%">';
print '<tr class="liste_titre">';
print '<td>'.$langs->trans("Parameters").'</td>'."\n";
print '<td align="center" width="20">&nbsp;</td>';
print '<td align="center" width="100">'.$langs->trans("Value").'</td>'."\n";
print '</tr>';

/*
 * Formulaire parametres divers
 */

// Hide product details inside milestone
$var=!$var;
print '<tr '.$bc[$var].'>';
print '<td>'.$langs->trans("HideBydefaultProductDetailsInsideMilestone").'</td>';
print '<td align="center" width="20">&nbsp;</td>';

print '<td align="center" width="100">';
if (! empty($conf->use_javascript_ajax))
{
	print ajax_constantonoff('MILESTONE_HIDE_PRODUCT_DETAILS');
}
else
{
	if (empty($conf->global->MILESTONE_HIDE_PRODUCT_DETAILS))
	{
		print '<a href="'.$_SERVER['PHP_SELF'].'?action=set_MILESTONE_HIDE_PRODUCT_DETAILS">'.img_picto($langs->trans("Disabled"),'off').'</a>';
	}
	else
	{
		print '<a href="'.$_SERVER['PHP_SELF'].'?action=del_MILESTONE_HIDE_PRODUCT_DETAILS">'.img_picto($langs->trans("Enabled"),'on').'</a>';
	}
}
print '</td></tr>';

// Hide product description inside milestone
$var=!$var;
print '<tr '.$bc[$var].'>';
print '<td>'.$langs->trans("HideByDefaultProductDescInsideMilestone").'</td>';
print '<td align="center" width="20">&nbsp;</td>';

print '<td align="center" width="100">';
if (! empty($conf->use_javascript_ajax))
{
	print ajax_constantonoff('MILESTONE_HIDE_PRODUCT_DESC');
}
else
{
	if (empty($conf->global->MILESTONE_HIDE_PRODUCT_DESC))
	{
		print '<a href="'.$_SERVER['PHP_SELF'].'?action=set_MILESTONE_HIDE_PRODUCT_DESC">'.img_picto($langs->trans("Disabled"),'off').'</a>';
	}
	else
	{
		print '<a href="'.$_SERVER['PHP_SELF'].'?action=del_MILESTONE_HIDE_PRODUCT_DESC">'.img_picto($langs->trans("Enabled"),'on').'</a>';
	}
}
print '</td></tr>';

// Hide milestone amount
$var=!$var;
print '<tr '.$bc[$var].'>';
print '<td>'.$langs->trans("HideByDefaultMilestoneAmount").'</td>';
print '<td align="center" width="20">&nbsp;</td>';

print '<td align="center" width="100">';
if (! empty($conf->use_javascript_ajax))
{
	print ajax_constantonoff('MILESTONE_HIDE_MILESTONE_AMOUNT');
}
else
{
	if (empty($conf->global->MILESTONE_HIDE_MILESTONE_AMOUNT))
	{
		print '<a href="'.$_SERVER['PHP_SELF'].'?action=set_MILESTONE_HIDE_MILESTONE_AMOUNT">'.img_picto($langs->trans("Disabled"),'off').'</a>';
	}
	else
	{
		print '<a href="'.$_SERVER['PHP_SELF'].'?action=del_MILESTONE_HIDE_MILESTONE_AMOUNT">'.img_picto($langs->trans("Enabled"),'on').'</a>';
	}
}
print '</td></tr>';

print '</table>';


llxFooter();
$db->close();
?>
