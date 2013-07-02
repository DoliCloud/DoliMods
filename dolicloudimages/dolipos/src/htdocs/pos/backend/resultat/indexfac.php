<?php
/* Copyright (C) 2001-2004 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2011 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2009 Regis Houssin        <regis@dolibarr.fr>
 * Copyright (C) 2012	   Juanjo Menent		<jmenent@2byte.es>
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
 */

/**
 *	\file        htdocs/pos/resultat/index.php
 *	\brief       Page reporting CA
 */

$res=@include("../../../main.inc.php");                                   // For root directory
if (! $res) $res=@include("../../../../main.inc.php");                // For "custom" directory
require_once(DOL_DOCUMENT_ROOT."/core/lib/report.lib.php");


$year_start=isset($_GET["year_start"])?$_GET["year_start"]:$_POST["year_start"];
$year_current = strftime("%Y",time());
$nbofyear=4;
if (! $year_start) {
    $year_start = $year_current - ($nbofyear-1);
    $year_end = $year_current;
}
else {
    $year_end=$year_start + ($nbofyear-1);
}

$userid=GETPOST('userid');
$socid=GETPOST('socid');
// Security check
if ($user->societe_id > 0) $socid = $user->societe_id;
if (!$user->rights->pos->stats)
accessforbidden();

/*
 * View
 */
$helpurl='EN:Module_DoliPos|FR:Module_DoliPos_FR|ES:M&oacute;dulo_DoliPos';
llxHeader('','',$helpurl);
if($conf->global->POS_HELP){
	dol_include_once('/pos/backend/class/utils.class.php');
}
$html=new Form($db);

$nom=$langs->trans("SalesTurnover");
$period="$year_start - $year_end";
$periodlink=($year_start?"<a href='".$_SERVER["PHP_SELF"]."?year_start=".($year_start-1)."&modecompta=".$modecompta."'>".img_previous()."</a> <a href='".$_SERVER["PHP_SELF"]."?year_start=".($year_start+1)."&modecompta=".$modecompta."'>".img_next()."</a>":"");
$description=$langs->trans("RulesResult");
$builddate=time();

report_header($nom,$nomlink,$period,$periodlink,$description,$builddate,$exportlink);

$sql  = "SELECT date_format(f.date_valid,'%Y-%m') as dm, sum(f.total_ttc) as amount_ttc";
	$sql.= " FROM ".MAIN_DB_PREFIX."facture as f";
	$sql.= ", ".MAIN_DB_PREFIX."pos_facture as pf";
	$sql.= " WHERE f.fk_statut in (1,2,3,4)";
	$sql.= " AND f.rowid = pf.fk_facture";

$sql.= " AND f.entity = ".$conf->entity;
if ($socid) $sql.= " AND f.fk_soc = ".$socid;
$sql.= " GROUP BY dm";
$sql.= " ORDER BY dm";

$result = $db->query($sql);
if ($result)
{
	$num = $db->num_rows($result);
	$i = 0;
	while ($i < $num)
	{
		$obj = $db->fetch_object($result);
		$cum[$obj->dm] = $obj->amount_ttc;
		if ($obj->amount_ttc)
		{
			$minyearmonth=($minyearmonth?min($minyearmonth,$obj->dm):$obj->dm);
			$maxyearmonth=max($maxyearmonth,$obj->dm);
		}
		$i++;
	}
	$db->free($result);
}
else {
	dol_print_error($db);
}

/*
 * Show result array
 */

print '<table width="100%" class="noborder">';
print '<tr class="liste_titre"><td>&nbsp;</td>';

for ($annee = $year_start ; $annee <= $year_end ; $annee++)
{
	print '<td align="center" width="10%" colspan="2">';
	print '<a href="casoc.php?year='.$annee.'">';
	print $annee;
    if ($conf->global->SOCIETE_FISCAL_MONTH_START > 1) print '-'.($annee+1);
	print '</a></td>';
	if ($annee != $year_end) print '<td width="15">&nbsp;</td>';
}
print '</tr>';

print '<tr class="liste_titre"><td>'.$langs->trans("Month").'</td>';
for ($annee = $year_start ; $annee <= $year_end ; $annee++)
{
	print '<td align="right">'.$langs->trans("AmountTTC").'</td>';
	print '<td align="right">'.$langs->trans("Delta").'</td>';
	if ($annee != $year_end) print '<td width="15">&nbsp;</td>';
}
print '</tr>';

$now_show_delta=0;
$minyear=substr($minyearmonth,0,4);
$maxyear=substr($maxyearmonth,0,4);
$nowyear=strftime("%Y",dol_now());
$nowyearmonth=strftime("%Y-%m",dol_now());
$maxyearmonth=max($maxyearmonth,$nowyearmonth);

// Loop on each month
$nb_mois_decalage = $conf->global->SOCIETE_FISCAL_MONTH_START?($conf->global->SOCIETE_FISCAL_MONTH_START-1):0;
for ($mois = 1+$nb_mois_decalage ; $mois <= 12+$nb_mois_decalage ; $mois++)
{
	$mois_modulo = $mois;// ajout
	if($mois>12){$mois_modulo = $mois-12;} // ajout
	$var=!$var;
	print "<tr ".$bc[$var].">";

	print "<td>".dol_print_date(dol_mktime(12,0,0,$mois_modulo,1,2000),"%B")."</td>";
	for ($annee = $year_start ; $annee <= $year_end ; $annee++)
	{
		$annee_decalage=$annee;
		if($mois>12) {$annee_decalage=$annee+1;}
		$casenow = dol_print_date(mktime(),"%Y-%m");
		$case = dol_print_date(dol_mktime(1,1,1,$mois_modulo,1,$annee_decalage),"%Y-%m");
		$caseprev = dol_print_date(dol_mktime(1,1,1,$mois_modulo,1,$annee_decalage-1),"%Y-%m");

		// Valeur CA du mois
		print '<td align="right">';
		if ($cum[$case])
		{
			$now_show_delta=1;  // On a trouve le premier mois de la premiere annee generant du chiffre.
			print '<a href="casocfac.php?year='.$annee_decalage.'&month='.$mois_modulo.'">'.price($cum[$case],1).'</a>';
		}
		else
		{
			if ($minyearmonth < $case && $case <= max($maxyearmonth,$nowyearmonth)) { print '0'; }
			else { print '&nbsp;'; }
		}
		print "</td>";

		// Pourcentage du mois
		if ($annee_decalage > $minyear && $case <= $casenow)
		{
			if ($cum[$caseprev] && $cum[$case])
			{
				$percent=(round(($cum[$case]-$cum[$caseprev])/$cum[$caseprev],4)*100);
				//print "X $cum[$case] - $cum[$caseprev] - $cum[$caseprev] - $percent X";
				print '<td align="right">'.($percent>=0?"+$percent":"$percent").'%</td>';
			}
			if ($cum[$caseprev] && ! $cum[$case])
			{
				print '<td align="right">-100%</td>';
			}
			if (! $cum[$caseprev] && $cum[$case])
			{
				print '<td align="right">+Inf%</td>';
			}
			if (isset($cum[$caseprev]) && ! $cum[$caseprev] && ! $cum[$case])
			{
				print '<td align="right">+0%</td>';
			}
			if (! isset($cum[$caseprev]) && ! $cum[$case])
			{
				print '<td align="right">-</td>';
			}
		}
		else
		{
			print '<td align="right">';
			if ($minyearmonth <= $case && $case <= $maxyearmonth) { print '-'; }
			else { print '&nbsp;'; }
			print '</td>';
		}

		$total[$annee]+=$cum[$case];
		if ($annee_decalage != $year_end) print '<td width="15">&nbsp;</td>';
	}

	print '</tr>';
}

// Affiche total
print '<tr class="liste_total"><td>'.$langs->trans("Total").'</td>';
for ($annee = $year_start ; $annee <= $year_end ; $annee++)
{
	// Montant total
	if ($total[$annee] || ($annee >= $minyear && $annee <= max($nowyear,$maxyear)))
	{
		print '<td align="right" nowrap="nowrap">'.($total[$annee]?price($total[$annee]):"0")."</td>";
	}
	else
	{
		print '<td>&nbsp;</td>';
	}

	// Pourcentage total
	if ($annee > $minyear && $annee <= max($nowyear,$maxyear))
	{
		if ($total[$annee-1] && $total[$annee]) {
			$percent=(round(($total[$annee]-$total[$annee-1])/$total[$annee-1],4)*100);
			print '<td align="right" nowrap="nowrap">'.($percent>=0?"+$percent":"$percent").'%</td>';
		}
		if ($total[$annee-1] && ! $total[$annee])
		{
			print '<td align="right">-100%</td>';
		}
		if (! $total[$annee-1] && $total[$annee])
		{
			print '<td align="right">+Inf%</td>';
		}
		if (! $total[$annee-1] && ! $total[$annee])
		{
			print '<td align="right">+0%</td>';
		}
	}
	else
	{
		print '<td align="right">';
		if ($total[$annee] || ($minyear <= $annee && $annee <= max($nowyear,$maxyear))) { print '-'; }
		else { print '&nbsp;'; }
		print '</td>';
	}

	if ($annee != $year_end) print '<td width="15">&nbsp;</td>';
}
print "</tr>\n";
print "</table>";

llxFooter();

$db->close();
?>