<?php
/* Copyright (C) 2011-2012 Juanjo Menent           <2byte.es>
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
 *	\file       htdocs/pos/backend/closes.php
 *	\ingroup    ticket
 *	\brief      Page to list control closes history
 */

$res=@include("../../main.inc.php");                                   // For root directory
if (! $res) $res=@include("../../../main.inc.php");                // For "custom" directory

dol_include_once('/pos/backend/class/ticket.class.php');
require_once(DOL_DOCUMENT_ROOT."/core/class/html.formother.class.php");
dol_include_once('/pos/backend/class/cash.class.php');
require_once(DOL_DOCUMENT_ROOT ."/core/lib/date.lib.php");

$langs->load('pos@pos');
$langs->load('deliveries');
$langs->load('companies');

$ticketyear=GETPOST('ticketyear','int');
$ticketmonth=GETPOST('ticketmonth','int');
$deliveryyear=GETPOST('deliveryyear','int');
$deliverymonth=GETPOST('deliverymonth','int');
$sref=GETPOST('sref','alfa');
$sref_client=GETPOST('sref_client','alfa');
$snom=GETPOST('snom');
$sall=GETPOST('sall');
$socid=GETPOST('socid','int');
$viewstatut=GETPOST('viewstatut','int');

// Security check
$ticketid = GETPOST('ticketid','int');//isset($_GET["ticketid"])?$_GET["ticketid"]:'';
if ($user->societe_id) $socid=$user->societe_id;

if (!$user->rights->pos->backend)
accessforbidden();

$sortfield = GETPOST('sortfield','alpha');
$sortorder = GETPOST('sortorder','alpha');
$page = GETPOST('page','int');

if ($page == -1) { $page = 0; }
$offset = $conf->liste_limit * $page;
$pageprev = $page - 1;
$pagenext = $page + 1;

$month	=GETPOST('month','int');
$year	=GETPOST('year','int');

$limit = $conf->liste_limit;
if (! $sortorder) $sortorder='DESC';
if (! $sortfield) $sortfield='f.date_c';

/*
 * View
 */
$helpurl='EN:Module_DoliPos|FR:Module_DoliPos_FR|ES:M&oacute;dulo_DoliPos';
llxHeader("",$langs->trans("Tickets"),$helpurl);
if($conf->global->POS_HELP){
	dol_include_once('/pos/backend/class/utils.class.php');
}

$html = new FormOther($db);
$now=dol_now();
$ticketstatic=new Ticket($db);

if ($page == -1) $page = 0 ;

$sql = 'SELECT ';
$sql.= ' f.rowid, f.fk_cash, f.fk_user, f.amount_teor, f.amount_real,  f.amount_diff,';
$sql.= ' f.date_c, f.type_control';
$sql.= ' from '.MAIN_DB_PREFIX.'pos_control_cash as f';
$sql.= " WHERE f.entity = ".$conf->entity;
if ($viewstatut <> '') $sql.= ' AND f.type_control = '.$viewstatut;

if ($_GET['filtre'])
{
	$filtrearr = explode(',', $_GET['filtre']);
	foreach ($filtrearr as $fil)
	{
		$filt = explode(':', $fil);
		$sql .= ' AND ' . trim($filt[0]) . ' = ' . trim($filt[1]);
		}
}
if ($_GET['search_ref'])
{
	 $sql.= ' AND f.rowid LIKE \'%'.$db->escape(trim($_GET['search_ref'])).'%\'';
}
if ($_GET['search_user'])
{
	$sql.= ' AND s.nom LIKE \'%'.$db->escape(trim($_GET['search_user'])).'%\'';
}

if ($month > 0)
{
	if ($year > 0)
		$sql.= " AND f.date_c BETWEEN '".$db->idate(dol_get_first_day($year,$month,false))."' AND '".$db->idate(dol_get_last_day($year,$month,false))."'";
	else
	$sql.= " AND date_format(f.date_c, '%m') = '".$month."'";
}
else if ($year > 0)
{
	$sql.= " AND f.date_c BETWEEN '".$db->idate(dol_get_first_day($year,1,false))."' AND '".$db->idate(dol_get_last_day($year,12,false))."'";
}
if ($_POST['sf_ref'])
{
	$sql.= ' AND rowid LIKE \'%'.$db->escape(trim($_POST['sf_ref'])) . '%\'';
	}
if ($sall)
{
	$sql.= ' AND (s.nom LIKE \'%'.$db->escape($sall).'%\' OR f.ticketnumber LIKE \'%'.$db->escape($sall).'%\' OR f.note LIKE \'%'.$db->escape($sall).'%\' OR fd.description LIKE \'%'.$db->escape($sall).'%\')';
}
if (! $sall)
{
	/*$sql.= ' GROUP BY f.rowid, f.type, f.increment, f.total_ht, f.total_ttc,';
	$sql.= ' f.date_c, ';
	$sql.= ' f.paye, f.fk_statut,';*/
}
$sql.= ' ORDER BY ';
$listfield=explode(',',$sortfield);
foreach ($listfield as $key => $value) $sql.= $listfield[$key].' '.$sortorder.',';
$sql.= ' f.rowid DESC ';
$sql.= $db->plimit($limit+1,$offset);
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

	if($viewstatut)
		$label=$langs->trans('CloseList');
	else
		$label=$langs->trans('ArchingList');
	
	print_barre_liste($label.' '.($socid?' '.$soc->nom:''),$page,'closes.php',$param,$sortfield,$sortorder,'',$num);

	$i = 0;
	print '<form method="get" action="'.$_SERVER["PHP_SELF"].'">'."\n";
	print '<table class="liste" width="100%">';
	print '<tr class="liste_titre">';
	print_liste_field_titre($langs->trans('Ref'),$_SERVER['PHP_SELF'],'rowid','',$param,'',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans('Type'),$_SERVER['PHP_SELF'],'type_control','',$param,'',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans('Date'),$_SERVER['PHP_SELF'],'date_c','',$param,'align="center"',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans('Caja'),$_SERVER['PHP_SELF'],'fk_cash','',$param,'',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans('User'),$_SERVER['PHP_SELF'],'fk_user','',$param,'',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans('AmountTeor'),$_SERVER['PHP_SELF'],'amount_teor','',$param,'align="right"',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans('AmountReel'),$_SERVER['PHP_SELF'],'amount_real','',$param,'align="right"',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans('AmountDiff'),$_SERVER['PHP_SELF'],'amount_diff','',$param,'align="right"',$sortfield,$sortorder);
	//print_liste_field_titre($langs->trans('AmountOut'),$_SERVER['PHP_SELF'],'amount_mov_out','',$param,'align="right"',$sortfield,$sortorder);
	//print_liste_field_titre($langs->trans('AmountIn'),$_SERVER['PHP_SELF'],'amount_mov_in',$param,'align="right"',$sortfield,$sortorder);
	//print_liste_field_titre($langs->trans('AmountNextDay'),$_SERVER['PHP_SELF'],'amount_next_day','',$param,'align="right"',$sortfield,$sortorder);
    print '<td class="liste_titre">&nbsp;</td>';
	print '</tr>';

	// Lignes des champs de filtre

	print '<tr class="liste_titre">';
	print '<td class="liste_titre" align="left">';
	print '<input class="flat" size="10" type="text" name="search_ref" value="'.$_GET['search_ref'].'">';
	
	print '<td class="liste_titre" align="right">';
	print '&nbsp;';
	print '</td>';
	
	print '<td class="liste_titre" colspan="1" align="center">';
	print '<input class="flat" type="text" size="1" maxlength="2" name="month" value="'.$month.'">';
	//$syear = $year;
    //if ($syear == '') $syear = date("Y");
	$html->select_year($syear?$syear:-1,'year',1, 20, 5);
	print '</td>';
	//print '<td class="liste_titre" align="left">&nbsp;</td>';
	print '<td class="liste_titre" align="left">';
	print '<input class="flat" size="10" type="text" name="search_cash" value="'.$_GET['search_cash'].'">';
	print '</td>';
	print '<td class="liste_titre" align="left">';
	print '<input class="flat" size="10" type="text" name="search_user" value="'.$_GET['search_user'].'">';
	print '</td>';
	print '<td class="liste_titre" align="right">';
	print '&nbsp;';
	print '</td>';
	print '<td class="liste_titre" align="right">';
	print '&nbsp;';
	print '</td>';
	/*print '<td class="liste_titre" align="right">';
	print '&nbsp;';
	print '</td>';
	print '<td class="liste_titre" align="right">';
	print '&nbsp;';
	print '</td>';
	print '<td class="liste_titre" align="right">';
	print '&nbsp;';
	print '</td>';*/
	print '<td class="liste_titre" align="right"><input type="image" class="liste_titre" name="button_search" src="'.DOL_URL_ROOT.'/theme/'.$conf->theme.'/img/search.png" value="'.dol_escape_htmltag($langs->trans("Search")).'" title="'.dol_escape_htmltag($langs->trans("Search")).'">';
	print '<td class="liste_titre" align="left">&nbsp;</td>';
	print "</td></tr>\n";

	if ($num > 0)
	{
		$var=True;
		$total=0;
		$totalrecu=0;
		
		$controlstatic = new ControlCash($db, $objp->fk_cash);
		$controlstatic->type_control = $objp->type_control;
		
		while ($i < min($num,$limit))
		{
			$objp = $db->fetch_object($resql);
			$var=!$var;

			//$datelimit=$db->jdate($objp->datelimite);

			print '<tr '.$bc[$var].'>';
			print '<td nowrap="nowrap">';

			print '<table class="nobordernopadding"><tr class="nocellnopadd">';

			//Id cash control
			
			print '<td class="nobordernopadding" nowrap="nowrap">';
			if($objp->type_control){
				if($conf->global->POS_FACTURE == 1){
					print '<a href="listefac.php?closeid='.$objp->rowid.'&terminalid='.$objp->fk_cash.'&viewstatut=2">'.$objp->rowid.'</a>';
				}
				else{	
					print '<a href="liste.php?closeid='.$objp->rowid.'&terminalid='.$objp->fk_cash.'&viewstatut=2">'.$objp->rowid.'</a>';
				}
			}
			else {
				print $objp->rowid;
			}
			print '</td>';

			print '</tr></table>';

			print "</td>\n";
			
			//Type
	        print '<td align="left" nowrap="nowrap">';
			print $controlstatic->LibStatut($objp->type_control,2);
			print "</td>";;

			// Date
			print '<td align="center" nowrap>';
			print dol_print_date($db->jdate($objp->date_c),'day');
			print '</td>';
			
			//Cash
			print '<td>';
			$cash=new Cash($db);
			$cash->fetch($objp->fk_cash);
			print $cash->getNomUrl(1);
			print '</td>';
	
			//User
			$userstatic=new User($db);
	        $userstatic->fetch($objp->fk_user); 
	        print "<td>".$userstatic->getNomUrl(1)."</td>\n";
	        
	        //Teoric
	        print '<td align="right">'.price($objp->amount_teor).'</td>';
	        
	        //Real
	        print '<td align="right">'.price($objp->amount_real).'</td>';
	        
	        //Diff
	        print '<td align="right">'.price($objp->amount_diff).'</td>';
	        /*
	        //Out
	        print '<td align="right"></td>';
	        
	        //In
	        print '<td align="right"></td>';
	        
	        //Next day
	        print '<td align="right"></td>';*/
	        
	        print '<td align="right"></td>';
            		
           $i++;
		}

		
	}

	print "</table>\n";
	print "</form>\n";
	$db->free($resql);
	
	/*print '<div class="tabsAction">';
	print '<a class="butAction" href="'.DOL_URL_ROOT.'/pos/backend/closes/fiche.php">'.$langs->trans('NewClose').'</a>';
	print '</div>';*/
}
else
{
	dol_print_error($db);
}

llxFooter();

$db->close();
?>