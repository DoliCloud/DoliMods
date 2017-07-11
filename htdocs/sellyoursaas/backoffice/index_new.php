<?php
/* Copyright (C) 2007-2013 Laurent Destailleur  <eldy@users.sourceforge.net>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
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
 *   	\file       htdocs/sellyoursaas/dolicloud/index_new.php
 *		\ingroup    nltechno
 *		\brief      Home page of DoliCloud service
 */

// Load Dolibarr environment
$res=0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (! $res && ! empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) $res=@include($_SERVER["CONTEXT_DOCUMENT_ROOT"]."/main.inc.php");
// Try main.inc.php into web root detected using web root caluclated from SCRIPT_FILENAME
$tmp=empty($_SERVER['SCRIPT_FILENAME'])?'':$_SERVER['SCRIPT_FILENAME'];$tmp2=realpath(__FILE__); $i=strlen($tmp)-1; $j=strlen($tmp2)-1;
while($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i]==$tmp2[$j]) { $i--; $j--; }
if (! $res && $i > 0 && file_exists(substr($tmp, 0, ($i+1))."/main.inc.php")) $res=@include(substr($tmp, 0, ($i+1))."/main.inc.php");
if (! $res && $i > 0 && file_exists(dirname(substr($tmp, 0, ($i+1)))."/main.inc.php")) $res=@include(dirname(substr($tmp, 0, ($i+1)))."/main.inc.php");
// Try main.inc.php using relative path
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");
if (! $res) die("Include of main fails");

require_once(DOL_DOCUMENT_ROOT."/core/lib/company.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/class/dolgraph.class.php");
dol_include_once('/sellyoursaas/class/dolicloudcustomernew.class.php');
include_once dol_buildpath("/sellyoursaas/dolicloud/lib/refresh.lib.php");		// do not use dol_buildpath to keep global declaration working



// Load traductions files requiredby by page
$langs->load("companies");
$langs->load("other");
$langs->load("nltechno@sellyoursaas");

// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$myparam	= GETPOST('myparam','alpha');

$sortfield = GETPOST("sortfield",'alpha');
$sortorder = GETPOST("sortorder",'alpha');
$page = GETPOST("page",'int');
if ($page == -1) {
    $page = 0;
}
$offset = $conf->liste_limit * $page;
if (! $sortorder) $sortorder='ASC';
if (! $sortfield) $sortfield='t.date_registration';
$limit = GETPOST('limit')?GETPOST('limit','int'):$conf->liste_limit;

$pageprev = $page - 1;
$pagenext = $page + 1;

// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}


if (empty($conf->global->DOLICLOUD_DATABASE_HOST))
{
    accessforbidden("ModuleSetupNotComplete");
    exit;
}
$db2=getDoliDBInstance('mysqli', $conf->global->DOLICLOUD_DATABASE_HOST, $conf->global->DOLICLOUD_DATABASE_USER, $conf->global->DOLICLOUD_DATABASE_PASS, $conf->global->DOLICLOUD_DATABASE_NAME, $conf->global->DOLICLOUD_DATABASE_PORT);
if ($db2->error)
{
    dol_print_error($db2,"host=".$conf->global->DOLICLOUD_DATABASE_HOST.", port=".$conf->global->DOLICLOUD_DATABASE_PORT.", user=".$conf->global->DOLICLOUD_DATABASE_USER.", databasename=".$conf->global->DOLICLOUD_DATABASE_NAME.", ".$db2->error);
    exit;
}

/*******************************************************************
* ACTIONS
********************************************************************/




/***************************************************
* VIEW
****************************************************/

$form=new Form($db);
$dolicloudcustomerstatic = new Dolicloudcustomernew($db,$db2);

llxHeader('',$langs->transnoentitiesnoconv('DoliCloudCustomers'),'');

print_fiche_titre($langs->trans("DoliCloudArea"));

$tmparray=dol_getdate(dol_now());
$endyear=$tmparray['year'];
$endmonth=$tmparray['mon'];
$datelastday=dol_get_last_day($endyear, $endmonth, 1);
$startyear=$endyear-2;


print '<div class="fichecenter"><div class="fichethirdleft">';


/*
 * Search area
 */
$rowspan=2;
print '<form method="post" action="'.dol_buildpath('/sellyoursaas/dolicloud/dolicloud_list_new.php',1).'">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<table class="noborder nohover" width="100%">';
print '<tr class="liste_titre">';
print '<td colspan="3">'.$langs->trans("Search").'</td></tr>';
print "<tr ".$bc[false]."><td>";
print $langs->trans("Instance").':</td><td><input class="flat inputsearch" type="text" name="search_instance"></td>';
print '<td rowspan="'.$rowspan.'"><input type="submit" class="button" value="'.$langs->trans("Search").'"></td></tr>';

print "</table></form><br>";


print '</div><div class="fichetwothirdright"><div class="ficheaddleft">';


$total=0;
$totalusers=0;
$totalinstances=0;
$totalinstancespaying=0;
$totalcommissions=0;

$rep=dolicloud_calculate_stats($db2,'');	// $datelastday is last day of current month

$total=$rep['total'];
$totalcommissions=$rep['totalcommissions'];
$totalinstancespaying=$rep['totalinstancespaying'];
$totalinstances=$rep['totalinstances'];
$totalusers=$rep['totalusers'];
$benefit=($total * (1 - $part) - $serverprice - $totalcommissions);

// Show totals
$var=false;
print '<table class="noborder" width="100%">';
print '<tr class="liste_titre">';
print '<td colspan="2">'.$langs->trans("Statistics").' ('.$langs->trans("FromLiveTables").')</td></tr>';
print '<tr '.$bc[$var].'><td>';
print $langs->trans("NbOfInstancesActivePaying").' / '.$langs->trans("NbOfInstancesPaying").' ';
print '</td><td align="right">';
print '<font size="+2">'.$totalinstancespaying.' / '.$totalinstances.'</font>';
print '</td></tr>';
$var=!$var;
print '<tr '.$bc[$var].'><td>';
print $langs->trans("NbOfUsers").' ';
print '</td><td align="right">';
print '<font size="+2">'.$totalusers.'</font>';
print '</td></tr>';
$var=!$var;
print '<tr '.$bc[$var].'><td>';
print $langs->trans("AverageRevenuePerInstance");
print '</td><td align="right">';
print '<font size="+2">'.($totalinstancespaying?price(price2num($total/$totalinstancespaying,'MT'),1):'0').' </font>';
print '</td></tr>';
$var=!$var;
print '<tr '.$bc[$var].'><td>';
print $langs->trans("RevenuePerMonth");
print '</td><td align="right">';
print '<font size="+2">'.price($total,1).' </font>';
print '</td></tr>';
$var=!$var;
print '<tr '.$bc[$var].'><td>';
print $langs->trans("CommissionPerMonth").' ';
print '</td><td align="right">';
print '<font size="+2">'.price($totalcommissions).'</font>';
print '</td></tr>';
$var=!$var;
print '<tr '.$bc[$var].'><td>';
print $langs->trans("ChargePerMonth").' ';
print '</td><td align="right">';
print '<font size="+2">'.price($serverlocation).'$='.price($serverprice).'€</font>';
print '</td></tr>';
$var=!$var;
print '<tr class="liste_total"><td>';
print $langs->trans("BenefitDoliCloud");
print '<br>(';
//print price($total,1).' - '.($part*100).'% - '.price($serverlocation).'$= ';
print price($total,1).' - '.($part*100).'% - '.price($serverprice).'€ - '.price($totalcommissions).'€ = '.price($total * (1 - $part)).'€ - '.price($serverprice).'€ - '.price($totalcommissions).'€';
print ')</td><td align="right">';
print '<font size="+2">'.price($benefit,1).' </font>';
print '</td></tr>';
print '</table>';


print '</div></div></div>';


// array(array(0=>'labelxA',1=>yA1,...,n=>yAn), array('labelxB',yB1,...yBn))
$data1 = array();
$sql ='SELECT name, x, y FROM '.MAIN_DB_PREFIX.'dolicloud_stats';
$sql.=" WHERE name IN ('total', 'totalcommissions')";
$sql.=" ORDER BY x, name";
$resql=$db->query($sql);
if ($resql)
{
	$num = $db->num_rows($resql);
	$i=0;

	$oldx='';
	$absice=array();
	while ($i < $num)
	{
		$obj=$db->fetch_object($resql);
		if ($obj->x < $startyear."01") { $i++; continue; }
		if ($obj->x > $endyear."12") { $i++; continue; }

		if ($oldx && $oldx != $obj->x)
		{
			// break
			$absice[0]=preg_replace('/^20/','',$oldx);
			$benefit=price2num($absice[1] * (1 - $part) - $serverprice - $absice[2], 'MT');
			$absice[3]=$benefit;
			ksort($absice);
			$data1[]=$absice;
			$absice=array();
		}

		$oldx=$obj->x;

		if ($obj->name == 'total') $absice[1]=$obj->y;
		if ($obj->name == 'totalcommissions') $absice[2]=$obj->y;

		$i++;
	}

	if ($oldx)
	{
		$absice[0]=preg_replace('/^20/','',$oldx);
		$benefit=price2num($absice[1] * (1 - $part) - $serverprice - $absice[2], 'MT');
		$absice[3]=$benefit;
		ksort($absice);
		$data1[]=$absice;
	}
}
else dol_print_error($db);


$data2 = array();
$sql ='SELECT name, x, y FROM '.MAIN_DB_PREFIX.'dolicloud_stats';
$sql.=" WHERE name IN ('totalinstancespaying', 'totalusers')";
$sql.=" ORDER BY x, name";
$resql=$db->query($sql);
if ($resql)
{
	$num = $db->num_rows($resql);
	$i=0;

	$oldx='';
	$absice=array();
	while ($i < $num)
	{
		$obj=$db->fetch_object($resql);

		if ($obj->x < $startyear."01") { $i++; continue; }
		if ($obj->x > $endyear."12") { $i++; continue; }

		if ($oldx && $oldx != $obj->x)
		{
			// break
			$absice[0]=preg_replace('/^20/','',$oldx);
			ksort($absice);
			$data2[]=$absice;
			$absice=array();
		}

		$oldx=$obj->x;

		if ($obj->name == 'totalinstancespaying') $absice[1]=$obj->y;
		if ($obj->name == 'totalusers') $absice[2]=$obj->y;

		$i++;
	}

	if ($oldx)
	{
		$absice[0]=preg_replace('/^20/','',$oldx);
		ksort($absice);
		$data2[]=$absice;
	}
}
else dol_print_error($db);


//$WIDTH=DolGraph::getDefaultGraphSizeForStats('width');
//$HEIGHT=DolGraph::getDefaultGraphSizeForStats('height');
$WIDTH=600;
$HEIGHT=260;


// Show graph
$px1 = new DolGraph();
$mesg = $px1->isGraphKo();
if (! $mesg)
{
	$px1->SetData($data1);
	unset($data1);
	$px1->SetPrecisionY(0);

	$legend=array();
	$legend[0]=$langs->trans("RevenuePerMonth");
	$legend[1]=$langs->trans("CommissionPerMonth");
	$legend[2]=$langs->trans("BenefitDoliCloud");

	$px1->SetLegend($legend);
	$px1->SetMaxValue($px1->GetCeilMaxValue());
	$px1->SetWidth($WIDTH);
	$px1->SetHeight($HEIGHT);
	$px1->SetYLabel($langs->trans("Nb"));
	$px1->SetShading(3);
	$px1->SetHorizTickIncrement(1);
	$px1->SetPrecisionY(0);
	$px1->SetCssPrefix("cssboxes");
	$px1->SetType(array('lines','lines','lines'));
	$px1->mode='depth';
	$px1->SetTitle($langs->trans("Amount"));

	$px1->draw('dolicloudamount.png',$fileurlnb);
}

$px2 = new DolGraph();
$mesg = $px2->isGraphKo();
if (! $mesg)
{
	$px2->SetData($data2);
	unset($data2);
	$px2->SetPrecisionY(0);

	$legend=array();
	$legend[0]=$langs->trans("NbOfInstancesPaying");
	$legend[1]=$langs->trans("NbOfUsers");

	$px2->SetLegend($legend);
	$px2->SetMaxValue($px2->GetCeilMaxValue());
	$px2->SetWidth($WIDTH);
	$px2->SetHeight($HEIGHT);
	$px2->SetYLabel($langs->trans("Nb"));
	$px2->SetShading(3);
	$px2->SetHorizTickIncrement(1);
	$px2->SetPrecisionY(0);
	$px2->SetCssPrefix("cssboxes");
	$px2->SetType(array('lines','lines'));
	$px2->mode='depth';
	$px2->SetTitle($langs->trans("Instances").'/'.$langs->trans("Users"));

	$px2->draw('dolicloudcustomersusers.png',$fileurlnb);
}

print '<div class="fichecenter"><br></div>';

//print '<hr>';
print '<div class="fichecenter liste_titre" style="height: 20px;">'.$langs->trans("Graphics").' ('.$langs->trans("FromHistoryStatsTables").')</div>';

print '<div class="fichecenter"><div class="impair"><center>';
print $px1->show();
print '</center></div></div>';
print '<div class="fichecenter"><div class="impair"><center>';
print $px2->show();
print '</center></div></div>';


// End of page
llxFooter();

$db->close();
