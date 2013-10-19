<?php
/* Copyright (C) 2001-2004 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2005 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2013      Florian Henry	  <florian.henry@open-concept.pro>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * $Id: index.php,v 1.12 2011/08/08 15:28:01 eldy Exp $
 */

/**
        \file       htdocs/compta/param/comptes/index.php
        \ingroup    compta
		\brief      Page acceuil zone parametrages
		\version    $Revision: 1.12 $
*/

// Dolibarr environment
$res=@include("../main.inc.php");
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");
if (! $res) die("Include of main fails");

require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';

$langs->load("compta");
$langs->load("bills");
$langs->load('admin');
$langs->load('ventilation@ventilation');

$action=GETPOST('action','alpha');

/*
 * Affichage page
 *
 */
if ($action == 'update' || $action == 'add')
{
	$constname = GETPOST('constname','alpha');
	$constvalue = GETPOST('constvalue','alpha');
	$consttype = GETPOST('consttype','alpha');
	$constnote = GETPOST('constnote','alpha');

	$res = dolibarr_set_const($db, $constname, $constvalue, $consttype, 0, $constnote, $conf->entity);

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

llxHeader("","Accueil Compta");

$form=new Form($db);

$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php">'.$langs->trans("BackToModuleList").'</a>';
print_fiche_titre($langs->trans('ComptaSetup'),$linkback,'setup');

$list=array('COMPTA_ACCOUNT_CUSTOMER','COMPTA_ACCOUNT_SUPPLIER','VENTILATION_ACCOUNT_SUSPENSE' , 'VENTILATION_SELL_JOURNAL','VENTILATION_PURCHASE_JOURNAL', 'VENTILATION_BANK_JOURNAL', 'VENTILATION_SOCIAL_JOURNAL'
);

$num=count($list);
if ($num)
{
	print '<table class="noborder" width="100%">';
	print '<tr class="liste_titre">';
	print '<td colspan="3">'.$langs->trans('OtherOptions').'</td>';
	print "</tr>\n";
}

foreach ($list as $key)
{
	$var=!$var;

	print '<form action="index.php" method="POST">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="update">';
	print '<input type="hidden" name="consttype" value="string">';
	print '<input type="hidden" name="constname" value="'.$key.'">';
	
	print '<tr '.$bc[$var].' class="value">';

	// Param
	$libelle = $langs->trans($key); 
	print '<td>'.$libelle;
	//print ' ('.$key.')';
	print "</td>\n";

	// Value
	print '<td>';
	print '<input type="text" size="20" name="constvalue" value="'.$conf->global->$key.'">';
	print '</td><td>';
	print '<input type="submit" class="button" value="'.$langs->trans('Modify').'" name="button"> &nbsp; ';
	print "</td></tr>\n";
	print '</form>';
	
	$i++;
}

if ($num)
{
	print "</table>\n";
}

dol_htmloutput_mesg($mesg);

$db->close();

llxFooter();
?>