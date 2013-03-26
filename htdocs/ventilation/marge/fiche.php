<?PHP
/* Copyright (C) 2004 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2005 Simon TOSSER <simon@kornog-computing.com>
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
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
 *
 */

/**
 *      \file       htdocs/compta/ventilation/fournisseur/fiche.php
 *      \ingroup    compta
 *      \brief      Page fiche ventilation
 */

$res=@include("../../main.inc.php");						// For root directory
if (! $res) $res=@include("../../../main.inc.php");			// For "custom" directory

require_once(DOL_DOCUMENT_ROOT."/compta/facture/class/facture.class.php");

$langs->load("bills");

llxHeader("","","Fiche Marge Commerciale");

if (array_key_exists("id", $_GET)) {
	$currentId = $_GET["id"];
} else {
	print "pas d'id";
}





print '<table class="noborder" width="100%"><tr><td>';
print '<tr class="liste_titre"><td>Facture</td><td align="center">Ref Produit</td><td align="center">Description</td><td align="center">PU HT</td><td align="center">Quantite</td><td align="center">Total HT</td><td align="center">Ventilation</td>';


$sql = "SELECT f.facnumber as facnumber, l.fk_product as product, l.description as description, l.price as price, l.qty as qty, l.total_ht as total, l.fk_code_ventilation as ventilation ";
$sql .= " FROM ".MAIN_DB_PREFIX."facture as f";
$sql .= " INNER JOIN ".MAIN_DB_PREFIX."facturedet as l ON f.rowid = l.fk_facture ";
$sql .= " WHERE f.rowid = ".$currentId;
$sql .= " UNION SELECT ff.facnumber as facnumber, fd.fk_product as product, fd.description as description, fd.pu_ht as price, va.qty as qty, fd.pu_ht*va.qty*-1 as total, fd.fk_code_ventilation as ventilation ";
$sql .= " FROM ".MAIN_DB_PREFIX."facture_fourn as ff";
$sql .= " INNER JOIN ".MAIN_DB_PREFIX."facture_fourn_det as fd ON ff.rowid = fd.fk_facture_fourn ";
$sql .= " INNER JOIN ".MAIN_DB_PREFIX."ventilation_achat as va ON fd.rowid = va.fk_facture_fourn_det ";
$sql .= " WHERE va.fk_facture = ".$currentId ;
$sql .= " ORDER BY product";

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
	print '<td align="center">'.$row[4].'</td>';
	print '<td align="center">'.$row[5].'</td>';
	print '<td align="center">'.$row[6].'</td>';
	print '<td align="right"><a href="fiche.php?id='.$row[0].'">';
	print '</tr>';
      $i++;
    }
  $db->free($resql);
}else {
	print $db->lasterror(); // affiche la derniere erreur sql
}


print '</td></tr></table>';

llxFooter();
?>
