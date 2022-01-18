<?php
/* Copyright (C) 2004      Rodolphe Quiedeville 	<rodolphe@quiedeville.org>
 * Copyright (C) 2005-2013 Laurent Destailleur  	<eldy@users.sourceforge.org>
 * Copyright (C) 2014      Mário Batista            <mariorbatista@gmail.com> ISCTE-UL Moss
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
 *	    \file       htdocs/saftpt/admin/saftpt.php
 *		\ingroup    saftpt
 *		\brief      Page to setup saftpt module
 */

require '../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';
require_once DOL_DOCUMENT_ROOT.'/saftpt/class/html.formsaftpt.class.php';

$langs->load("admin");
$langs->load("saftpt@saftpt");

if (!$user->admin) accessforbidden();

$action = GETPOST('action','alpha');

/*
 * Actions reason for tax exemption
 */

if ($action == 'setvalue')
{
	$db->begin();

	//set constant in table llx_const
	$taxexemption = GETPOST('taxexemption_code','alpha');
	
	$res=dolibarr_set_const($db, "TAX_EXEMPTION_REASON",$taxexemption,'chaine',0,'',$conf->entity);
	if (! $res > 0) $error++;
	
    if (! $error)
    {
    	$db->commit();
    	setEventMessage($langs->trans("SetupSaved"));
    }
    else
    {
    	$db->rollback();
    	setEventMessage($langs->trans("Error"),'errors');
    }
}


/*
 *	View
 */

 $formtaxexemption = new FormSaftPt($db);
 
llxHeader('',$langs->trans("SaftSetup"));

$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php">'.$langs->trans("BackToModuleList").'</a>';
print_fiche_titre($langs->trans("SaftSetup"),$linkback,'setup');

$h = 0;

$head[$h][0] = DOL_URL_ROOT."/admin/saftpt.php";
$head[$h][1] = $langs->trans("Miscellaneous");
$head[$h][2] = 'general';
$hselected=$h;
$h++;

dol_fiche_head($head, $hselected, $langs->trans("ModuleSetup"));

print '<br>';
print '<form method="post" action="'.$_SERVER["PHP_SELF"].'">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="setvalue">';

$var=true;

print '<table class="noborder" width="100%">';
print '<tr class="liste_titre">';
print '<td>'.$langs->trans("Parameter").'</td>';
print '<td>'.$langs->trans("Value").'</td>';
print "</tr>\n";

$var=!$var;

print '<tr '.$bc[$var].'><td>';
print $langs->trans("TaxExemptionDef").'</td><td>';
//shows the combo list with the VAT exemption code
print $formtaxexemption->select_taxexemption($conf->global->TAX_EXEMPTION_REASON,'taxexemption_code');

if (empty($conf->global->TAX_EXEMPTION_REASON)) print ' '.img_warning($langs->trans("TaxExemptionEmpty"));
print '</td></tr>';
		
print '</table>';

print '<br>';
print '<div align="center"><input type="submit" class="button" value="'.$langs->trans("Modify").'"></div>';

print '</form>';

llxFooter();

$db->close();



?>
