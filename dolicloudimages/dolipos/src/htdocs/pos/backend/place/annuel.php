<?php
/* Copyright (C) 2011 		Juanjo Menent <jmenent@2byte.es>
 * Copyright (C) 2012 		Ferran Marcet <fmarcet@2byte.es>
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
 */

/**
 *		\file        /pos/backend/place/annuel.php
 *		\ingroup     pos
 *		\brief       Page reporting budget
 *		\version     $Id: annuel.php,v 1.5 2011-08-16 15:36:15 jmenent Exp $
 */

$res=@include("../../../main.inc.php");                                   // For root directory
if (! $res) $res=@include("../../../../main.inc.php");                // For "custom" directory
	
dol_include_once('/pos/backend/lib/place.lib.php');
dol_include_once('/pos/backend/class/ticket.class.php');
dol_include_once('/pos/backend/class/place.class.php');

// Security check

$id=GETPOST('id','int');
$ref=GETPOST('ref','alpha');
$fieldid = 'rowid';
if ($user->societe_id) $socid=$user->societe_id;
//$result=restrictedArea($user,'banque',$id,'bank_account','','',$fieldid);

$year_start=GETPOST('year_start');
$year_current = strftime("%Y",time());
if (! $year_start)
{
	$year_start = $year_current - 2;
	$year_end = $year_current;
}
else
{
	$year_end=$year_start+2;
}

$helpurl='EN:Module_DoliPos|FR:Module_DoliPos_FR|ES:M&oacute;dulo_DoliPos';
llxHeader('','',$helpurl);
if($conf->global->POS_HELP){
	dol_include_once('/pos/backend/class/utils.class.php');
}

$form = new Form($db);

// Get account informations
$acct = new Place($db);

$result=$acct->fetch($id,$ref);



# Ce rapport de tresorerie est base sur llx_bank (car doit inclure les transactions sans facture)
# plutot que sur llx_paiement + llx_paiementfourn

$sql = "SELECT SUM(total_ttc) as total";
$sql.= ", date_format(date_creation,'%Y-%m') as dm";
$sql.= " FROM ".MAIN_DB_PREFIX."pos_ticket";
//$sql.= " WHERE fk_cash = ".$id;
$sql.= " WHERE entity = ".$conf->entity;
$sql.= " AND fk_statut in (1,2,3,4)";
if ($acct->id) $sql .= " AND fk_place IN (".$acct->id.")";
$sql.= " GROUP BY dm";

$sql .= " UNION SELECT sum(fac.total_ttc) as total";
$sql.= ", date_format(datec,'%Y-%m') as dm";
$sql .= " FROM ".MAIN_DB_PREFIX."facture as fac, ".MAIN_DB_PREFIX."pos_facture as pf";
$sql .= " WHERE fac.entity =".$conf->entity;
$sql.= " AND fac.fk_statut in (1,2,3,4)";
//$sql.= " AND (fac.type = 0)";
$sql .= " AND pf.fk_facture = fac.rowid";
if ($acct->id) $sql .= " AND pf.fk_place IN (".$acct->id.")";

$resql=$db->query($sql);
if ($resql)
{
	$num = $db->num_rows($resql);
	$i = 0;
	while ($i < $num)
	{
		$row = $db->fetch_row($resql);
		$encaiss[$row[1]] = $row[0];
		$i++;
	}
}
else
{
	dol_print_error($db);
}

// Onglets
$head=place_prepare_head($acct);
dol_fiche_head($head,'annual',$langs->trans("Place"),0,'placedesk');

$title=$langs->trans("FinancialAccount")." : ";
$lien=($year_start?"<a href='".$_SERVER["PHP_SELF"]."?id=".$acct->id."&year_start=".($year_start-1)."'>".img_previous()."</a> ".$langs->trans("Year")." <a href='".$_SERVER["PHP_SELF"]."?id=".$acct->id."&year_start=".($year_start+1)."'>".img_next()."</a>":"");

print '<table class="border" width="100%">';

// Name
print '<tr><td valign="top">'.$langs->trans("Name").'</td>';
print '<td colspan="3">';
if ($id || $ref)
{
	print $form->showrefnav($acct,'ref','',1,'name','ref');
}
else
{
	print $langs->trans("ALL");
}
print '</td></tr>';

print '</table>';

print '<br>';

// Affiche tableau
print '<table class="notopnoleftnoright" width="100%">';

print '<tr><td colspan="'.(1+($year_end-$year_start+1)*2).'" align="right">'.$lien.'</td></tr>';

print '<tr class="liste_titre"><td class="liste_titre">'.$langs->trans("Month").'</td>';
for ($annee = $year_start ; $annee <= $year_end ; $annee++)
{
	print '<td align="right" width="20%" colspan="2">'.$annee.'</td>';
}
print '</tr>';

$var=true;
for ($mois = 1 ; $mois < 13 ; $mois++)
{
	$var=!$var;
	print '<tr '.$bc[$var].'>';
	print "<td>".dol_print_date(dol_mktime(1,1,1,$mois,1,2000),"%B")."</td>";
	for ($annee = $year_start ; $annee <= $year_end ; $annee++)
	{
		$case = sprintf("%04s-%02s",$annee,$mois);

		print '<td align="right" width="10%">';
		print "</td>";

		print '<td align="right" width="10%">&nbsp;';
		if ($encaiss[$case]>0)
		{
			print price($encaiss[$case]);
			$totentrees[$annee]+=$encaiss[$case];
		}
		print "</td>";
	}
	print '</tr>';
}

// Total debit-credit
print '<tr class="liste_total"><td><b>'.$langs->trans("Total")."</b></td>";
for ($annee = $year_start ; $annee <= $year_end ; $annee++)
{
	print '<td align="right"></td><td align="right"><b>'.price($totentrees[$annee]).'</b></td>';
}
print "</tr>\n";

// Ligne vierge
print '<tr><td>&nbsp;</td>';
$nbcol=0;
for ($annee = $year_start ; $annee <= $year_end ; $annee++)
{
	$nbcol+=2;
}
print "</tr>\n";

// Solde actuel
$balance=0;

$sql = "SELECT SUM(total_ttc) as total";
$sql.= " FROM ".MAIN_DB_PREFIX."pos_ticket";
$sql.= " WHERE entity = ".$conf->entity;
$sql.= " AND fk_statut in (1,2,3,4)";
if ($acct->id) $sql.= " AND fk_place IN (".$acct->id.")";

$sql .= " UNION SELECT sum(fac.total_ttc) as total";
$sql .= " FROM ".MAIN_DB_PREFIX."facture as fac, ".MAIN_DB_PREFIX."pos_facture as pf";
$sql .= " WHERE fac.entity =".$conf->entity;
$sql.= " AND fac.fk_statut in (1,2,3,4)";
//$sql.= " AND (fac.type = 0)";
$sql .= " AND pf.fk_facture = fac.rowid";
if ($acct->id) $sql .= " AND pf.fk_place IN (".$acct->id.")";

$resql=$db->query($sql);
if ($resql)
{
	$obj = $db->fetch_object($resql);
	if ($obj) $balance=$obj->total;
}
else {
	dol_print_error($db);
}
print '<tr class="liste_total"><td><b>'.$langs->trans("CurrentBalance")."</b></td>";
print '<td colspan="'.($nbcol).'" align="right">'.price($balance).'</td>';
print "</tr>\n";

print "</table>";

print "\n</div>\n";

llxFooter();

$db->close();
?>