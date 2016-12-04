<?php
/* Copyright (C) 2007-2014 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 *   	\file       htdocs/nltechno/dolicloud/dolicloud_list.php
 *		\ingroup    nltechno
 *		\brief      This file is an example of a php page
 */

// Change this following line to use the correct relative path (../, ../../, etc)
$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");
if (! $res && file_exists("../../../../main.inc.php")) $res=@include("../../../../main.inc.php");
if (! $res && preg_match('/\/nltechno([^\/]*)\//',$_SERVER["PHP_SELF"],$reg)) $res=@include("../../../../dolibarr".$reg[1]."/htdocs/main.inc.php"); // Used on dev env only
if (! $res) die("Include of main fails");
require_once(DOL_DOCUMENT_ROOT."/core/lib/company.lib.php");
dol_include_once("/nltechno/class/dolicloudcustomer.class.php");
dol_include_once("/nltechno/class/dolicloudcustomernew.class.php");


$db2=getDoliDBInstance('mysqli', $conf->global->DOLICLOUD_DATABASE_HOST, $conf->global->DOLICLOUD_DATABASE_USER, $conf->global->DOLICLOUD_DATABASE_PASS, $conf->global->DOLICLOUD_DATABASE_NAME, $conf->global->DOLICLOUD_DATABASE_PORT);
if ($db2->error)
{
	dol_print_error($db2,"host=".$conf->global->DOLICLOUD_DATABASE_HOST.", port=".$conf->global->DOLICLOUD_DATABASE_PORT.", user=".$conf->global->DOLICLOUD_DATABASE_USER.", databasename=".$conf->global->DOLICLOUD_DATABASE_NAME.", ".$db2->error);
	exit;
}


// Load traductions files requiredby by page
$langs->load("companies");
$langs->load("other");
$langs->load("nltechno@nltechno");

// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$myparam	= GETPOST('myparam','alpha');

$search_dolicloud = GETPOST("search_dolicloud");	// Search from index page
$search_multi = GETPOST("search_multi");
$search_instance = GETPOST("search_instance");
$search_organization = GETPOST("search_organization");
$search_plan = GETPOST("search_plan");
$search_partner = GETPOST("search_partner");
$search_source = GETPOST("search_source");
$search_email = GETPOST("search_email");
$search_lastlogin = GETPOST("search_lastlogin");
$search_status = GETPOST('search_status');

$sortfield = GETPOST("sortfield",'alpha');
$sortorder = GETPOST("sortorder",'alpha');
$page = GETPOST("page",'int');
if ($page == -1) {
    $page = 0;
}
$offset = $conf->liste_limit * $page;
if (! $sortorder) $sortorder='DESC';
if (! $sortfield) $sortfield='i.created_date';
$limit = GETPOST('limit')?GETPOST('limit','int'):$conf->liste_limit;

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

$arraystatus=Dolicloudcustomernew::$listOfStatusNewShort;

llxHeader('',$langs->transnoentitiesnoconv('DoliCloudInstances'),'');

$form=new Form($db);
$dolicloudcustomerstaticnew = new Dolicloudcustomernew($db,$db2);

$now=dol_now();

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
$sql.= " i.id,";

$sql.= " i.version,";
$sql.= " i.app_package_id,";
$sql.= " i.created_date as date_registration,";
$sql.= " i.customer_id,";
$sql.= " i.db_name,";
$sql.= " i.db_password,";
$sql.= " i.db_port,";
$sql.= " i.db_server,";
$sql.= " i.db_username,";
$sql.= " i.default_password,";
$sql.= " i.deployed_date,";
$sql.= " i.domain_id,";
$sql.= " i.fs_path,";
$sql.= " i.install_time,";
$sql.= " i.ip_address,";
$sql.= " i.last_login as date_lastlogin,";
$sql.= " i.last_updated,";
$sql.= " i.name as instance,";
$sql.= " i.os_password,";
$sql.= " i.os_username,";
$sql.= " i.rm_install_url,";
$sql.= " i.rm_web_app_name,";
$sql.= " i.status as instance_status,";
$sql.= " i.undeployed_date,";
$sql.= " i.access_enabled,";
$sql.= " i.default_username,";
$sql.= " i.ssh_port,";

$sql.= " p.id as planid,";
$sql.= " p.name as plan,";

$sql.= " im.value as nbofusers,";
$sql.= " im.last_updated as lastcheck,";

$sql.= " pao.amount as price_user,";
$sql.= " pao.min_threshold as min_threshold,";

$sql.= " pl.amount as price_instance,";
$sql.= " pl.meter_id as plan_meter_id,";

$sql.= " c.org_name as organization,";
$sql.= " c.status as status,";
$sql.= " c.past_due_start,";
$sql.= " c.suspension_date,";

$sql.= " s.payment_status,";
$sql.= " s.status as subscription_status,";

$sql.= " per.username as email,";
$sql.= " per.first_name as firstname,";
$sql.= " per.last_name as lastname,";

$sql.= " cp.org_name as partner";

$sql.= " FROM app_instance as i";
$sql.= " LEFT JOIN app_instance_meter as im ON i.id = im.app_instance_id AND im.meter_id = 1,";	// meter_id = 1 = users
$sql.= " customer as c";
$sql.= " LEFT JOIN channel_partner_customer as cc ON cc.customer_id = c.id";
$sql.= " LEFT JOIN channel_partner as cp ON cc.channel_partner_id = cp.id";
$sql.= " LEFT JOIN person as per ON c.primary_contact_id = per.id,";
$sql.= " subscription as s, plan as pl";
$sql.= " LEFT JOIN plan_add_on as pao ON pl.id=pao.plan_id and pao.meter_id = 1,";	// meter_id = 1 = users
$sql.= " app_package as p";
$sql.= " WHERE i.customer_id = c.id AND c.id = s.customer_id AND s.plan_id = pl.id AND pl.app_package_id = p.id";
if ($search_dolicloud) $sql.='';
if ($search_multi) $sql.=" AND (i.name LIKE '%".$db->escape($search_multi)."%' OR c.org_name LIKE '%".$db->escape($search_multi)."%' OR per.username LIKE '%".$db->escape($search_multi)."%')";
if ($search_instance) $sql.=" AND i.name LIKE '%".$db->escape($search_instance)."%'";
if ($search_organization) $sql.=" AND c.org_name LIKE '%".$db->escape($search_organization)."%'";
if ($search_plan) $sql.=" AND p.name LIKE '%".$db->escape($search_plan)."%'";
if ($search_partner) $sql.=" AND cp.org_name LIKE '%".$db->escape($search_partner)."%'";
if ($search_source) $sql.=" AND t.source LIKE '%".$db->escape($search_source)."%'";
if ($search_email) $sql.=" AND per.username LIKE '%".$db->escape($search_email)."%'";
if ($search_lastlogin) $sql.=" AND i.last_login LIKE '%".$db->escape($search_lastlogin)."%'";
if (! empty($search_status) && ! is_numeric($search_status))
{
	//if ($search_status == 'UNDEPLOYED') $sql.=" AND i.status LIKE '%".$db->escape($search_status)."%'";
	//else $sql.=" AND c.status LIKE '%".$db->escape($search_status)."%'";
	$sql.=" AND c.status LIKE '%".$db->escape($search_status)."%'";
}
//print $sql;

// Count total nb of records
$nbtotalofrecords = -1;
if (empty($conf->global->MAIN_DISABLE_FULL_SCANLIST))
{
	$result = $db2->query($sql);
	$nbtotalofrecords = $db2->num_rows($result);
}

$sql.= $db->order($sortfield,$sortorder);
$sql.= $db->plimit($conf->liste_limit +1, $offset);

$param='';
if ($month)              	$param.='&month='.$month;
if ($year)               	$param.='&year=' .$year;
if ($search_instance)    	$param.='&search_instance='.urlencode($search_instance);
if ($search_organization) 	$param.='&search_organization='.urlencode($search_organization);
if ($search_plan) 			$param.='&search_plan='.urlencode($search_plan);
if ($search_partner) 		$param.='&search_partner='.urlencode($search_partner);
if ($search_source) 		$param.='&search_source='.urlencode($search_source);
if ($search_email) 			$param.='&search_email='.urlencode($search_email);
if ($search_lastlogin) 		$param.='&search_lastlogin='.urlencode($search_lastlogin);
if ($search_status)      	$param.='&search_status='.urlencode($search_status);


$totalinstances=0;
$totalinstancespaying=0;
$total=0;

$var=false;
//print $sql;
dol_syslog($script_file." sql=".$sql, LOG_DEBUG);
$resql=$db2->query($sql);
if ($resql)
{
    $num = $db2->num_rows($resql);

    print_barre_liste($langs->trans('DoliCloudInstances'),$page,$_SERVER["PHP_SELF"],$param,$sortfield,$sortorder,'',$num,$nbtotalofrecords);

    if ($search_multi) print $langs->trans("Search").': '.$search_multi.'<br><br>'."\n";

    // Lignes des champs de filtre
    print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';

    print '<table class="liste" width="100%">';
    print '<tr class="liste_titre">';
    print_liste_field_titre($langs->trans('Instance'),$_SERVER['PHP_SELF'],'i.name','',$param,'align="left"',$sortfield,$sortorder);
    print_liste_field_titre($langs->trans('Organization'),$_SERVER['PHP_SELF'],'c.organization','',$param,'',$sortfield,$sortorder);
    print_liste_field_titre($langs->trans('EMail'),$_SERVER['PHP_SELF'],'per.email','',$param,'',$sortfield,$sortorder);
    print_liste_field_titre($langs->trans('Plan'),$_SERVER['PHP_SELF'],'pl.plan','',$param,'',$sortfield,$sortorder);
    print_liste_field_titre($langs->trans('Partner'),$_SERVER['PHP_SELF'],'cc.partner','',$param,'',$sortfield,$sortorder);
    //print_liste_field_titre($langs->trans('Source'),$_SERVER['PHP_SELF'],'t.source','',$param,'',$sortfield,$sortorder);
    print_liste_field_titre($langs->trans('DateRegistration'),$_SERVER['PHP_SELF'],'t.date_registration','',$param,'',$sortfield,$sortorder);
    print_liste_field_titre($langs->trans('DateNextBilling'),$_SERVER['PHP_SELF'],'c.past_due_start','',$param,'',$sortfield,$sortorder);
    print_liste_field_titre($langs->trans('DateLastCheck'),$_SERVER['PHP_SELF'],'im.last_updated','',$param,'',$sortfield,$sortorder);
    print_liste_field_titre($langs->trans('NbOfUsers'),$_SERVER['PHP_SELF'],'im.value','',$param,'align="right"',$sortfield,$sortorder);
    //print_liste_field_titre($langs->trans('LastLogin'),$_SERVER['PHP_SELF'],'t.lastlogin','',$param,'align="center"',$sortfield,$sortorder);
    print_liste_field_titre($langs->trans('DateLastLogin'),$_SERVER['PHP_SELF'],'t.date_lastlogin','',$param,'align="center"',$sortfield,$sortorder);
    print_liste_field_titre($langs->trans('Revenue'),$_SERVER['PHP_SELF'],'','',$param,' align="right"',$sortfield,$sortorder);
    print_liste_field_titre($langs->trans('CustStatus'),$_SERVER['PHP_SELF'],'c.status','',$param,'align="right"',$sortfield,$sortorder);
    print '</tr>';

    print '<tr class="liste_titre">';
    print '<td><input type="text" name="search_instance" size="4" value="'.$search_instance.'"></td>';
    print '<td><input type="text" name="search_organization" size="4" value="'.$search_organization.'"></td>';
    print '<td><input type="text" name="search_email" size="4" value="'.$search_email.'"></td>';
    print '<td><input type="text" name="search_plan" size="4" value="'.$search_plan.'"></td>';
    print '<td><input type="text" name="search_partner" size="4" value="'.$search_partner.'"></td>';
    //print '<td><input type="text" name="search_source" size="4" value="'.$search_source.'"></td>';
    print '<td></td>';
    print '<td></td>';
    print '<td></td>';
    print '<td></td>';
    //print '<td align="center"><input type="text" name="search_lastlogin" size="4" value="'.$search_lastlogin.'"></td>';
    print '<td></td>';
    print '<td></td>';
    print '<td align="right">';
    print $form->selectarray('search_status', $arraystatus, $search_status, 1);
    print '<input type="image" class="liste_titre" name="button_search" src="'.DOL_URL_ROOT.'/theme/'.$conf->theme.'/img/search.png"  value="'.dol_escape_htmltag($langs->trans("Search")).'" title="'.dol_escape_htmltag($langs->trans("Search")).'">';
    print '</td>';
    print '</tr>';


    $i = 0;
    if ($num)
    {
        while ($i < min($num,$conf->liste_limit))
        {
            $obj = $db2->fetch_object($resql);
            if ($obj)
            {
                $price=($obj->price_instance * ($obj->plan_meter_id == 1 ? $obj->nbofusers : 1)) + (max(0,($obj->nbofusers - $obj->min_threshold)) * $obj->price_user);
            	//var_dump($obj->status);exit;
                $totalinstances++;
				$instance=preg_replace('/\.on\.dolicloud\.com$/', '', $obj->instance);

                $dolicloudcustomerstaticnew->status = $obj->status;
                $dolicloudcustomerstaticnew->instance_status = $obj->instance_status;
                $dolicloudcustomerstaticnew->payment_status = $obj->payment_status;
                $dolicloudcustomerstaticnew->subscription_status = $obj->subscription_status;	// This is not used (info only)
                $status=$dolicloudcustomerstaticnew->getLibStatut(1,$form);

                $var=!$var;
                // You can use here results
                print '<tr '.$bc[$var].'><td align="left" nowrap="nowrap">';
                //print $dolicloudcustomerstaticnew->status.'/'.$dolicloudcustomerstaticnew->instance_status.'/'.$dolicloudcustomerstaticnew->payment_status.'=>'.$status.'<br>';
                $dolicloudcustomerstaticnew->id=$obj->id;
                $dolicloudcustomerstaticnew->ref=$instance;
                $dolicloudcustomerstaticnew->status=$obj->status;
                print $dolicloudcustomerstaticnew->getNomUrl(1,'',0,'_new');
                print '</td><td>';
                print $obj->organization;
                print '</td><td>';
                print $obj->email;
                print '</td><td>';
                if (empty($obj->planid)) print 'ERROR Bad value for Plan';
              	else print $obj->plan;
                print '</td><td>';
                print $obj->partner;
                print '</td><td>';
                //print $obj->source;
                //print '</td><td>';
                print dol_print_date($db->jdate($obj->date_registration),'dayhour','tzuser');
                print '</td><td>';
                print dol_print_date($db->jdate($obj->past_due_start),'day','tzuser');
                print '</td><td>';
                print dol_print_date($db->jdate($obj->lastcheck), 'dayhour', 'tzuser');
                print '</td><td align="right">';
                print $obj->nbofusers;
                //print '</td><td align="center">';
                //print $obj->lastlogin;
	            print '</td><td align="center">';
                print ($obj->date_lastlogin?dol_print_date($db->jdate($obj->date_lastlogin),'dayhour','tzuser'):'');
                print '</td><td align="right">';

                if ($status != 'ACTIVE' && $status != 'OK' && $status != 'PAID')
                {
                	print '';
                }
                else
              {
                	if (empty($obj->nbofusers)) print $langs->trans("NeedRefresh");
                	else print price($price);
                	$totalinstancespaying++;
                	$total+=$price;
                }
                print '</td>';
                print '<td align="right">';
                print $dolicloudcustomerstaticnew->getLibStatut(5,$form);
                print '</td>';
                print '</tr>';
            }
            $i++;
        }
    }

    print '</table>';

    print '</form>';

}
else
{
    $error++;
    dol_print_error($db2);
}


print '<br>';

// End of page
llxFooter();
$db->close();
