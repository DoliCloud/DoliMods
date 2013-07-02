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
 *	\file       htdocs/pos/backend/place/graph.php
 *	\ingroup    pos
 *	\brief      Page graph des budgets
 *	\version    $Id: graph.php,v 1.5 2011-08-16 15:36:15 jmenent Exp $
 */

$res=@include("../../../main.inc.php");                                   // For root directory
if (! $res) $res=@include("../../../../main.inc.php");                // For "custom" directory

dol_include_once('/pos/backend/lib/place.lib.php');
dol_include_once('/pos/backend/class/ticket.class.php');
dol_include_once('/pos/backend/class/place.class.php');
require_once(DOL_DOCUMENT_ROOT."/core/class/dolgraph.class.php");

$langs->load("pos@pos");

// Security check
$id=GETPOST('id','int');
$ref=GETPOST('ref','string');
$fieldid = 'rowid';

if ($user->societe_id) $socid=$user->societe_id;
//$result=restrictedArea($user,'banque',$id,'bank_account','','',$fieldid);

$mode='standard';

$mesg = '';
$error=0;


/*
 * View
 */
$helpurl='EN:Module_DoliPos|FR:Module_DoliPos_FR|ES:M&oacute;dulo_DoliPos';
llxHeader('','',$helpurl);
if($conf->global->POS_HELP){
	dol_include_once('/pos/backend/class/utils.class.php');
}

$form = new Form($db);

// If lib forced
if (! empty($_GET["lib"])) $conf->global->MAIN_GRAPH_LIBRARY=GETPOST('lib');


$datetime = time();
$year = dol_print_date($datetime, "%Y");
$month = dol_print_date($datetime, "%m");
$day = dol_print_date($datetime, "%d");
if (! empty($_GET["year"]))  $year=sprintf("%04d",GETPOST('year'));
if (! empty($_GET["month"])) $month=sprintf("%02d",GETPOST('month'));


$place = new place($db);
if ($id && ! preg_match('/,/',$_id))	// if for a particular account and not a list
{
	$result=$place->fetch($id);
}
elseif ($ref)
{
	$result =$place->fetch($id,$ref);
	$id= $place->id;
}

$result=dol_mkdir($conf->pos->dir_temp);
if ($result < 0)
{
	$langs->load("errors");
	$error++;
	$mesg='<div class="error">'.$langs->trans("ErrorFailedToCreateDir").'</div>';
}
else
{
	// Definition de $width et $height
	$width = 768;
	$height = 200;

	// Calcul de $min et $max
	$sql = "SELECT MIN(date_creation) as min, MAX(date_creation) as max";
	$sql.= " FROM ".MAIN_DB_PREFIX."pos_ticket";
	$sql.= " WHERE entity = ".$conf->entity;
	$sql.= " AND fk_statut in (1,2,3,4)";
	if ($id && GETPOST('option','string')!='all') $sql.= " AND fk_place IN (".$id.")";
	else $sql.= " AND fk_place IS NOT NULL";

	$sql.= " UNION SELECT MIN(datec) as min, MAX(datec) as max";
	$sql.= " FROM ".MAIN_DB_PREFIX."pos_facture as pf, ".MAIN_DB_PREFIX."facture as fac";
	$sql.= " WHERE fac.entity = ".$conf->entity;
	$sql.= " AND fac.fk_statut in (1,2,3,4)";
	if ($id && GETPOST('option','string')!='all') $sql.= " AND pf.fk_place IN (".$id.")";
	else $sql.= " AND pf.fk_place IS NOT NULL";
	$sql.= " AND fac.rowid = pf.fk_facture";

	$resql = $db->query($sql);
	if ($resql)
	{
		$num = $db->num_rows($resql);
		$i = 1;
		$obj = $db->fetch_object($resql);
		$min = $db->jdate($obj->min);
		$max = $db->jdate($obj->max);
		while ($i < $num)
		{
			$obj = $db->fetch_object($resql);
			if($min > $db->jdate($obj->min))
				$min = $db->jdate($obj->min);
			if($max < $db->jdate($obj->max))
				$max = $db->jdate($obj->max);
			$i++;
		}
	}
	else
	{
		dol_print_error($db);
	}
	$log="graph.php: min=".$min." max=".$max;
	dol_syslog($log);

	// Tableau Budget

	if ($mode == 'standard')
	{
		// Load place
		$credits = array();

		$monthnext = $month+1;
		$yearnext = $year;
		if ($monthnext > 12)
		{
			$monthnext=1;
			$yearnext++;
		}

		$sql = "SELECT date_format(date_creation,'%d') as d";
		$sql.= ", SUM(total_ttc) as total";
		$sql.= " FROM ".MAIN_DB_PREFIX."pos_ticket";
		$sql.= " WHERE entity = ".$conf->entity;
		$sql.= " AND date_creation >= '".$year."-".$month."-01 00:00:00'";
		$sql.= " AND date_creation< '".$yearnext."-".$monthnext."-01 00:00:00'";
		$sql.= " AND fk_statut in (1,2,3,4)";
		if ($id && GETPOST('option','string')!='all') $sql.= " AND fk_place IN (".$id.")";
		else $sql.= " AND fk_place IS NOT NULL";
		$sql.= " GROUP BY date_format(date_creation,'%d')";
		
		$sql.= "UNION SELECT date_format(fac.datec,'%d')as d";
		$sql.= ", SUM(fac.total_ttc) as total";
		$sql.= " FROM ".MAIN_DB_PREFIX."pos_facture as pf, ".MAIN_DB_PREFIX."facture as fac";
		$sql.= " WHERE fac.entity = ".$conf->entity;
		$sql.= " AND fac.datec >= '".$year."-".$month."-01 00:00:00'";
		$sql.= " AND fac.datec < '".$yearnext."-".$monthnext."-01 00:00:00'";
		$sql.= " AND pf.fk_facture = fac.rowid";
		$sql.= " AND fac.fk_statut in (1,2,3,4)";
		if ($id && GETPOST('option')!='all') $sql.= " AND pf.fk_place IN (".$id.")";
		else $sql.= " AND pf.fk_place IS NOT NULL";
		$sql.= " GROUP BY date_format(datec,'%d')";

		$resql = $db->query($sql);
		if ($resql)
		{
			$num = $db->num_rows($resql);
			$i = 0;
			while ($i < $num)
			{
				$row = $db->fetch_row($resql);
				$credits[$row[0]] += $row[1];
				$i++;
			}
			$db->free($resql);
		}
		else
		{
			dol_print_error($db);
		}

		$monthnext = $month+1;
		$yearnext = $year;
		if ($monthnext > 12)
		{
			$monthnext=1;
			$yearnext++;
		}

		// Chargement de labels et data_xxx pour budget
		$labels = array();
		$data_credit = array();
		for ($i = 0 ; $i < 31 ; $i++)
		{
			$data_credit[$i] = isset($credits[substr("0".($i+1),-2)]) ? $credits[substr("0".($i+1),-2)] : 0;
			$labels[$i] = sprintf("%02d",$i+1);
			$datamin[$i] = 0;
		}

		// Fabrication tableau 4a
		$file= $conf->pos->dir_temp."/movement".$id."-".$year.$month.".png";
		$fileurl=DOL_URL_ROOT.'/viewimage.php?modulepart=pos&perm=backend&file='."/movement".$id."-".$year.$month.".png";
		$title=$langs->transnoentities("PlaceMovements").' - '.$langs->transnoentities("Month").': '.$month.' '.$langs->transnoentities("Year").': '.$year;
		$graph_datas=array();
		foreach($data_credit as $i => $val)
		{
			$graph_datas[$i]=array($labels[$i],$data_credit[$i]);
		}
		$px = new DolGraph();
		$px->SetData($graph_datas);
		$px->SetLegendWidthMin(180);
		$px->SetMaxValue($px->GetCeilMaxValue()<0?0:$px->GetCeilMaxValue());
		$px->SetMinValue($px->GetFloorMinValue()>0?0:$px->GetFloorMinValue());
		$px->SetTitle($title);
		$px->SetWidth($width);
		$px->SetHeight($height);
		$px->SetType(array('lines','lines'));
		$px->SetShading(3);
		$px->setBgColor('onglet');
		$px->setBgColorGrid(array(255,255,255));
		$px->SetHorizTickIncrement(1);
		$px->SetPrecisionY(0);
		$px->draw($file,$fileurl);
		
		$show1=$px->show();

		unset($graph_datas);
		unset($px);
		unset($credits);
	}

	// Tableau 4b - place

	if ($mode == 'standard')
	{
		// load place
		$credits = array();
		$sql = "SELECT date_format(date_creation,'%m')";
		$sql.= ", SUM(total_ttc)";
		$sql.= " FROM ".MAIN_DB_PREFIX."pos_ticket";
		$sql.= " WHERE entity = ".$conf->entity;
		$sql.= " AND date_creation >= '".$year."-01-01 00:00:00'";
		$sql.= " AND date_creation <= '".$year."-12-31 23:59:59'";
		$sql.= " AND fk_statut in (1,2,3,4)";
		if ($id && GETPOST('option','string')!='all') $sql.= " AND fk_place IN (".$id.")";
		else $sql.= " AND fk_place IS NOT NULL";
		$sql .= " GROUP BY date_format(date_creation,'%m')";

		$sql.= " UNION SELECT date_format(fac.datec,'%m')";
		$sql.= ", SUM(fac.total_ttc)";
		$sql.= " FROM ".MAIN_DB_PREFIX."pos_facture as pf, ".MAIN_DB_PREFIX."facture as fac";
		$sql.= " WHERE fac.entity = ".$conf->entity;
		$sql.= " AND fac.datec >= '".$year."-01-01 00:00:00'";
		$sql.= " AND fac.datec <= '".$year."-12-31 23:59:59'";
		$sql.= " AND pf.fk_facture = fac.rowid";
		$sql.= " AND fac.fk_statut in (1,2,3,4)";
		if ($id && GETPOST('option')!='all') $sql.= " AND pf.fk_place IN (".$id.")";
		else $sql.= " AND pf.fk_place IS NOT NULL";
		$sql .= " GROUP BY date_format(datec,'%m')";

		$resql = $db->query($sql);
		if ($resql)
		{
			$num = $db->num_rows($resql);
			$i = 0;
			while ($i < $num)
			{
				$row = $db->fetch_row($resql);
				$credits[$row[0]] += $row[1];
				$i++;
			}
			$db->free($resql);
		}
		else
		{
			dol_print_error($db);
		}
		
		// Chargement de labels et data_xxx pour tableau 4 Mouvements
		$labels = array();
		$data_credit = array();
		for ($i = 0 ; $i < 12 ; $i++)
		{
			$data_credit[$i] = isset($credits[substr("0".($i+1),-2)]) ? $credits[substr("0".($i+1),-2)] : 0;
			$labels[$i] = dol_print_date(dol_mktime(12,0,0,$i+1,1,2000),"%b");
			$datamin[$i] = $cash->min_desired;
		}

		// Fabrication tableau 4b
		$file= $conf->pos->dir_temp."/movement".$id."-".$year.".png";
		$fileurl=DOL_URL_ROOT.'/viewimage.php?modulepart=pos_temp&file='."/movement".$id."-".$year.".png";
		
		$title=$langs->transnoentities("PlaceMovements").' - '.$langs->transnoentities("Year").': '.$year;
		$graph_datas=array();
		foreach($data_credit as $i => $val)
		{
			$graph_datas[$i]=array($labels[$i],$data_credit[$i]);
		}
		$px = new DolGraph();
		$px->SetData($graph_datas);
		//$px->SetLegend(array($langs->transnoentities("Credit")));
		$px->SetLegendWidthMin(180);
		$px->SetMaxValue($px->GetCeilMaxValue()<0?0:$px->GetCeilMaxValue());
		$px->SetMinValue($px->GetFloorMinValue()>0?0:$px->GetFloorMinValue());
		$px->SetTitle($title);
		$px->SetWidth($width);
		$px->SetHeight($height);
		$px->SetType(array('lines','lines'));
		$px->SetShading(3);
		$px->setBgColor('onglet');
		$px->setBgColorGrid(array(255,255,255));
		$px->SetHorizTickIncrement(1);
		$px->SetPrecisionY(0);
		$px->draw($file,$fileurl);
		
		$show2=$px->show();

		unset($graph_datas);
		unset($px);
		unset($credits);
	}
}


// Onglets
$head=place_prepare_head($place);
dol_fiche_head($head,'graph',$langs->trans("Place"),0,'placedesk');

if ($mesg) print $mesg.'<br>';

print '<table class="border" width="100%">';

// Name
print '<tr><td valign="top" width="25%">'.$langs->trans("Name").'</td>';
print '<td colspan="3">';
if ($id || $ref)
{
	
	if (! preg_match('/,/',$id))
	{
		$moreparam='&month='.$month.'&year='.$year.($mode=='showalltime'?'&mode=showalltime':'');
		if (GETPOST('option','string')!='all')
		{
			$morehtml='<a href="'.$_SERVER["PHP_SELF"].'?id='.$id.'&option=all'.$moreparam.'">'.$langs->trans("ShowAllPlaces").'</a>';
			print $form->showrefnav($place,'ref','',1,'name','ref','',$moreparam);
		}
		else
		{
			$morehtml='<a href="'.$_SERVER["PHP_SELF"].'?id='.$id.$moreparam.'">'.$langs->trans("BackToPlace").'</a>';
			print $langs->trans("All");
			//print $morehtml;
		}
	}
	/*else
	{
		$bankaccount=new Cash($db);
		$listid=explode(',',$id);
		foreach($listid as $key => $idb)
		{
			$bankaccount->fetch($idb);
			$bankaccount->name=$bankaccount->name;
			print $bankaccount->getNomUrl(1);
			if ($key < (sizeof($listid)-1)) print ', ';
		}
	}*/
}
else
{
	print $langs->trans("All");
}
print '</td></tr>';

// Description
print '<tr><td valign="top">'.$langs->trans("Name").'</td>';
print '<td colspan="3">';

if ($id && GETPOST('option','string')!='all')
{
	print $place->description;
}
else
{
	print $langs->trans("AllPlaces");
}
print '</td></tr>';

print '</table>';

print '<table class="notopnoleftnoright" width="100%">';

// Navigation links
print '<tr><td align="right">'.$morehtml;
print '<br><br></td></tr>';

if ($mode == 'standard')
{
	$prevyear=$year;$nextyear=$year;
	$prevmonth=$month-1;$nextmonth=$month+1;
	if ($prevmonth < 1)  { $prevmonth=12; $prevyear--; }
	if ($nextmonth > 12) { $nextmonth=1; $nextyear++; }

	// For month
	$lien="<a href='".$_SERVER["PHP_SELF"]."?id=".$id.(GETPOST('option','string')!='all'?'':'&option=all')."&year=".$prevyear."&month=".$prevmonth."'>".img_previous()."</a> ".$langs->trans("Month")." <a href='".$_SERVER["PHP_SELF"]."?id=".$id."&year=".$nextyear."&month=".$nextmonth."'>".img_next()."</a>";
	print '<tr><td align="right">'.$lien.'</td></tr>';

	print '<tr><td align="center">';
	$file = "movement".$id."-".$year.$month.".png";
	print $show1;
	print '</td></tr>';

	// For year
	$prevyear=$year-1;$nextyear=$year+1;
	$lien="<a href='".$_SERVER["PHP_SELF"]."?id=".$id.(GETPOST('option','string')!='all'?'':'&option=all')."&year=".($prevyear)."'>".img_previous()."</a> ".$langs->trans("Year")." <a href='".$_SERVER["PHP_SELF"]."?id=".$id."&year=".($nextyear)."'>".img_next()."</a>";
	print '<tr><td align="right">'.$lien.'</td></tr>';

	print '<tr><td align="center">';
	$file = "movement".$id."-".$year.".png";
	print $show2;
	print '</td></tr>';

}

print '</table>';

print "\n</div>\n";

llxFooter();

$db->close();
?>