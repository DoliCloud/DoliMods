<?php
/* Copyright (C) 2001-2004 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004      Laurent Destailleur  <eldy@users.sourceforge.net>
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
 *      \file       htdocs/compta/ventilation/fournisseur/index.php
 *      \ingroup    compta
 *      \brief      Page accueil ventilation
 *
 */

$res=@include("../../main.inc.php");						// For root directory
if (! $res) $res=@include("../../../main.inc.php");			// For "custom" directory

require_once(DOL_DOCUMENT_ROOT."/core/lib/date.lib.php");

$langs->load("suppliers");
$langs->load("compta");
$langs->load("bills");
$langs->load("other");

$year=$_GET["year"];
if ($year == 0 )
{
  $year_current = strftime("%Y",time());
  $year_start = $year_current;
} else {
  $year_current = $year;
  $year_start = $year;
}

$page = $_GET["page"];
if ($page < 0) $page = 0;
$limit = $conf->liste_limit;
$offset = $limit * $page ;

/*
 * View
 */


llxHeader('','Compta - Ventilation');

$textprevyear="<a href=\"index.php?year=" . ($year_current-1) . "\">".img_previous()."</a>";
$textnextyear=" <a href=\"index.php?year=" . ($year_current+1) . "\">".img_next()."</a>";

print_fiche_titre("Ventilation par affaire $textprevyear ".$langs->trans("Year")." $year_start $textnextyear");
$y = $year_current ;


$sql = "SELECT * FROM llx_view_margeco";
$sql .= " WHERE date >= '".$db->idate(dol_get_first_day($y,1,false))."'";
$sql .= "  AND date <= '".$db->idate(dol_get_last_day($y,12,false))."'";
$sql .= " ORDER BY llx_view_margeco.facid ".$db->plimit($limit+1,$offset);


$resql = $db->query($sql);

$num_lignes = $db->num_rows($resql);




print_barre_liste("Facture client",$page,"index.php","",$sortfield,$sortorder,'',$num_lignes);


print '<table class="noborder" width="100%"><tr><td>';





$var=true;




print '<table class="noborder" width="100%">';
print '<tr class="liste_titre"><td>Facture Client</td><td align="center">Date</td><td align="center">Reference facture</td><td align="center">Total HT</td><td align="center">Depense et achat</td><td align="center">Marge Commerciale</td>';

/*
$sql = "SELECT va.fk_facture as facid, f.datef as date, f.facnumber as facnumber, ROUND(f.total,2)as total,";
$sql .= " ROUND(Sum(va.qty*fd.pu_ht),2) AS Depense,";
$sql .= " ROUND(f.total,2)- ROUND(Sum(va.qty*fd.pu_ht),2)as marge";
$sql .= " FROM (llx_ventilation_achat as va";
$sql .= " INNER JOIN llx_facture_fourn_det as fd";
$sql .= " ON va.fk_facture_fourn_det = fd.rowid)";
$sql .= " INNER JOIN llx_facture as f";
$sql .= " ON va.fk_facture = f.rowid";
$sql .= " WHERE f.datef >= '".$db->idate(dol_get_first_day($y,1,false))."'";
$sql .= "  AND f.datef <= '".$db->idate(dol_get_last_day($y,12,false))."'";
$sql .= " GROUP BY va.fk_facture, f.datef, f.facnumber, f.total";
$sql .= " UNION SELECT f.rowid as facid, f.datef as date, f.facnumber as facnumber, ROUND(f.total,2)as total,";
$sql .= " '0' AS Depense,";
$sql .= " ROUND(f.total,2)as marge";
$sql .= " FROM llx_facture as f ";
$sql .= " LEFT JOIN llx_ventilation_achat as va";
$sql .= " ON va.fk_facture = f.rowid ";
$sql .= " WHERE f.datef >= '".$db->idate(dol_get_first_day($y,1,false))."'";
$sql .= "  AND f.datef <= '".$db->idate(dol_get_last_day($y,12,false))."'";
$sql .= " AND va.fk_facture is null ";
$sql .= " ORDER by date , facid ";
*/





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
	print img_edit();
	print '</a></td>';
	print '</tr>';
      $i++;
    }
  $db->free($resql);
}else {
	print $db->lasterror(); // affiche la derniere erreur sql
}


print '</td></tr></table>';

$db->close();

llxFooter();

?>
