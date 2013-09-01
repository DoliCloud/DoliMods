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
 *   	\file       htdocs/nltechno/dolicloud/index.php
 *		\ingroup    nltechno
 *		\brief      Home page of DoliCloud service
 */

//if (! defined('NOREQUIREUSER'))  define('NOREQUIREUSER','1');
//if (! defined('NOREQUIREDB'))    define('NOREQUIREDB','1');
//if (! defined('NOREQUIRESOC'))   define('NOREQUIRESOC','1');
//if (! defined('NOREQUIRETRAN'))  define('NOREQUIRETRAN','1');
//if (! defined('NOCSRFCHECK'))    define('NOCSRFCHECK','1');
//if (! defined('NOTOKENRENEWAL')) define('NOTOKENRENEWAL','1');
//if (! defined('NOREQUIREMENU'))  define('NOREQUIREMENU','1');	// If there is no menu to show
//if (! defined('NOREQUIREHTML'))  define('NOREQUIREHTML','1');	// If we don't need to load the html.form.class.php
//if (! defined('NOREQUIREAJAX'))  define('NOREQUIREAJAX','1');
//if (! defined("NOLOGIN"))        define("NOLOGIN",'1');		// If this page is public (can be called outside logged session)

// Change this following line to use the correct relative path (../, ../../, etc)
$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");
if (! $res && file_exists("../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../dolibarr/htdocs/main.inc.php");     // Used on dev env only
if (! $res && file_exists("../../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../../dolibarr/htdocs/main.inc.php");   // Used on dev env only
if (! $res && file_exists("../../../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../../../dolibarr/htdocs/main.inc.php");   // Used on dev env only
if (! $res) die("Include of main fails");
require_once(DOL_DOCUMENT_ROOT."/core/lib/company.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/class/dolgraph.class.php");
// Change this following line to use the correct relative path from htdocs (do not remove DOL_DOCUMENT_ROOT)
dol_include_once("/nltechno/class/dolicloudcustomer.class.php");
dol_include_once("/nltechno/dolicloud/lib/refresh.lib.php");

// Load traductions files requiredby by page
$langs->load("companies");
$langs->load("other");
$langs->load("nltechno@nltechno");

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
$limit = $conf->liste_limit;

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




/***************************************************
* VIEW
****************************************************/

$form=new Form($db);
$dolicloudcustomerstatic = new Dolicloudcustomer($db);

llxHeader('',$langs->transnoentitiesnoconv('DoliCloudCustomers'),'');

print_fiche_titre($langs->trans("DoliCloudArea"));


print '<div class="fichecenter"><div class="fichethirdleft">';


/*
 * Search area
 */
$rowspan=2;
print '<form method="post" action="'.dol_buildpath('/nltechno/dolicloud/dolicloud_list.php',1).'">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<table class="noborder nohover" width="100%">';
print '<tr class="liste_titre">';
print '<td colspan="3">'.$langs->trans("Search").'</td></tr>';
print "<tr ".$bc[false]."><td>";
print $langs->trans("Instance").':</td><td><input class="flat" type="text" size="14" name="search_instance"></td>';
print '<td rowspan="'.$rowspan.'"><input type="submit" class="button" value="'.$langs->trans("Search").'"></td></tr>';

print "</table></form><br>";


print '</div><div class="fichetwothirdright"><div class="ficheaddleft">';


$serverlocation=140;	// Price dollar
$dollareuro=0.78;		// Price euro
$serverprice=price2num($serverlocation * $dollareuro, 'MT');
$part=0.3;	// 30%

$total=0;
$totalusers=0;
$totalcustomers=0;
$totalcustomerspaying=0;
$totalcommissions=0;

$rep=dolicloud_calculate_stats($db);

$total=$rep['total'];
$totalcommissions=$rep['totalcommissions'];
$totalcustomerspaying=$rep['totalcustomerspaying'];
$totalcustomers=$rep['totalcustomers'];
$totalusers=$rep['totalusers'];
$benefit=($total * (1 - $part) - $serverprice - $totalcommissions);

// Show totals
$var=false;
print '<table class="noborder" width="100%">';
print '<tr class="liste_titre">';
print '<td colspan="2">'.$langs->trans("Statistics").'</td></tr>';
print '<tr '.$bc[$var].'><td>';
print $langs->trans("NbOfCustomersActive").' / '.$langs->trans("NbOfCustomers").' ';
print '</td><td align="right">';
print '<font size="+2">'.$totalcustomerspaying.' / '.$totalcustomers.'</font>';
print '</td></tr>';
$var=!$var;
print '<tr '.$bc[$var].'><td>';
print $langs->trans("NbOfUsers").' ';
print '</td><td align="right">';
print '<font size="+2">'.$totalusers.'</font>';
print '</td></tr>';
$var=!$var;
print '<tr '.$bc[$var].'><td>';
print $langs->trans("AverageRevenuePerCustomer");
print '</td><td align="right">';
print '<font size="+2">'.($totalcustomerspaying?price(price2num($total/$totalcustomerspaying,'MT'),1):'0').' </font>';
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

$endyear=2013;
$startyear=$endyear-2;

// array(array(0=>'labelxA',1=>yA1,...,n=>yAn), array('labelxB',yB1,...yBn))
$data2 = array();
$statkey='totalcustomers';
$sql ='SELECT name, x, y FROM '.MAIN_DB_PREFIX.'dolicloud_stats';
$sql.=" WHERE name = '".$statkey."'";
$resql=$db->query($sql);
if ($resql)
{
	$num = $db->num_rows($resql);
	$i=0;

	$serie[0]='totalcustomers';
	while ($i < $num)
	{
		$obj=$db->fetch_object($resql);
		if ($obj->x < $startyear."01") continue;
		if ($obj->x > $endyear."12") continue;
		$serie[$obj->x]=$obj->y;
		$i++;
	}

	$data2[]=$serie;
}
else dol_print_error($db);

// Show graph
$px2 = new DolGraph();
$mesg = $px2->isGraphKo();
if (! $mesg)
{
	$px2->SetData($data2);
	unset($data2);
	$px2->SetPrecisionY(0);
	$i=$startyear;$legend=array();
	while ($i <= $endyear)
	{
		$legend[]=$i;
		$i++;
	}
	$px2->SetLegend($legend);
	$px2->SetMaxValue($px2->GetCeilMaxValue());
	$px2->SetWidth($WIDTH);
	$px2->SetHeight($HEIGHT);
	$px2->SetYLabel($langs->trans("AmountOfBillsHT"));
	$px2->SetShading(3);
	$px2->SetHorizTickIncrement(1);
	$px2->SetPrecisionY(0);
	$px2->SetCssPrefix("cssboxes");
	$px2->mode='depth';
	$px2->SetTitle($langs->trans("AmountOfBillsByMonthHT"));

	$px2->draw($filenamenb,$fileurlnb);
}


print '<div>';

print $px2->show();

print '</div>';


// End of page
llxFooter();

$db->close();
?>
