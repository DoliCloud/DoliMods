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
 */

$res=@include("../../main.inc.php");						// For root directory
if (! $res) $res=@include("../../../main.inc.php");			// For "custom" directory

require_once(DOL_DOCUMENT_ROOT."/core/lib/date.lib.php");

$langs->load("suppliers");
$langs->load("compta");
$langs->load("bills");
$langs->load("other");
$langs->load("ventilation@ventilation");

$year=$_GET["year"];
if ($year == 0 )
{
  $year_current = strftime("%Y",time());
  $year_start = $year_current;
} else {
  $year_current = $year;
  $year_start = $year;
}

/*
 * View
 */


llxHeader('',$langs->trans("SuppliersVentilation"));

$textprevyear="<a href=\"index.php?year=" . ($year_current-1) . "\">".img_previous()."</a>";
$textnextyear=" <a href=\"index.php?year=" . ($year_current+1) . "\">".img_next()."</a>";


print_fiche_titre("Ventilation Fournisseur $textprevyear ".$langs->trans("Year")." $year_start $textnextyear");



print '<table border="0" width="100%" class="notopnoleftnoright">';

print '<tr><td valign="top" width="30%" class="notopnoleft">';



$sql = "SELECT count(*) FROM ".MAIN_DB_PREFIX."facture_fourn_det";
$sql .= " WHERE fk_code_ventilation = 0";
$result = $db->query($sql);
if ($result)
{
  $row = $db->fetch_row($result);
  $nbfacfourn = $row[0];

  $db->free($result);
}

/*$sql = "SELECT count(*) FROM ".MAIN_DB_PREFIX."facture_fourn";
$sql .= " WHERE fk_export_compta = 0";

$result = $db->query($sql);
if ($result)
{
  $row = $db->fetch_row($result);
  $nbpfourn = $row[0];

  $db->free($result);
}*/

print '<table class="noborder" width="100%">';
print '<tr class="liste_titre"><td colspan="2">Lignes a ventiler</tr>';
print '<tr class="liste_titre"><td>Type</td><td align="center">Nb</td></tr>';
print '<tr><td>Factures fournisseurs</td><td align="center">'.$nbfacfourn.'</td></tr>';
print '<tr><td>Paiements fournisseurs</td><td align="center">'.$nbpfourn.'</td></tr>';
print "</table>\n";

$y = $year_current ;


$var=true;


print '</td><td valign="top" width="70%" class="notopnoleftnoright"></td>';

print '</tr><tr><td colspan=2>';

print '<table class="noborder" width="100%">';
print '<tr class="liste_titre"><td width=150>Intitule</td><td align="center">Janvier</td><td align="center">Fevrier</td><td align="center">Mars</td><td align="center">Avril</td><td align="center">Mai</td><td align="center">Juin</td><td align="center">Juillet</td><td align="center">Aout</td><td align="center">Septembre</td><td align="center">Octobre</td><td align="center">Novembre</td><td align="center">Decembre</td><td align="center"><b>Total</b></td></tr>';

$sql = "SELECT IF(ccg.intitule IS NULL, 'Non pointe', ccg.intitule) AS 'Intitulé',";
$sql .= "  ROUND(SUM(IF(MONTH(ff.datef)=1,ffd.total_ht,0)),2) AS 'Janvier',";
$sql .= "  ROUND(SUM(IF(MONTH(ff.datef)=2,ffd.total_ht,0)),2) AS 'Fevrier',";
$sql .= "  ROUND(SUM(IF(MONTH(ff.datef)=3,ffd.total_ht,0)),2) AS 'Mars',";
$sql .= "  ROUND(SUM(IF(MONTH(ff.datef)=4,ffd.total_ht,0)),2) AS 'Avril',";
$sql .= "  ROUND(SUM(IF(MONTH(ff.datef)=5,ffd.total_ht,0)),2) AS 'Mai',";
$sql .= "  ROUND(SUM(IF(MONTH(ff.datef)=6,ffd.total_ht,0)),2) AS 'Juin',";
$sql .= "  ROUND(SUM(IF(MONTH(ff.datef)=7,ffd.total_ht,0)),2) AS 'Juillet',";
$sql .= "  ROUND(SUM(IF(MONTH(ff.datef)=8,ffd.total_ht,0)),2) AS 'Aout',";
$sql .= "  ROUND(SUM(IF(MONTH(ff.datef)=9,ffd.total_ht,0)),2) AS 'Septembre',";
$sql .= "  ROUND(SUM(IF(MONTH(ff.datef)=10,ffd.total_ht,0)),2) AS 'Octobre',";
$sql .= "  ROUND(SUM(IF(MONTH(ff.datef)=11,ffd.total_ht,0)),2) AS 'Novembre',";
$sql .= "  ROUND(SUM(IF(MONTH(ff.datef)=12,ffd.total_ht,0)),2) AS 'Decembre',";
$sql .= "  ROUND(SUM(ffd.total_ht),2) as 'Total'";
$sql .= " FROM llx_facture_fourn_det as ffd";
$sql .= "  LEFT JOIN llx_facture_fourn as ff ON ff.rowid = ffd.fk_facture_fourn";
$sql .= "  LEFT JOIN llx_compta_compte_generaux as ccg ON ccg.rowid = ffd.fk_code_ventilation";
$sql .= " WHERE ff.datef >= '".$db->idate(dol_get_first_day($y,1,false))."'";
$sql .= "  AND ff.datef <= '".$db->idate(dol_get_last_day($y,12,false))."'";
$sql .= " GROUP BY ffd.fk_code_ventilation";

$resql = $db->query($sql);
if ($resql)
{
  $i = 0;
  $num = $db->num_rows($resql);

  while ($i < $num)
    {

      $row = $db->fetch_row($resql);

      print '<tr><td>'.$row[0].'</td>';
	print '<td align="right">'.$row[1].'</td>';
	print '<td align="right">'.$row[2].'</td>';
	print '<td align="right">'.$row[3].'</td>';
	print '<td align="right">'.$row[4].'</td>';
	print '<td align="right">'.$row[5].'</td>';
	print '<td align="right">'.$row[6].'</td>';
	print '<td align="right">'.$row[7].'</td>';
	print '<td align="right">'.$row[8].'</td>';
	print '<td align="right">'.$row[9].'</td>';
	print '<td align="right">'.$row[10].'</td>';
	print '<td align="right">'.$row[11].'</td>';
	print '<td align="right">'.$row[12].'</td>';
	print '<td align="right"><b>'.$row[13].'</b></td>';
	print '</tr>';
      $i++;
    }
  $db->free($resql);
}else {
	print $db->lasterror(); // affiche la derniere erreur sql
}
print "</table>\n";

print '<table class="noborder" width="100%">';
print "</table>\n";

print '<table class="noborder" width="100%">';
print "</table>\n";


print '</td><td valign="top" width="70%" class="notopnoleftnoright">';


print '</td><td valign="top" width="70%" class="notopnoleftnoright"></td>';

print '</tr><tr><td colspan=2>';

print '<table class="noborder" width="100%">';
print '<tr class="liste_titre"><td width=150>Total</td><td align="center">Janvier</td><td align="center">Fevrier</td><td align="center">Mars</td><td align="center">Avril</td><td align="center">Mai</td><td align="center">Juin</td><td align="center">Juillet</td><td align="center">Aout</td><td align="center">Septembre</td><td align="center">Octobre</td><td align="center">Novembre</td><td align="center">Decembre</td><td align="center"><b>Total</b></td></tr>';

$sql = "SELECT 'Total Achat Fournisseur HT' AS 'Total',";
$sql .= "  ROUND(SUM(IF(MONTH(ff.datef)=1,ffd.total_ht,0)),2) AS 'Janvier',";
$sql .= "  ROUND(SUM(IF(MONTH(ff.datef)=2,ffd.total_ht,0)),2) AS 'Fevrier',";
$sql .= "  ROUND(SUM(IF(MONTH(ff.datef)=3,ffd.total_ht,0)),2) AS 'Mars',";
$sql .= "  ROUND(SUM(IF(MONTH(ff.datef)=4,ffd.total_ht,0)),2) AS 'Avril',";
$sql .= "  ROUND(SUM(IF(MONTH(ff.datef)=5,ffd.total_ht,0)),2) AS 'Mai',";
$sql .= "  ROUND(SUM(IF(MONTH(ff.datef)=6,ffd.total_ht,0)),2) AS 'Juin',";
$sql .= "  ROUND(SUM(IF(MONTH(ff.datef)=7,ffd.total_ht,0)),2) AS 'Juillet',";
$sql .= "  ROUND(SUM(IF(MONTH(ff.datef)=8,ffd.total_ht,0)),2) AS 'Aout',";
$sql .= "  ROUND(SUM(IF(MONTH(ff.datef)=9,ffd.total_ht,0)),2) AS 'Septembre',";
$sql .= "  ROUND(SUM(IF(MONTH(ff.datef)=10,ffd.total_ht,0)),2) AS 'Octobre',";
$sql .= "  ROUND(SUM(IF(MONTH(ff.datef)=11,ffd.total_ht,0)),2) AS 'Novembre',";
$sql .= "  ROUND(SUM(IF(MONTH(ff.datef)=12,ffd.total_ht,0)),2) AS 'Decembre',";
$sql .= "  ROUND(SUM(ffd.total_ht),2) as 'Total'";
$sql .= " FROM llx_facture_fourn_det as ffd";
$sql .= "  LEFT JOIN llx_facture_fourn as ff ON ff.rowid = ffd.fk_facture_fourn";
$sql .= " WHERE ff.datef >= '".$db->idate(dol_get_first_day($y,1,false))."'";
$sql .= "  AND ff.datef <= '".$db->idate(dol_get_last_day($y,12,false))."'";


$resql = $db->query($sql);
if ($resql)
{
  $i = 0;
  $num = $db->num_rows($resql);

  while ($i < $num)
    {

      $row = $db->fetch_row($resql);

      print '<tr><td>'.$row[0].'</td>';
	print '<td align="right">'.$row[1].'</td>';
	print '<td align="right">'.$row[2].'</td>';
	print '<td align="right">'.$row[3].'</td>';
	print '<td align="right">'.$row[4].'</td>';
	print '<td align="right">'.$row[5].'</td>';
	print '<td align="right">'.$row[6].'</td>';
	print '<td align="right">'.$row[7].'</td>';
	print '<td align="right">'.$row[8].'</td>';
	print '<td align="right">'.$row[9].'</td>';
	print '<td align="right">'.$row[10].'</td>';
	print '<td align="right">'.$row[11].'</td>';
	print '<td align="right">'.$row[12].'</td>';
	print '<td align="right"><b>'.$row[13].'</b></td>';
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
