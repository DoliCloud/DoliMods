<?php
/* Copyright (C) 2007-2018 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 *   	\file       htdocs/sellyoursaas/backoffice/index.php
 *		\ingroup    sellyoursaas
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

require_once(DOL_DOCUMENT_ROOT."/core/lib/admin.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/company.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/class/dolgraph.class.php");
require_once(DOL_DOCUMENT_ROOT."/core/class/doleditor.class.php");
dol_include_once('/sellyoursaas/lib/refresh.lib.php');
dol_include_once("/sellyoursaas/backoffice/lib/refresh.lib.php");		// do not use dol_buildpath to keep global of var into refresh.lib.php working



// Load traductions files requiredby by page
$langs->loadLangs(array("companies","other","sellyoursaas@sellyoursaas"));

// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$mode		= GETPOST('mode','alpha');

$sortfield = GETPOST("sortfield",'alpha');
$sortorder = GETPOST("sortorder",'alpha');
$page = GETPOST("page",'int');
if (empty($page) || $page == -1) { $page = 0; }     // If $page is not defined, or '' or -1
$offset = $conf->liste_limit * $page;
if (! $sortorder) $sortorder='ASC';
if (! $sortfield) $sortfield='t.date_registration';
$limit = GETPOST('limit','int')?GETPOST('limit','int'):$conf->liste_limit;

$pageprev = $page - 1;
$pagenext = $page + 1;

// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}



/*******************************************************************
* ACTIONS
********************************************************************/

if ($action == 'update')
{
	dolibarr_set_const($db,"NLTECHNO_NOTE",GETPOST("NLTECHNO_NOTE", 'none'),'chaine',0,'',$conf->entity);
}

if (GETPOST('saveannounce','alpha'))
{
	dolibarr_set_const($db,"SELLYOURSAAS_ANNOUNCE",GETPOST("SELLYOURSAAS_ANNOUNCE", 'none'),'chaine',0,'',$conf->entity);
}


/***************************************************
* VIEW
****************************************************/

$form=new Form($db);

llxHeader('',$langs->transnoentitiesnoconv('DoliCloudCustomers'),'');

print_fiche_titre($langs->trans("DoliCloudArea"));

$tmparray=dol_getdate(dol_now());
$endyear=$tmparray['year'];
$endmonth=$tmparray['mon'];
$datelastday=dol_get_last_day($endyear, $endmonth, 1);
$startyear=$endyear-2;


print '<div class="fichecenter"><div class="fichethirdleft">';


/*
 * Announce
 */

print '<form method="post" action="'.dol_buildpath('/sellyoursaas/backoffice/index.php',1).'">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<table class="noborder nohover" width="100%">';
print '<tr class="liste_titre">';
print '<td>'.$langs->trans("AnnounceOnCustomerDashboard").'</td></tr>';
print '<tr class="oddeven"><td><textarea class="flat inputsearch centpercent" type="text" name="SELLYOURSAAS_ANNOUNCE">';
print $conf->global->SELLYOURSAAS_ANNOUNCE;
print '</textarea>';
print '<br><input type="submit" name="saveannounce" class="button" value="'.$langs->trans("Save").'"></td></tr>';
print "</table></form><br>";

print '<table class="noborder nohover" width="100%">';
print '<tr class="liste_titre">';
print '<td>'.$langs->trans("CommandToManageRemoteDeploymentAgent").'</td></tr>';
print '<tr class="oddeven"><td><textarea class="flat inputsearch centpercent" type="text" name="SELLYOURSAAS_ANNOUNCE">';
print 'sudo '.$conf->global->DOLICLOUD_SCRIPTS_PATH.'/remote_server_launcher.sh start|status|stop';
print '</textarea>';
print '</td></tr>';
print "</table><br>";


print '</div><div class="fichetwothirdright"><div class="ficheaddleft">';


$total=0;
$totalusers=0;
$totalinstances=0;
$totalinstancespaying=0;
$totalcommissions=0;
$totalresellers=0;
$serverprice = empty($conf->global->SELLYOURSAAS_INFRA_COST)?'100':$conf->global->SELLYOURSAAS_INFRA_COST;

$sql='SELECT COUNT(*) as nb FROM '.MAIN_DB_PREFIX.'societe as s, llx_categorie_fournisseur as c WHERE c.fk_soc = s.rowid AND c.fk_categorie = '.$conf->global->SELLYOURSAAS_DEFAULT_RESELLER_CATEG;
$resql = $db->query($sql);
if ($resql)
{
	$obj = $db->fetch_object($resql);
	if ($obj) $totalresellers = $obj->nb;
}

if ($mode == 'refreshstats')
{
	$rep=sellyoursaas_calculate_stats($db,'');	// $datelastday is last day of current month

	$total=$rep['total'];
	$totalcommissions=$rep['totalcommissions'];
	$totalinstancespaying=$rep['totalinstancespaying'];
	$totalinstancessuspended=$rep['totalinstancessuspended'];
	$totalinstancesexpired=$rep['totalinstancesexpired'];
	$totalinstances=$rep['totalinstances'];
	$totalusers=$rep['totalusers'];

	$_SESSION['stats_total']=$total;
	$_SESSION['stats_totalcommissions']=$totalcommissions;
	$_SESSION['stats_totalinstancespaying']=$totalinstancespaying;
	$_SESSION['stats_totalinstancessuspended']=$totalinstancessuspended;
	$_SESSION['stats_totalinstancesexpired']=$totalinstancesexpired;
	$_SESSION['stats_totalinstances']=$totalinstances;
	$_SESSION['stats_totalusers']=$totalusers;
}
else
{
	$total = $_SESSION['stats_total'];
	$totalcommissions = $_SESSION['stats_totalcommissions'];
	$totalinstancespaying = $_SESSION['stats_totalinstancespaying'];
	$totalinstancessuspended = $_SESSION['stats_totalinstancessuspended'];
	$totalinstancesexpired = $_SESSION['stats_totalinstancesexpired'];
	$totalinstances = $_SESSION['stats_totalinstances'];
	$totalusers = $_SESSION['stats_totalusers'];
}

$part = 0.015;
$benefit=($total * (1 - $part) - $serverprice - $totalcommissions);

// Show totals
print '<table class="noborder" width="100%">';
print '<tr class="liste_titre">';
print '<td class="wordwrap wordbreak"><span class="valignmiddle">'.$langs->trans("Statistics").'</span>';
print '<a href="'.$_SERVER["PHP_SELF"].'?mode=refreshstats">'.img_picto('', 'refresh', '', false, 0, 0, '', 'valignmiddle').'</a>';
print '</td>';
print '<td></td>';
print '</tr>';
print '<tr class="oddeven"><td class="wordwrap wordbreak">';
print $langs->trans("NbOfResellers");
print '</td><td align="right">';
print '<font size="+2">'.$totalresellers.'</font>';
print '</td></tr>';
print '<tr class="oddeven"><td class="wordwrap wordbreak">';
print $form->textwithpicto($langs->trans("NbOfInstancesActivePaying"), $langs->trans("NbOfInstancesActivePayingDesc"));
print ' / '.$langs->trans("NbOfActiveInstances").' ';
print '</td><td align="right">';
print '<font size="+2">'.$totalinstancespaying.' / '.$totalinstances.'</font>';
print '<!-- List of instances : '."\n";
if (is_array($rep['listofinstancespaying']))
{
	foreach($rep['listofinstancespaying'] as $arrayofcontract)
	{
		print $arrayofcontract['thirdparty_name'].' - '.$arrayofcontract['contract_ref']."\n";
	}
}
print "\n".'-->';
print '</td></tr>';
print '<tr class="oddeven"><td class="wordwrap wordbreak">';
print $langs->trans("NbOfSuspendedInstances").' ';
print ' + '.$langs->trans("NbOfExpiredInstances").' ';
print '</td><td align="right">';
print '<font size="+2">'.$totalinstancessuspended.' + '.$totalinstancesexpired.'</font>';
print '</td></tr>';
print '<tr class="oddeven"><td>';
print $langs->trans("NbOfUsers").' ';
print '</td><td align="right" class="wordwrap wordbreak">';
print '<font size="+2">'.$totalusers.'</font>';
print '</td></tr>';
print '<tr class="oddeven"><td class="wordwrap wordbreak">';
print $langs->trans("AverageRevenuePerInstance");
print '</td><td align="right">';
print '<font size="+2">'.($totalinstancespaying?price(price2num($total/$totalinstancespaying,'MT'),1):'0').' </font>';
print '</td></tr>';
print '<tr class="oddeven"><td class="wordwrap wordbreak">';
print $langs->trans("RevenuePerMonth").' ('.$langs->trans("HT").')';
print '</td><td align="right">';
print '<font size="+2">'.price($total,1).' </font>';
print '</td></tr>';
print '<tr class="oddeven"><td class="wordwrap wordbreak">';
print $langs->trans("CommissionPerMonth").' ('.$langs->trans("HT").')';
print '</td><td align="right">';
print '<font size="+2">'.price($totalcommissions).'</font>';
print '</td></tr>';
print '<tr class="oddeven"><td class="wordwrap wordbreak">';
print $langs->trans("ChargePerMonth").' ('.$langs->trans("HT").')';
print '</td><td align="right">';
print '<font size="+2">'.price($serverprice).'€</font>';
print '</td></tr>';
print '<tr class="liste_total"><td class="wrapimp wordwrap wordbreak">';
print $langs->trans("BenefitDoliCloud");
print '<br>(';
print price($total,1).' - '.($part ? ($part*100).'% - ' : '').price($serverprice).'€ - '.price($totalcommissions).'€ = '.price($total * (1 - $part)).'€ - '.price($serverprice).'€ - '.price($totalcommissions).'€';
print ')</td><td align="right">';
print '<font size="+2">'.price($benefit,1).' </font>';
print '</td></tr>';
print '</table>';


print '</div></div></div>';

//$servicetouse='old';
$servicetouse=strtolower($conf->global->SELLYOURSAAS_NAME);

// array(array(0=>'labelxA',1=>yA1,...,n=>yAn), array('labelxB',yB1,...yBn))
$data1 = array();
$sql ='SELECT name, x, y FROM '.MAIN_DB_PREFIX.'dolicloud_stats';
$sql.=" WHERE service = '".$servicetouse."' AND name IN ('total', 'totalcommissions')";
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
$sql.=" WHERE service = '".$servicetouse."' AND name IN ('totalinstancespaying', 'totalusers')";
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


if (empty($conf->dol_optimize_smallscreen))
{
	$WIDTH=600;
	$HEIGHT=260;
}
else
{
	$WIDTH=DolGraph::getDefaultGraphSizeForStats('width');
	$HEIGHT=DolGraph::getDefaultGraphSizeForStats('height');
}

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

	$px1->draw('dolicloudamount.png', $fileurlnb);
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

	$px2->draw('dolicloudcustomersusers.png', $fileurlnb);
}

print '<div class="fichecenter"><br></div>';

//print '<hr>';
print '<div class="fichecenter liste_titre" style="height: 20px;">'.$langs->trans("Graphics").'</div>';

print '<div class="fichecenter"><div class="impair"><center>';
print $px1->show();
print '</center></div></div>';
print '<div class="fichecenter"><div class="impair"><center>';
print $px2->show();
print '</center></div></div>';


print '<br><hr><br>';

print_fiche_titre($langs->trans("Notes"));

print '<br>';

if ($action != 'edit')
{
	print dol_htmlcleanlastbr($conf->global->NLTECHNO_NOTE);

	print '<div class="tabsAction">';

	print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?action=edit">'.$langs->trans("Edit").'</a></div>';

	print '</div>';
}
else
{
	print '<form action="'.$_SERVER["PHP_SELF"].'" method="POST">';
	print '<input type="hidden" name="action" value="update">';
	$doleditor=new DolEditor('NLTECHNO_NOTE',$conf->global->NLTECHNO_NOTE,'',480,'Full');
	print $doleditor->Create(1);
	print '<br>';
	print '<input class="button" type="submit" name="'.$langs->trans("Save").'">';
	print '</form>';
}


// End of page
llxFooter();

$db->close();
