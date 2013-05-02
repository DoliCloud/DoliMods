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
 *   	\file       htdocs/nltechno/dolicloud/dolicloud_customers.php
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
// Change this following line to use the correct relative path from htdocs (do not remove DOL_DOCUMENT_ROOT)
dol_include_once("/nltechno/class/dolicloudcustomer.class.php");

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


$sql = "SELECT";
$sql.= " t.rowid,";

$sql.= " t.instance,";
$sql.= " t.organization,";
$sql.= " t.email,";
$sql.= " t.plan,";
$sql.= " t.date_registration,";
$sql.= " t.date_endfreeperiod,";
$sql.= " t.status,";
$sql.= " t.partner,";
$sql.= " t.total_invoiced,";
$sql.= " t.total_payed,";
$sql.= " t.tms,";
$sql.= " t.hostname_web,";
$sql.= " t.username_web,";
$sql.= " t.password_web,";
$sql.= " t.hostname_db,";
$sql.= " t.database_db,";
$sql.= " t.port_db,";
$sql.= " t.username_db,";
$sql.= " t.password_db,";
$sql.= " t.lastcheck,";
$sql.= " t.nbofusers,";
$sql.= " t.lastlogin,";
$sql.= " t.lastpass,";
$sql.= " t.date_lastlogin,";
$sql.= " t.modulesenabled,";
$sql.= " p.price_instance,";
$sql.= " p.price_user,";
$sql.= " p.price_gb";
$sql.= " FROM ".MAIN_DB_PREFIX."dolicloud_customers as t";
$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."c_dolicloud_plans as p ON t.plan = p.code";
//$sql.= $db->order($sortfield,$sortorder);
//$sql.= $db->plimit($conf->liste_limit +1, $offset);

$totalusers=0;
$totalcustomers=0;
$totalcustomerspaying=0;
$total=0;

$var=false;

dol_syslog($script_file." sql=".$sql, LOG_DEBUG);
$resql=$db->query($sql);
if ($resql)
{
    $num = $db->num_rows($resql);
    $i = 0;
    if ($num)
    {
        while ($i < $num)
        {
            $obj = $db->fetch_object($resql);
            if ($obj)
            {
                $price=$obj->price_instance + ($obj->nbofusers * $obj->price_user);
                $totalcustomers++;
				$totalusers+=$obj->nbofusers;
                if ($obj->status != 'ACTIVE')
                {
                }
                else
              {
                	$totalcustomerspaying++;
                	$total+=$price;
                }
            }
            $i++;
        }
    }
}
else
{
    $error++;
    dol_print_error($db);
}

// Show totals
$serverlocation=140;	// Price dollar
$dollareuro=0.78;		// Price euro
$serverprice=price2num($serverlocation * $dollareuro, 'MT');
$part=0.3;	// 30%

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
print '<font size="+2">'.price(price2num($total/$totalcustomerspaying,'MT'),1).' </font>';
print '</td></tr>';
$var=!$var;
print '<tr '.$bc[$var].'><td>';
print $langs->trans("RevenuePerMonth");
print '</td><td align="right">';
print '<font size="+2">'.price($total,1).' </font>';
print '</td></tr>';
$var=!$var;
print '<tr class="liste_total"><td>';
print $langs->trans("BenefitDoliCloud");
print '<br>(';
print price($total,1).' - '.($part*100).'% - '.price($serverlocation).'$= ';
print price($total,1).' - '.($part*100).'% - '.price($serverprice).'€ = '.price($total * (1 - $part)).'€ - '.price($serverprice).'€';
print ')</td><td align="right">';
print '<font size="+2">'.price(($total * (1 - $part) - $serverprice),1).' </font>';
print '</td></tr>';
print '</table>';


print '</div></div></div>';


// End of page
llxFooter();

$db->close();
?>
