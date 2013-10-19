<?PHP
/* Copyright (C) 2004-2005 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2005      Laurent Destailleur  <eldy@users.sourceforge.net>
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
 * $Id: liste.php,v 1.12 2011/07/31 22:23:31 eldy Exp $
 */

/**
        \file       htdocs/compta/param/comptes/liste.php
        \ingroup    compta
        \brief      Onglet de gestion de parametrages des ventilations
        \version    $Revision: 1.12 $
*/

// Dolibarr environment
$res=@include("../main.inc.php");
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");
if (! $res) die("Include of main fails");

require_once (DOL_DOCUMENT_ROOT . "/core/lib/date.lib.php");

$year=$_GET["year"];
if ($year == 0 )
{
	$year_current = strftime("%Y",time());
	$year_start = $year_current;
}
else
{
	$year_current = $year;
	$year_start = $year;
}


/*
 * Mode Liste
 *
 *
 *
 */
 
 
 

llxHeader('',$langs->trans("ThirdPartyAccount"));

$textprevyear="<a href=\"liste.php?year=" . ($year_current-1) . "\">".img_previous()."</a>";
$textnextyear=" <a href=\"liste.php?year=" . ($year_current+1) . "\">".img_next()."</a>";

 
 
 
 

$sql = "SELECT so.rowid, so.nom , so.address, so.zip , so.town, so.code_compta , ";
$sql .= " so.fk_forme_juridique , so.fk_pays , so.phone , so.fax ,   fa.datec , fa.fk_soc ";
$sql .= " FROM ".MAIN_DB_PREFIX."facture as fa";
$sql.= " JOIN ".MAIN_DB_PREFIX."societe so ON so.rowid = fa.fk_soc";
//$sql .= " WHERE fa.datec >= '" . $db->idate ( dol_get_first_day ( $y, 1, false ) ) . "'";
//$sql .= "  AND fa.datec <= '" . $db->idate ( dol_get_last_day ( $y, 12, false ) ) . "'";
$sql .= " GROUP BY so.rowid";


$resql = $db->query($sql);
if ($resql)
{
  $num = $db->num_rows($resql);
  $i = 0;

print_fiche_titre($langs->trans("ThirdPartyAccount")." ".$textprevyear." ".$langs->trans("Year")." ".$year_start." ".$textnextyear);
print '<table class="noborder" width="100%">';
print "</table>\n";
print '</td><td valign="top" width="70%" class="notopnoleftnoright"></td>';
print '</tr><tr><td colspan=2>';
print '<table class="noborder" width="100%">';
print '<tr class="liste_titre"><td align="center">'.$langs->trans("AccountNumber").'</td>';
print '<td align="center">'.$langs->trans("Name").'</td>';
print '<td align="center">'.$langs->trans("RaisonSociale").'</td>';
print '<td align="center">'.$langs->trans("Address").'</td>';
print '<td align="center">'.$langs->trans("zipcode").'</td>';
print '<td align="center">'.$langs->trans("city").'</td>';
print '<td align="center">'.$langs->trans("Country").'</td>';
print '<td align="center">'.$langs->trans("Contact").'</td>';
print '<td align="center">'.$langs->trans("tel").'</td>';
print '<td align="center">'.$langs->trans("Fax").'</td></tr>';

  $var=True;

  while ($i < min($num,$conf->liste_limit))
    {
      $obj = $db->fetch_object($resql);
      $var=!$var;

 print "<tr $bc[$var]>";
 print '<td><a href="./fiche.php?action=update&id='.$obj->rowid.'">';
 print img_edit();
 print '</a>&nbsp;'.$obj->code_compta.'</td>'."\n";
 print '<td>'.$obj->nom.'</td>';
 print '<td align="center">'.$obj->fk_forme_juridique.'</td>';
 print '<td align="center">'.$obj->address.'</td>';
 print '<td align="center">'.$obj->zip.'</td>';
 print '<td align="center">'.$obj->town.'</td>';
 print '<td align="center">'.$obj->fk_pays.'</td>';
 print '<td align="center"></td>';
 print '<td align="center">'.$obj->phone.'</td>';
 print '<td align="center">'.$obj->fax.'</td>';
   


      print "</tr>\n";
      $i++;
    }
  print "</table>";
  $db->free($resql);
}
else
{
  dol_print_error($db);
}

$db->close();

llxFooter("<em>Derni&egrave;re modification $Date: 2011/07/31 22:23:31 $ r&eacute;vision $Revision: 1.12 $</em>");
?>
