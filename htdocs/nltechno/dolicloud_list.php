<?php
/* Copyright (C) 2007-2012 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 *   	\file       htdocs/nltechno/dolicloud_list.php
 *		\ingroup    nltechno
 *		\brief      This file is an example of a php page
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
*
* Put here all code to do according to value of "action" parameter
********************************************************************/

if ($action == 'add')
{
	$myobject=new Dolicloudcustomer($db);
	$myobject->prop1=$_POST["field1"];
	$myobject->prop2=$_POST["field2"];
	$result=$myobject->create($user);
	if ($result > 0)
	{
		// Creation OK
	}
	{
		// Creation KO
		$mesg=$myobject->error;
	}
}





/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

llxHeader('',$langs->transnoentitiesnoconv('DoliCloudCustomers'),'');

$form=new Form($db);
$dolicloudcustomerstatic = new Dolicloudcustomer($db);

print '<script type="text/javascript" language="javascript">
jQuery(document).ready(function() {
	function init_myfunc()
	{
		jQuery("#myid").removeAttr(\'disabled\');
		jQuery("#myid").attr(\'disabled\',\'disabled\');
	}
	init_myfunc();
	jQuery("#mybutton").click(function() {
		init_needroot();
	});
});
</script>';


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
$sql.= " t.modulesenabled";
$sql.= " FROM ".MAIN_DB_PREFIX."dolicloud_customers as t";
//    $sql.= " WHERE field3 = 'xxx'";
$sql.= $db->order($sortfield,$sortorder);
$sql.= $db->plimit($conf->liste_limit +1, $offset);


$param='';
if ($month)              $param.='&month='.$month;
if ($year)               $param.='&year=' .$year;
if ($search_ref)         $param.='&search_ref=' .$search_ref;
if ($search_societe)     $param.='&search_societe=' .$search_societe;
if ($search_sale > 0)    $param.='&search_sale=' .$search_sale;
print_barre_liste($langs->trans('DoliCloudCustomers'),$page,$_SERVER["PHP_SELF"],$param,$sortfield,$sortorder,'',$num);


print '<table class="liste" width="100%">';
print '<tr class="liste_titre">';
print_liste_field_titre($langs->trans('Instance'),$_SERVER['PHP_SELF'],'t.instance','',$param,'align="left"',$sortfield,$sortorder);
print_liste_field_titre($langs->trans('Organization'),$_SERVER['PHP_SELF'],'t.organization','',$param,'',$sortfield,$sortorder);
print_liste_field_titre($langs->trans('EMail'),$_SERVER['PHP_SELF'],'t.email','',$param,'',$sortfield,$sortorder);
print_liste_field_titre($langs->trans('Plan'),$_SERVER['PHP_SELF'],'t.plan','',$param,'',$sortfield,$sortorder);
print_liste_field_titre($langs->trans('DateRegistration'),$_SERVER['PHP_SELF'],'t.date_registration','',$param,'',$sortfield,$sortorder);
print_liste_field_titre($langs->trans('DateEndFreePeriod'),$_SERVER['PHP_SELF'],'t.date_endfreeperiod','',$param,'',$sortfield,$sortorder);
print_liste_field_titre($langs->trans('DateLastCheck'),$_SERVER['PHP_SELF'],'t.lastcheck','',$param,'',$sortfield,$sortorder);
print_liste_field_titre($langs->trans('NbOfUsers'),$_SERVER['PHP_SELF'],'t.nbofusers','',$param,'',$sortfield,$sortorder);
print_liste_field_titre($langs->trans('LastLogin'),$_SERVER['PHP_SELF'],'t.lastlogin','',$param,'',$sortfield,$sortorder);
print_liste_field_titre($langs->trans('DateLastLogin'),$_SERVER['PHP_SELF'],'t.datelastlogin','',$param,'',$sortfield,$sortorder);
print_liste_field_titre($langs->trans('Status'),$_SERVER['PHP_SELF'],'t.status','',$param,'align="right"',$sortfield,$sortorder);
print '</tr>';

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
                $var=!$var;
                // You can use here results
                print '<tr '.$bc[$var].'><td align="left">';
                $dolicloudcustomerstatic->id=$obj->rowid;
                $dolicloudcustomerstatic->ref=$obj->instance;
                $dolicloudcustomerstatic->status=$obj->status;
                print $dolicloudcustomerstatic->getNomUrl(1,'');
                print '</td><td>';
                print $obj->organization;
                print '</td><td>';
                print $obj->email;
                print '</td><td>';
                print $obj->plan;
                print '</td><td>';
                print dol_print_date($obj->date_registration,'dayhour');
                print '</td><td>';
                print dol_print_date($obj->date_endfreeperiod,'day');
                print '</td><td>';
                print $obj->lastcheck;
                print '</td><td>';
                print $obj->nbofusers;
                print '</td><td>';
                print $obj->lastlogin;
                print '</td><td>';
                print ($obj->date_lastlogin?dol_print_date($obj->date_lastlogin,'dayhour','tzuser'):'');
                print '</td><td align="right">';
                print $dolicloudcustomerstatic->getLibStatut(3);
                print '</td>';
                print '</tr>';
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
print '</table>';


// End of page
llxFooter();
$db->close();
?>
