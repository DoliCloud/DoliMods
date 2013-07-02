<?php
/* Copyright (C) 2011-2012 Juanjo Menent           <2byte.es>
 * Copyright (C) 2012 	   Ferran Marcet           <fmarcet@2byte.es>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU  *General Public License as published by
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
 *	\file       htdocs/pos/backend/liste.php
 *	\ingroup    ticket
 *	\brief      Page to list tickets
 */

$res=@include("../../../main.inc.php");                                   // For root directory
if (! $res) $res=@include("../../../../main.inc.php");                // For "custom" directory

dol_include_once('/pos/backend/class/ticket.class.php');
require_once(DOL_DOCUMENT_ROOT."/core/class/html.formother.class.php");
dol_include_once('/pos/backend/class/cash.class.php');
require_once(DOL_DOCUMENT_ROOT."/core/lib/report.lib.php");
require_once(DOL_DOCUMENT_ROOT ."/core/lib/date.lib.php");
dol_include_once('/pos/backend/class/pos.class.php');

$langs->load('pos@pos');
$langs->load('deliveries');
$langs->load('companies');

/*$ticketyear=GETPOST("ticketyear","int");
$ticketmonth=GETPOST("ticketmonth","int");
$deliveryyear=GETPOST("deliveryyear","int");
$deliverymonth=GETPOST("deliverymonth","int");
$socid=GETPOST('socid','int');
$userid=GETPOST('userid','int');
$viewstatut=GETPOST('viewstatut');
$viewtype=GETPOST('viewtype');
$closeid=GETPOST('closeid','int');
$terminalid=GETPOST('terminalid','int');*/

$mesg = $_SESSION['message'];
$_SESSION['message'] = '';

$sortfield = GETPOST("sortfield",'alpha');
$sortorder = GETPOST("sortorder",'alpha');
$page = GETPOST("page",'int');
if ($page == -1) { $page = 0; }
$pageprev = $page - 1;
$pagenext = $page + 1;

/*$month    =GETPOST('month','int');
$year     =GETPOST('year','int');*/

if (! $sortorder) $sortorder='DESC';
if (! $sortfield) $sortfield='f.date_ticket';

/*
 * View
 */
$helpurl='EN:Module_DoliPos|FR:Module_DoliPos_FR|ES:M&oacute;dulo_DoliPos';
llxHeader("",$langs->trans("Tickets"),$helpurl);
if($conf->global->POS_HELP){
	dol_include_once('/pos/backend/class/utils.class.php');
}
dol_htmloutput_mesg($mesg);

$html=new Form($db);

// Date range
$year=GETPOST("year");
$month=GETPOST("month");
if (empty($year))
{
	$year_current = strftime("%Y",dol_now());
	$month_current = strftime("%m",dol_now());
	$year_start = $year_current;
} else {
	$year_current = $year;
	$month_current = strftime("%m",dol_now());
	$year_start = $year;
}
$date_start=dol_mktime(0,0,0,$_REQUEST["date_startmonth"],$_REQUEST["date_startday"],$_REQUEST["date_startyear"]);
$date_end=dol_mktime(23,59,59,$_REQUEST["date_endmonth"],$_REQUEST["date_endday"],$_REQUEST["date_endyear"]);
// Quarter
if (empty($date_start) || empty($date_end)) // We define date_start and date_end
{
	$q=GETPOST("q")?GETPOST("q"):0;
	if ($q==0)
	{
		// We define date_start and date_end
		$month_start=GETPOST("month")?GETPOST("month"):($conf->global->SOCIETE_FISCAL_MONTH_START?($conf->global->SOCIETE_FISCAL_MONTH_START):1);
		$year_end=$year_start;
		$month_end=$month_start;
		if (! GETPOST("month"))	// If month not forced
		{
			if (! GETPOST('year') && $month_start > $month_current)
			{
				$year_start--;
				$year_end--;
			}
			$month_end=$month_start-1;
			if ($month_end < 1) $month_end=12;
			else $year_end++;
		}
		$date_start=dol_get_first_day($year_start,$month_start,false); $date_end=dol_get_last_day($year_end,$month_end,false);
	}
	if ($q==1) { $date_start=dol_get_first_day($year_start,1,false); $date_end=dol_get_last_day($year_start,3,false); }
	if ($q==2) { $date_start=dol_get_first_day($year_start,4,false); $date_end=dol_get_last_day($year_start,6,false); }
	if ($q==3) { $date_start=dol_get_first_day($year_start,7,false); $date_end=dol_get_last_day($year_start,9,false); }
	if ($q==4) { $date_start=dol_get_first_day($year_start,10,false); $date_end=dol_get_last_day($year_start,12,false); }
}

$nom=$langs->trans("Tickets");
//$nomlink=;
$builddate=time();
$description=$langs->trans("RulesResult");
$period=$html->select_date($date_start,'date_start',0,0,0,'',1,0,1).' - '.$html->select_date($date_end,'date_end',0,0,0,'',1,0,1);
report_header($nom,$nomlink,$period,$periodlink,$description,$builddate,$exportlink);

$p = explode(":", $conf->global->MAIN_INFO_SOCIETE_PAYS);
$idpays = $p[0];

$html = new FormOther($db);
$totaltickets=0;
$ticketstatic=new Ticket($db);
$now=dol_now();

if (!$user->rights->pos->backend)
{
	print '<a href="'.dol_buildpath('/pos/frontend/index.php',1).'"><img src='.dol_buildpath('/pos/frontend/img/bgback.jpg',1).' WIDTH="100%" HEIGHT="100%" ></a>';
}	
else {
	if ($page == -1) $page = 0 ;
	
	$sql = 'SELECT ';
	$sql.= ' f.rowid as ticketid, f.type, f.ticketnumber, f.total_ht, f.total_ttc,';
	$sql.= ' f.date_creation as df, f.fk_user_close,';
	$sql.= ' f.paye as paye, f.fk_statut, f.customer_pay, f.difpayment, ';
	$sql.= ' s.nom, s.rowid as socid,';
	$sql.= ' u.firstname, u.name,';
	$sql.= ' t.name, f.fk_cash';
	$sql.= ' FROM '.MAIN_DB_PREFIX.'societe as s';
	$sql.= ', '.MAIN_DB_PREFIX.'pos_ticket as f';
	$sql.= ', '.MAIN_DB_PREFIX.'pos_cash as t';
	$sql.= ', '.MAIN_DB_PREFIX.'user as u';
	$sql.= ' WHERE f.fk_soc = s.rowid';
	$sql.= " AND f.entity = ".$conf->entity;
	$sql.= " AND f.fk_cash = t.rowid";
	if ($date_start && $date_end) $sql .= " AND f.date_closed >= '".$db->idate($date_start)."' AND f.date_closed <= '".$db->idate($date_end)."'";
	//$sql.= " AND f.fk_user_close = u.rowid";
	
	$sql.= ' AND f.fk_statut > 0';
	
	$sql.= ' GROUP BY f.rowid, f.ticketnumber, f.total_ht, f.total_ttc,';
	$sql.= ' f.date_ticket,';
	$sql.= ' f.paye, f.fk_statut,';
	$sql.= ' s.nom, s.rowid';
	$sql.= ' ORDER BY ';
	$listfield=explode(',',$sortfield);
	foreach ($listfield as $key => $value) $sql.= $listfield[$key].' '.$sortorder.',';
	$sql.= ' f.rowid DESC ';
	
	        //print $sql;
	
	$resql = $db->query($sql);
	if ($resql)
	{
		$num = $db->num_rows($resql);
	
		if ($socid)
		{
			$soc = new Societe($db);
			$soc->fetch($socid);
		}
	
		$param='&amp;socid='.$socid;
		if ($month) $param.='&amp;month='.$month;
		if ($year)  $param.='&amp;year=' .$year;
		
			
		//print_barre_liste($txtListe.' '.($socid?' '.$soc->nom:''),$page,'liste.php',$param,$sortfield,$sortorder,'',$num);
	
		$i = 0;
		print '<form method="get" action="'.$_SERVER["PHP_SELF"].'">'."\n";
		print '<table class="liste" width="100%">';
		print '<tr class="liste_titre">';
		print_liste_field_titre($langs->trans('Ref'),$_SERVER['PHP_SELF'],'f.ticketnumber','',$param,'',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Date'),$_SERVER['PHP_SELF'],'f.date_ticket','',$param,'align="center"',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Terminal'),$_SERVER['PHP_SELF'],'t.name','',$param,'align="center"',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('User'),$_SERVER['PHP_SELF'],'u.name','',$param,'align="center"',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Customer'),$_SERVER['PHP_SELF'],'s.nom','',$param,'',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('AmountTTC'),$_SERVER['PHP_SELF'],'f.total_ttc','',$param,'align="right"',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans('Status'),$_SERVER['PHP_SELF'],'fk_statut,paye','',$param,'align="right"',$sortfield,$sortorder);
	    print '<td class="liste_titre">&nbsp;</td>';
		print '</tr>';
	
		// Lignes des champs de filtre
	/*
		print '<tr class="liste_titre">';
		print '<td class="liste_titre" align="left">';
		print '<input class="flat" size="10" type="text" name="search_ref" value="'.$_GET['search_ref'].'">';
		print '<td class="liste_titre" colspan="1" align="center">';
		print '<input class="flat" type="text" size="1" maxlength="2" name="month" value="'.$month.'">';
		//$syear = $year;
	    //if ($syear == '') $syear = date("Y");
		$html->select_year($syear?$syear:-1,'year',1, 20, 5);
		print '</td>';
		
		print '<td class="liste_titre" align="left">';
		print '<input class="flat" type="text" name="search_cash" value="'.$_GET['search_cash'].'">';
		print '</td>';
		
		print '<td class="liste_titre" align="left">';
		print '<input class="flat" type="text" name="search_user" value="'.$_GET['search_user'].'">';
		print '</td>';
		
		print '<td class="liste_titre" align="left">';
		print '<input class="flat" type="text" name="search_societe" value="'.$_GET['search_societe'].'">';
		print '</td><td class="liste_titre" align="right">';
		print '<input class="flat" type="text" size="10" name="search_montant_ht" value="'.$_GET['search_montant_ht'].'">';
		print '</td><td class="liste_titre" align="right">';
		print '<input class="flat" type="text" size="10" name="search_montant_ttc" value="'.$_GET['search_montant_ttc'].'">';
		print '</td>';
		print '<td class="liste_titre" align="right">';
		print '&nbsp;';
		print '</td>';
		print '<td class="liste_titre" align="right">';
		print '&nbsp;';
		print '</td>';
		print '<td class="liste_titre" align="right"><input type="image" class="liste_titre" name="button_search" src="'.DOL_URL_ROOT.'/theme/'.$conf->theme.'/img/search.png" value="'.dol_escape_htmltag($langs->trans("Search")).'" title="'.dol_escape_htmltag($langs->trans("Search")).'">';
		print '<td class="liste_titre" align="left">&nbsp;</td>';
		print "</td></tr>\n";
	*/
		if ($num > 0)
		{
			$var=True;
			$total=0;
			$totalrecu=0;
	
			while ($i < $num)
			{
				$objp = $db->fetch_object($resql);
				$var=!$var;
	
				print '<tr '.$bc[$var].'>';
				print '<td nowrap="nowrap">';
	
				$ticketstatic->id=$objp->ticketid;
				$ticketstatic->ref=$objp->ticketnumber;
	
				print '<table class="nobordernopadding"><tr class="nocellnopadd">';
	
				print '<td class="nobordernopadding" nowrap="nowrap">';
				//print $ticketstatic->ref;
				print $ticketstatic->getNomUrl(1);
				print '</td>';
	
				print '</tr></table>';
	
				print "</td>\n";
	
				// Date
				print '<td align="center" nowrap>';
				print dol_print_date($db->jdate($objp->df),'day');
				print '</td>';
	
				print '<td align="left">';
				$cash=new Cash($db);
				$cash->fetch($objp->fk_cash);
				print $cash->getNomUrl(1);
				print '</td>';
				print '<td align="left">';
				if ($objp->fk_user_close>0)
				{
					$userstatic=new User($db);
		        	$userstatic->fetch($objp->fk_user_close); 
		       	 	print $userstatic->getNomUrl(1);
				}
				print '</td>';
	            
				print '<td align="left">';
				if(!$user->rights->societe->client->voir) 
				{
					print $objp->nom;
				}
				else
				{
					$thirdparty=new Societe($db);
					$thirdparty->id=$objp->socid;
					$thirdparty->nom=$objp->nom;
					print $thirdparty->getNomUrl(1,'customer');
				}
				print '</td>';
				
				if($objp->type==0)
				{
					$objttc=$objp->total_ttc;		
				}
				else
				{
					$objttc=$objp->total_ttc *-1;
				}
				
				print '<td align="right">'.price($objttc).'</td>';
				
				// Affiche statut de la ticket
				print '<td align="right" nowrap="nowrap">';
				print $ticketstatic->LibStatut($objp->fk_statut,1);
				print "</td>";
				print "<td>&nbsp;</td>";
				print "</tr>\n";
				$total_ttc+=$objttc;
				$totaltickets++;
				$i++;
			}
	
			
			// Print total
			print '<tr class="liste_total">';
			print '<td class="liste_total" align="left">'.$langs->trans('Total').'</td>';
			print '<td class="liste_total" align="left">'.$langs->trans('Tickets').': '.$totaltickets.'</td>';
			print "<td>&nbsp;</td>";
			print "<td>&nbsp;</td>";
			print "<td>&nbsp;</td>";
			print '<td class="liste_total" align="right">'.price($total_ttc).'</td>';
			print "<td>&nbsp;</td>";
			print "<td>&nbsp;</td>";
			
			print '</tr>';
			
		}
	
		print "</table>\n";
		print "</form>\n";
		$db->free($resql);
			
	
	}
	else
	{
		dol_print_error($db);
	}
}
llxFooter();

$db->close();
?>