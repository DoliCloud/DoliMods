<?php
/* Copyright (C) 2001-2006 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2005 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2013 Olivier Geffroy  <jeff@jeffinfo.com>
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
 *    \file       htdocs/custom/ventilation/index.php
 *    \ingroup    ventilation compta
 *    \brief      Page accueil ventilation
 */
$res=@include("../main.inc.php");
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");
if (! $res) die("Include of main fails");

require_once(DOL_DOCUMENT_ROOT."/core/lib/date.lib.php");

$langs->load("compta");
$langs->load("bills");
$langs->load("other");
$langs->load("main");
$langs->load("ventilation@ventilation");

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
 * View
 */
llxHeader('',$langs->trans("CustomersVentilation"));

$textprevyear="<a href=\"index.php?year=" . ($year_current-1) . "\">".img_previous()."</a>";
$textnextyear=" <a href=\"index.php?year=" . ($year_current+1) . "\">".img_next()."</a>";

print_fiche_titre($langs->trans("VentilationComptable")." ".$textprevyear." ".$langs->trans("Year")." ".$year_start." ".$textnextyear);

print '<table border="0" width="100%" class="notopnoleftnoright">';
print '<tr><td valign="top" width="30%" class="notopnoleft">';

$sql = "SELECT count(*) FROM ".MAIN_DB_PREFIX."facturedet as fd";
$sql.= " , ".MAIN_DB_PREFIX."facture as f";
$sql.= " WHERE fd.fk_code_ventilation = 0";
$sql.= " AND f.rowid = fd.fk_facture AND f.fk_statut = 1;";

$result = $db->query($sql);
if ($result)
{
	$row = $db->fetch_row($result);
	$nbfac = $row[0];
	$db->free($result);
}

$y = $year_current ;

$var=true;

print '<table class="noborder" width="100%">';
print "</table>\n";
print '</td><td valign="top" width="70%" class="notopnoleftnoright"></td>';
print '</tr><tr><td colspan=2>';
print '<table class="noborder" width="100%">';
print '<tr class="liste_titre"><td width=150>'.$langs->trans("Intitule").'</td>';
print '<td align="center">'.$langs->trans("January").'</td>';
print '<td align="center">'.$langs->trans("February").'</td>';
print '<td align="center">'.$langs->trans("March").'</td>';
print '<td align="center">'.$langs->trans("April").'</td>';
print '<td align="center">'.$langs->trans("May").'</td>';
print '<td align="center">'.$langs->trans("June").'</td>';
print '<td align="center">'.$langs->trans("July").'</td>';
print '<td align="center">'.$langs->trans("August").'</td>';
print '<td align="center">'.$langs->trans("September").'</td>';
print '<td align="center">'.$langs->trans("October").'</td>';
print '<td align="center">'.$langs->trans("November").'</td>';
print '<td align="center">'.$langs->trans("December").'</td>';
print '<td align="center"><b>'.$langs->trans("Total").'</b></td></tr>';

$sql = "SELECT IF(ccg.intitule IS NULL, 'Non pointe', ccg.intitule) AS 'Intitulé',";
$sql .= "  ROUND(SUM(IF(MONTH(f.datef)=1,fd.total_ht,0)),2) AS 'Janvier',";
$sql .= "  ROUND(SUM(IF(MONTH(f.datef)=2,fd.total_ht,0)),2) AS 'Fevrier',";
$sql .= "  ROUND(SUM(IF(MONTH(f.datef)=3,fd.total_ht,0)),2) AS 'Mars',";
$sql .= "  ROUND(SUM(IF(MONTH(f.datef)=4,fd.total_ht,0)),2) AS 'Avril',";
$sql .= "  ROUND(SUM(IF(MONTH(f.datef)=5,fd.total_ht,0)),2) AS 'Mai',";
$sql .= "  ROUND(SUM(IF(MONTH(f.datef)=6,fd.total_ht,0)),2) AS 'Juin',";
$sql .= "  ROUND(SUM(IF(MONTH(f.datef)=7,fd.total_ht,0)),2) AS 'Juillet',";
$sql .= "  ROUND(SUM(IF(MONTH(f.datef)=8,fd.total_ht,0)),2) AS 'Aout',";
$sql .= "  ROUND(SUM(IF(MONTH(f.datef)=9,fd.total_ht,0)),2) AS 'Septembre',";
$sql .= "  ROUND(SUM(IF(MONTH(f.datef)=10,fd.total_ht,0)),2) AS 'Octobre',";
$sql .= "  ROUND(SUM(IF(MONTH(f.datef)=11,fd.total_ht,0)),2) AS 'Novembre',";
$sql .= "  ROUND(SUM(IF(MONTH(f.datef)=12,fd.total_ht,0)),2) AS 'Decembre',";
$sql .= "  ROUND(SUM(fd.total_ht),2) as 'Total'";
$sql .= " FROM llx_facturedet as fd";
$sql .= "  LEFT JOIN llx_facture as f ON f.rowid = fd.fk_facture";
$sql .= "  LEFT JOIN llx_compta_compte_generaux as ccg ON ccg.rowid = fd.fk_code_ventilation";
$sql .= " WHERE f.datef >= '".$db->idate(dol_get_first_day($y,1,false))."'";
$sql .= "  AND f.datef <= '".$db->idate(dol_get_last_day($y,12,false))."'";
$sql .= " GROUP BY fd.fk_code_ventilation";

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
}
else
{
	print $db->lasterror(); // affiche la derniere erreur sql
}

print "</table>\n";
print '</td><td valign="top" width="70%" class="notopnoleftnoright">';
print '</td><td valign="top" width="70%" class="notopnoleftnoright"></td>';
print '</tr><tr><td colspan=2>';
print "\n<br>\n";
print '<table class="noborder" width="100%">';
print '<tr class="liste_titre"><td width=150>'.$langs->trans("Total").'</td>';
print '<td align="center">'.$langs->trans("January").'</td>';
print '<td align="center">'.$langs->trans("February").'</td>';
print '<td align="center">'.$langs->trans("March").'</td>';
print '<td align="center">'.$langs->trans("April").'</td>';
print '<td align="center">'.$langs->trans("May").'</td>';
print '<td align="center">'.$langs->trans("June").'</td>';
print '<td align="center">'.$langs->trans("July").'</td>';
print '<td align="center">'.$langs->trans("August").'</td>';
print '<td align="center">'.$langs->trans("September").'</td>';
print '<td align="center">'.$langs->trans("October").'</td>';
print '<td align="center">'.$langs->trans("November").'</td>';
print '<td align="center">'.$langs->trans("December").'</td>';
print '<td align="center"><b>'.$langs->trans("Total").'</b></td></tr>';


$sql = "SELECT '".$langs->trans("CAHT")."' AS 'Total',";
$sql .= "  ROUND(SUM(IF(MONTH(f.datef)=1,fd.total_ht,0)),2) AS 'Janvier',";
$sql .= "  ROUND(SUM(IF(MONTH(f.datef)=2,fd.total_ht,0)),2) AS 'Fevrier',";
$sql .= "  ROUND(SUM(IF(MONTH(f.datef)=3,fd.total_ht,0)),2) AS 'Mars',";
$sql .= "  ROUND(SUM(IF(MONTH(f.datef)=4,fd.total_ht,0)),2) AS 'Avril',";
$sql .= "  ROUND(SUM(IF(MONTH(f.datef)=5,fd.total_ht,0)),2) AS 'Mai',";
$sql .= "  ROUND(SUM(IF(MONTH(f.datef)=6,fd.total_ht,0)),2) AS 'Juin',";
$sql .= "  ROUND(SUM(IF(MONTH(f.datef)=7,fd.total_ht,0)),2) AS 'Juillet',";
$sql .= "  ROUND(SUM(IF(MONTH(f.datef)=8,fd.total_ht,0)),2) AS 'Aout',";
$sql .= "  ROUND(SUM(IF(MONTH(f.datef)=9,fd.total_ht,0)),2) AS 'Septembre',";
$sql .= "  ROUND(SUM(IF(MONTH(f.datef)=10,fd.total_ht,0)),2) AS 'Octobre',";
$sql .= "  ROUND(SUM(IF(MONTH(f.datef)=11,fd.total_ht,0)),2) AS 'Novembre',";
$sql .= "  ROUND(SUM(IF(MONTH(f.datef)=12,fd.total_ht,0)),2) AS 'Decembre',";
$sql .= "  ROUND(SUM(fd.total_ht),2) as 'Total'";
$sql .= " FROM llx_facturedet as fd";
$sql .= "  LEFT JOIN llx_facture as f ON f.rowid = fd.fk_facture";
$sql .= " WHERE f.datef >= '".$db->idate(dol_get_first_day($y,1,false))."'";
$sql .= "  AND f.datef <= '".$db->idate(dol_get_last_day($y,12,false))."'";


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
}
else
{
	print $db->lasterror(); // affiche la derniere erreur sql
}

print "</table>\n";
print '</td></tr></table>';

llxFooter();
$db->close();

?>
