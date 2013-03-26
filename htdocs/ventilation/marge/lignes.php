<?php
/* Copyright (C) 2002-2005 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2005 Simon TOSSER <simon@kornog-computing.com>
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
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
 *
 */

/**
 *    \file       htdocs/compta/ventilation/fournisseur/lignes.php
 *    \ingroup    facture
 *    \brief      Page de detail des lignes de ventilation d'une facture
 */

$res=@include("../../main.inc.php");						// For root directory
if (! $res) $res=@include("../../../main.inc.php");			// For "custom" directory

require_once(DOL_DOCUMENT_ROOT."/core/lib/date.lib.php");

$langs->load("bills");

$year=$_GET["year"];
if ($year == 0 )
{
  $year_current = strftime("%Y",time());
  $year_start = $year_current;
} else {
  $year_current = $year;
  $year_start = $year;
}


if (!$user->rights->facture->lire) accessforbidden();
if (!$user->rights->compta->ventilation->creer) accessforbidden();
/*
 * Securite acces client
 */
if ($user->societe_id > 0) accessforbidden();

llxHeader('');

$textprevyear="<a href=\"lignes.php?year=" . ($year_current-1) . "\">".img_previous()."</a>";
$textnextyear=" <a href=\"lignes.php?year=" . ($year_current+1) . "\">".img_next()."</a>";
print_fiche_titre("Ventilation par affaire $textprevyear ".$langs->trans("Year")." $year_start $textnextyear");

$y = $year_current ;


print '<table class="noborder" width="100%">';
print '<tr class="liste_titre"><td>Mois</td><td align="center">CA HT</td><td align="center">Marge</td>';


$sql = "SELECT month(date), sum(total), sum(marge) FROM llx_view_margeco";
$sql .= " WHERE date >= '".$db->idate(dol_get_first_day($y,1,false))."'";
$sql .= "  AND date <= '".$db->idate(dol_get_last_day($y,12,false))."'";
$sql .= " GROUP BY month(date) ";

$resql = $db->query($sql);
if ($resql)
{
  $i = 0;
  $num = $db->num_rows($resql);

  while ($i < $num)
    {

      $row = $db->fetch_row($resql);

      print '<tr><td>'.$row[0].'</td>';
	print '<td align="center">'.$row[1].'</td>';
	print '<td align="center">'.$row[2].'</td>';
	
	print '</tr>';
      $i++;
    }
  $db->free($resql);
}else {
	print $db->lasterror(); // affiche la derniere erreur sql
}
print "</table>\n";

print '<table class="noborder" width="100%">';
print '<tr class="liste_titre"><td>Annee</td><td align="center">CA HT</td><td align="center">Marge</td><td align="center">Pourcentage</td>';


$sql = "SELECT year(date), sum(total), sum(marge), round(sum(marge)/sum(total),2) FROM llx_view_margeco";
$sql .= " WHERE date >= '".$db->idate(dol_get_first_day($y,1,false))."'";
$sql .= "  AND date <= '".$db->idate(dol_get_last_day($y,12,false))."'";
$sql .= " GROUP BY year(date) ";

$resql = $db->query($sql);
if ($resql)
{
  $i = 0;
  $num = $db->num_rows($resql);

  while ($i < $num)
    {

      $row = $db->fetch_row($resql);

      print '<tr><td>'.$row[0].'</td>';
	print '<td align="center">'.$row[1].'</td>';
	print '<td align="center">'.$row[2].'</td>';
	print '<td align="center">'.$row[3].'</td>';

	
	print '</tr>';
      $i++;
    }
  $db->free($resql);
}else {
	print $db->lasterror(); // affiche la derniere erreur sql
}
print "</table>\n";

$db->close();

llxFooter();
?>
