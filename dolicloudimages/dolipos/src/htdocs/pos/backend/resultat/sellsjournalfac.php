<?php
/* Copyright (C) 2007-2010 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2007-2010 Jean Heimburger  	<jean@tiaris.info>
 * Copyright (C) 2011-2012 Juanjo Menent    	<jmenent@2byte.es>
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

$res=@include("../../../main.inc.php");                                   // For root directory
if (! $res) $res=@include("../../../../main.inc.php");                // For "custom" directory
require_once(DOL_DOCUMENT_ROOT."/core/lib/report.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/date.lib.php");
require_once DOL_DOCUMENT_ROOT.'/societe/class/client.class.php';
dol_include_once('/pos/backend/class/ticket.class.php');
require_once(DOL_DOCUMENT_ROOT."/compta/facture/class/facture.class.php");


$langs->load("companies");
$langs->load("other");
$langs->load("compta");

// Protection if external user
if ($user->societe_id > 0)
{
	accessforbidden();
}

/***************************************************
* PAGE
*
* Put here all code to build page
****************************************************/
$helpurl='EN:Module_DoliPos|FR:Module_DoliPos_FR|ES:M&oacute;dulo_DoliPos';
llxHeader('','',$helpurl);
if($conf->global->POS_HELP){
	dol_include_once('/pos/backend/class/utils.class.php');
}

$html=new Form($db);

$year_current = strftime("%Y",dol_now());
$pastmonth = strftime("%m",dol_now()) - 1;
$pastmonthyear = $year_current;
if ($pastmonth == 0)
{
	$pastmonth = 12;
	$pastmonthyear--;
}

$date_start=dol_mktime(0,0,0,$_REQUEST["date_startmonth"],$_REQUEST["date_startday"],$_REQUEST["date_startyear"]);
$date_end=dol_mktime(23,59,59,$_REQUEST["date_endmonth"],$_REQUEST["date_endday"],$_REQUEST["date_endyear"]);

if (empty($date_start) || empty($date_end)) // We define date_start and date_end
{
	$date_start=dol_get_first_day($pastmonthyear,$pastmonth,false); $date_end=dol_get_last_day($pastmonthyear,$pastmonth,false);
}

$nom=$langs->trans("TicketSellsJournal");
//$nomlink=;
$builddate=time();
$description=$langs->trans("TicketDescSellsJournal");
$period=$html->select_date($date_start,'date_start',0,0,0,'',1,0,1).' - '.$html->select_date($date_end,'date_end',0,0,0,'',1,0,1);
report_header($nom,$nomlink,$period,$periodlink,$description,$builddate,$exportlink);

$p = explode(":", $conf->global->MAIN_INFO_SOCIETE_PAYS);
$idpays = $p[0];

$sql = "SELECT fac.rowid as id, fac.facnumber, fac.type, fac.date_valid as datec, facd.product_type, facd.total_ht, facd.total_tva, facd.tva_tx, facd.total_ttc,facd.total_localtax1,facd.total_localtax2,";
$sql .= " p.accountancy_code_sell, s.code_compta, ";
$sql.= " s.rowid as socid, s.nom as name, s.code_compta, s.client,";

if (version_compare(DOL_VERSION, 3.3) >= 0)
{
	$sql .= " ct.accountancy_code_sell";
}
else
{
	$sql .= " ct.accountancy_code";
}
$sql .= " FROM ".MAIN_DB_PREFIX."pos_facture as pf, ".MAIN_DB_PREFIX."facturedet facd ";
$sql .= " LEFT JOIN ".MAIN_DB_PREFIX."product p ON p.rowid = facd.fk_product ";
$sql .= " JOIN ".MAIN_DB_PREFIX."facture fac ON fac.rowid = facd.fk_facture ";
$sql .= " JOIN ".MAIN_DB_PREFIX."societe s ON s.rowid = fac.fk_soc";
$sql .= " LEFT JOIN ".MAIN_DB_PREFIX."c_tva ct ON facd.tva_tx = ct.taux AND ct.fk_pays = '".$idpays."'";
$sql .= " WHERE fac.fk_statut > 0 AND pf.fk_facture = fac.rowid AND fac.entity IN (0,".$conf->entity.")";
if ($date_start && $date_end) $sql .= " AND fac.date_valid >= '".$db->idate($date_start)."' AND fac.date_valid <= '".$db->idate($date_end)."'";
$sql .= " group by id";
$sql .= " order by datec,id";

$result = $db->query($sql);
if ($result)
{
	$tabfac = array();
	$tabht = array();
	$tabtva = array();
	$tabttc = array();

	$num = $db->num_rows($result);
   	$i=0;
   	$resligne=array();
   	while ($i < $num)
   	{
   	    $obj = $db->fetch_object($result);
   	    // les variables
   	    $cptcli = (! empty($conf->global->COMPTA_ACCOUNT_CUSTOMER))?$conf->global->COMPTA_ACCOUNT_CUSTOMER:$langs->trans("CodeNotDef");
   	    $compta_soc = (! empty($obj->code_compta)?$obj->code_compta:$cptcli);
		$compta_prod = $obj->accountancy_code_sell;
		if (empty($compta_prod))
		{
			if($obj->product_type == 0) $compta_prod = (! empty($conf->global->COMPTA_PRODUCT_SOLD_ACCOUNT))?$conf->global->COMPTA_PRODUCT_SOLD_ACCOUNT:$langs->trans("CodeNotDef") ;
			else $compta_prod = (! empty($conf->global->COMPTA_SERVICE_SOLD_ACCOUNT))?$conf->global->COMPTA_SERVICE_SOLD_ACCOUNT:$langs->trans("CodeNotDef") ;
		}
		$cpttva = (! empty($conf->global->COMPTA_VAT_ACCOUNT))?$conf->global->COMPTA_VAT_ACCOUNT:$langs->trans("CodeNotDef");
		$compta_tva = (! empty($obj->accountancy_code))?$obj->accountancy_code:$cpttva;
		$compta_ltx1 = $langs->trans("CodeNotDef");
		$compta_ltx2 = $langs->trans("CodeNotDef");

    	//la ligne facture
   		$tabfac[$obj->id]["date"] = $obj->date_closed;
   		$tabfac[$obj->id]["ref"] = $obj->ticketnumber;
   		$tabfac[$obj->id]["type"] = $obj->type;
   		
   		$tabttc[$obj->id][$compta_soc] += $obj->total_ttc;
   		$tabht[$obj->id][$compta_prod] += $obj->total_ht;
   		$tabtva[$obj->id][$compta_tva] += $obj->total_tva;
   		$tabltx1[$obj->id][$compta_ltx1] += $obj->total_localtax1;
   		$tabltx2[$obj->id][$compta_ltx2] += $obj->total_localtax2;

		$tabcompany[$obj->id]=array('id'=>$obj->socid, 'name'=>$obj->name, 'client'=>$obj->client);
   		$i++;
   	}
}
else 
{
    dol_print_error($db);
}

/*
 * Show result array
 */

$i = 0;
print "<table class=\"noborder\" width=\"100%\">";
print "<tr class=\"liste_titre\">";
//print "<td>".$langs->trans("JournalNum")."</td>";
print "<td>".$langs->trans("Date")."</td><td>".$langs->trans("Piece").' ('.$langs->trans("TicketRef").")</td>";
print "<td>".$langs->trans("Account")."</td>";
print "<t><td>".$langs->trans("Type")."</td><td align='right'>".$langs->trans("Debit")."</td><td align='right'>".$langs->trans("Credit")."</td>";
print "</tr>\n";

$var=true;
$r='';

$facturestatic=new Facture($db);
$companystatic=new Client($db);
$totalclient=0;
$totalvat=0;
$totalltx1=0;
$totalltx2=0;
$totalp=0;
$totaltickets=0;
foreach ($tabfac as $key => $val)
{
	$facturestatic->fetch($key);
	/*$facturestatic->id=$val["id"];
	$facturestatic->ref=$val["ref"];
	$facturestatic->type=$val["type"];*/
	

	print "<tr ".$bc[$var].">";
	// third party
	print "<td>".dol_print_date($facturestatic->date_validation,"day")."</td>";
	print "<td>".$facturestatic->getNomUrl(1)."</td>";

	foreach ($tabttc[$key] as $k => $mt)
	{
		$companystatic->id=$tabcompany[$key]['id'];
		$companystatic->name=$tabcompany[$key]['name'];
		$companystatic->client=$tabcompany[$key]['client'];
		print "<td>".$k;
		print "</td><td>".$langs->trans("ThirdParty");
		print ' ('.$companystatic->getNomUrl(0,'customer',16).')';
		print "</td><td align='right'>".($mt>=0?price($mt):'')."</td><td align='right'>".($mt<0?price(-$mt):'')."</td>";
		$totalclient+=$mt;
	}
	print "</tr>";
	
	// product
	foreach ($tabht[$key] as $k => $mt)
	{
		if ($mt)
		{
			print "<tr ".$bc[$var].">";
			print "<td>".dol_print_date($facturestatic->date_validation,"day")."</td>";
			print "<td>".$facturestatic->getNomUrl(1)."</td>";
			//print "<td>".$facturestatic->getNomUrl(1)."</td>";
			print "<td>".$k."</td><td>".$langs->trans("Products")."</td><td align='right'>".($mt<0?price(-$mt):'')."</td><td align='right'>".($mt>=0?price($mt):'')."</td></tr>";
			$totalp+=$mt;
		}
	}

	foreach ($tabtva[$key] as $k => $mt)
	{
	    if ($mt)
	    {
    		print "<tr ".$bc[$var].">";
    		print "<td>".dol_print_date($facturestatic->date_validation,"day")."</td>";
			print "<td>".$facturestatic->getNomUrl(1)."</td>";
    		//print "<td>".$invoicestatic->getNomUrl(1)."</td>";
    		print "<td>".$k."</td><td>".$langs->trans("VAT")."</td><td align='right'>".($mt<0?price(-$mt):'')."</td><td align='right'>".($mt>=0?price($mt):'')."</td></tr>";
    		$totalvat+=$mt;
	    }
	}
	
	foreach ($tabltx1[$key] as $k => $mt)
	{
		if ($mt)
		{
			print "<tr ".$bc[$var].">";
			print "<td>".dol_print_date($facturestatic->date_validation,"day")."</td>";
			print "<td>".$facturestatic->getNomUrl(1)."</td>";
			print "<td>".$k."</td><td>".$langs->trans("TotalLT1ES")."</td><td align='right'>".($mt<0?price(-$mt):'')."</td><td align='right'>".($mt>=0?price($mt):'')."</td></tr>";
			$totalltx1+=$mt;
		}
	}
	
	foreach ($tabltx2[$key] as $k => $mt)
	{
		if ($mt)
		{
			print "<tr ".$bc[$var].">";
			print "<td>".dol_print_date($facturestatic->date_validation,"day")."</td>";
			print "<td>".$facturestatic->getNomUrl(1)."</td>";
			//print "<td>".$invoicestatic->getNomUrl(1)."</td>";
			print "<td>".$k."</td><td>".$langs->trans("TotalLT2ES")."</td><td align='right'>".($mt<0?price(-$mt):'')."</td><td align='right'>".($mt>=0?price($mt):'')."</td></tr>";
			$totalltx2-=$mt;
		}
	}

	$var = !$var;
	$totaltickets+=1;
}

// Print total
print '<tr class="liste_total">';
print '<td class="liste_total" align="left">'.$langs->trans('Total').'</td>';
print '<td class="liste_total" align="left">'.$langs->trans('Tickets').': '.$totaltickets.'</td>';
print "<td>&nbsp;</td>";
print "<td>&nbsp;</td>";
print '<td class="liste_total" align="right">'.price($totalclient+$totalltx2).'</td>';
print '<td class="liste_total" align="right">'.price($totalp+$totalvat+$totalltx1).'</td>';

print '</tr>';
print "</table>";

// End of page
llxFooter();

$db->close();
?>