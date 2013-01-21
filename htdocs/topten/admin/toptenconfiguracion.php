<?php
/* Copyright (C) 2003      Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2003      Eric Seigne          <erics@rycks.com>
 * Copyright (C) 2004-2010 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2004      Sebastien Di Cintio  <sdicintio@ressource-toi.org>
 * Copyright (C) 2004      Benoit Mortier       <benoit.mortier@opensides.be>
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
 *    \file       htdocs/externalsite/admin/externalsite.php
 *    \ingroup    externalsite
 *    \brief      Page de configuration du module externalsite
 */

require("../../main.inc.php");
require_once(DOL_DOCUMENT_ROOT."/lib/admin.lib.php");


if (!$user->admin)
    accessforbidden();


$langs->load("admin");
$langs->load("other");
$langs->load("calprodlang@calprod");

$def = array();

// Sauvegardes parametres
if ($_POST["action"] == 'update')
{
    $i=0;

    $db->begin();

    $i+=dolibarr_set_const($db,'WSDL_URL',trim($_POST["WSDL_URL"]),'chaine',0,'',$conf->entity);

    if ($i >= 1)
    {
        $db->commit();
        $mesg = "<font class=\"ok\">".$langs->trans("SetupSaved")."</font>";
    }
    else
    {
        $db->rollback();
        $mesg=$db->lasterror();
    }
}
if ($_POST["action"] == 'updatepagoconfiguracion')
{
    $i=0;

    $db->begin();

    $i+=dolibarr_set_const($db,'NP_DIAS_ADELANTO',trim($_POST["diasadelanto"]),'chaine',0,'',$conf->entity);
    $i+=dolibarr_set_const($db,'NP_DIAS_ATRASO',trim($_POST["diasatraso"]),'chaine',0,'',$conf->entity);
	$i+=dolibarr_set_const($db,'NP_CORREO_ECONOMICA',trim($_POST["email_economica"]),'chaine',0,'',$conf->entity);

    if ($i >= 1)
    {
        $db->commit();
        $mesg = "<font class=\"ok\">".$langs->trans("SetupSaved")."</font>";
    }
    else
    {
        $db->rollback();
        $mesg=$db->lasterror();
    }

}


/**
 * View
 */

llxHeader();

$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php">'.$langs->trans("BackToModuleList").'</a>';
print_fiche_titre($langs->trans("CALPRODSetup"),$linkback,'setup');
print '<br>';


print '<form name="externalsiteconfig" action="'.$_SERVER["PHP_SELF"].'" method="POST">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="updatepagoconfiguracion">';
print "<table class=\"noborder\" width=\"100%\">";
print "<tr class=\"liste_titre\">";
print "<td width=\"30%\">".$langs->trans("Parameter")."</td>";
print "<td>".$langs->trans("Value")."</td>";
print "<td>".$langs->trans("Examples")."</td>";
print "</tr>";

print "<tr class=\"pair\">";
print "<td>".$langs->trans("DiasAvisoAdelantado")."</td>";
print "<td><input type=\"text\" class=\"flat\" name=\"diasadelanto\" value=\"". ($_POST["diasadelanto"]?$_POST["diasadelanto"]:$conf->global->NP_DIAS_ADELANTO) . "\" size=\"40\"></td>";
print "<td>2";
print "</td>";
print "</tr>";

print "<tr class=\"impair\">";
print "<td>".$langs->trans("DiasAvisoAtraso")."</td>";
print "<td><input type=\"text\" class=\"flat\" name=\"diasatraso\" value=\"". ($_POST["diasatraso"]?$_POST["diasatraso"]:$conf->global->NP_DIAS_ATRASO) . "\" size=\"40\"></td>";
print "<td>3";
print "</td>";
print "</tr>";

print "<tr class=\"pair\">";
print "<td>".$langs->trans("EmailEconomica")."</td>";
print "<td><input type=\"text\" class=\"flat\" name=\"email_economica\" value=\"". ($_POST["email_economica"]?$_POST["email_economica"]:$conf->global->NP_CORREO_ECONOMICA) . "\" size=\"40\"></td>";
print "<td>maria@enteratek.com";
print "</td>";
print "</tr>";
print "</table>";


print '<br><center>';
print "<input type=\"submit\" name=\"save\" class=\"button\" value=\"".$langs->trans("Save")."\">";
print "</center>";

print "</form>\n";
print '<br/>';


if ($mesg) print "<br>$mesg<br>";

$db->close();

llxFooter('$Date: 2010/04/20 14:27:47 $ - $Revision: 1.2 $');
?>
