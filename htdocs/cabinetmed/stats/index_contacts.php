<?php
/* Copyright (C) 2003-2006 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (c) 2004-2011 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 * or see http://www.gnu.org/
 */

/**
 *  \file       htdocs/cabinetmed/stats/index.php
 *  \ingroup    cabinetmed
 *  \brief      Page of patient outcomes statistics
 */


$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");
if (! $res && file_exists("../../../../main.inc.php")) $res=@include("../../../../main.inc.php");
if (! $res && preg_match('/\/nltechno([^\/]*)\//',$_SERVER["PHP_SELF"],$reg)) $res=@include("../../../../dolibarr".$reg[1]."/htdocs/main.inc.php"); // Used on dev env only
if (! $res) die("Include of main fails");
require_once(DOL_DOCUMENT_ROOT."/core/class/dolgraph.class.php");
require_once(DOL_DOCUMENT_ROOT."/contact/class/contact.class.php");
dol_include_once("/cabinetmed/lib/cabinetmed.lib.php");
dol_include_once("/cabinetmed/class/cabinetmedcons.class.php");
dol_include_once("/cabinetmed/class/cabinetmedstats.class.php");

$WIDTH=500;
$HEIGHT=200;

$userid=GETPOST('userid','int'); if ($userid < 0) $userid=0;
$socid=GETPOST('socid','int'); if ($socid < 0) $socid=0;
// Security check
if ($user->societe_id > 0)
{
    $action = '';
    $socid = $user->societe_id;
}

$year = strftime("%Y", time());
$startyear=$year-2;
$endyear=$year;

$mode=GETPOST("mode")?GETPOST("mode"):'customer';

$sortfield = GETPOST("sortfield",'alpha');
$sortorder = GETPOST("sortorder",'alpha');
$page = GETPOST("page",'int');
if ($page == -1) {
    $page = 0;
}
$offset = $conf->liste_limit * $page;
$pageprev = $page - 1;
$pagenext = $page + 1;
if (! $sortfield) $sortfield='nb';
if (! $sortorder) $sortorder='DESC';
$limit = $conf->liste_limit;

if (empty($conf->cabinetmed->enabled)) accessforbidden();


/*
 * View
 */

$langs->load("cabinetmed@cabinetmed");

$form=new Form($db);

llxHeader();

$title=$langs->trans("Statistics");
$dir=$conf->cabinetmed->dir_temp;

print_fiche_titre($title, $mesg);

dol_mkdir($dir);


/*
$stats = new CabinetMedStats($db, $socid, $mode, ($userid>0?$userid:0));


// Build graphic number of object
// $data = array(array('Lib',val1,val2,val3),...)
$data = $stats->getNbByMonthWithPrevYear($endyear,$startyear);
//var_dump($data);

$filenamenb = $dir."/outcomesnbinyear-".$year.".png";
$fileurlnb = DOL_URL_ROOT.'/viewimage.php?modulepart=cabinetmed_temp&amp;file=outcomesnbinyear-'.$year.'.png';

$px1 = new DolGraph();
$mesg = $px1->isGraphKo();
if (! $mesg)
{
	$px1->SetData($data);
	$px1->SetPrecisionY(0);
	$i=$startyear;
	while ($i <= $endyear)
	{
		$legend[]=$i;
		$i++;
	}
	$px1->SetLegend($legend);
	$px1->SetMaxValue($px1->GetCeilMaxValue());
	$px1->SetWidth($WIDTH);
	$px1->SetHeight($HEIGHT);
	$px1->SetYLabel($langs->trans("Number"));
	$px1->SetShading(3);
	$px1->SetHorizTickIncrement(1);
	$px1->SetPrecisionY(0);
	$px1->mode='depth';
	$px1->SetTitle($langs->trans("NumberByMonth"));

	$px1->draw($filenamenb,$fileurlnb);
}

// Build graphic amount of object
$data = $stats->getAmountByMonthWithPrevYear($endyear,$startyear);
//var_dump($data);
// $data = array(array('Lib',val1,val2,val3),...)

$filenameamount = $dir."/outcomesamountinyear-".$year.".png";
$fileurlamount = DOL_URL_ROOT.'/viewimage.php?modulepart=cabinetmed_temp&amp;file=outcomesamountinyear-'.$year.'.png';

$px2 = new DolGraph();
$mesg = $px2->isGraphKo();
if (! $mesg)
{
	$px2->SetData($data);
	$i=$startyear;
	while ($i <= $endyear)
	{
		$legend[]=$i;
		$i++;
	}
	$px2->SetLegend($legend);
	$px2->SetMaxValue($px2->GetCeilMaxValue());
	$px2->SetMinValue(min(0,$px2->GetFloorMinValue()));
	$px2->SetWidth($WIDTH);
	$px2->SetHeight($HEIGHT);
	$px2->SetYLabel($langs->trans("Amount"));
	$px2->SetShading(3);
	$px2->SetHorizTickIncrement(1);
	$px2->SetPrecisionY(0);
	$px2->mode='depth';
	$px2->SetTitle($langs->trans("AmountByMonth"));

	$px2->draw($filenameamount,$fileurlamount);
}

*/

$head = contact_patient_stats_prepare_head(null);

dol_fiche_head($head, 'statscontacts', $langs->trans("Contacts"), 0, 'contact');

print '<table class="notopnoleftnopadd" width="100%"><tr>';
print '<td valign="top">';

print $langs->trans("PatientsPerContacts").'<br>';

print '<br>';

$param='&userid='.$user->id;

print '<table class="noborder" width="100%">';
print '<tr class="liste_titre">';
print_liste_field_titre($langs->trans('Contact'),$_SERVER['PHP_SELF'],'c.name','',$param,'',$sortfield,$sortorder);
print_liste_field_titre($langs->trans('NumberOfPatient'),$_SERVER['PHP_SELF'],'nb','',$param,'align="right"',$sortfield,$sortorder);
print_liste_field_titre($langs->trans('AverageOld'),$_SERVER['PHP_SELF'],'averageold','',$param,'align="right"',$sortfield,$sortorder);
print '</tr>';


$sql = "SELECT";
$sql.= " COUNT(s.rowid) as nb,";
$sql.= " AVG(DATEDIFF(NOW(), STR_TO_DATE(s.ape, '%d/%m/%Y'))) as averageold,";
$sql.= " c.rowid,";
$sql.= " c.lastname as lastname,";
$sql.= " c.firstname as firstname";
$sql.= " FROM (".MAIN_DB_PREFIX."societe as s,";
$sql.= " ".MAIN_DB_PREFIX."element_contact as ec,";
$sql.= " ".MAIN_DB_PREFIX."c_type_contact as tc,";
$sql.= " ".MAIN_DB_PREFIX."socpeople as c)";
$sql.= " WHERE ec.fk_socpeople = c.rowid";
$sql.= " AND ec.element_id = s.rowid";
$sql.= " AND ec.fk_c_type_contact = tc.rowid";
$sql.= " AND tc.element = 'societe'";
$sql.= " GROUP BY c.rowid, c.lastname, c.firstname";
$sql.= " ORDER BY ".$sortfield." ".$sortorder.", s.rowid DESC";

//print $sql;
$resql=$db->query($sql);
if ($resql)
{
    $num=$db->num_rows($resql);

    $contactstatic=new Contact($db);

    $i=0; $var=false;
    while ($i < $num)
    {
        $obj=$db->fetch_object($resql);

        if ($obj)
        {
            $contactstatic->id=$obj->rowid;
            $contactstatic->lastname=$obj->lastname;
            $contactstatic->firstname=$obj->firstname;

            $var=!$var;
            print '<tr '.$bc[$var].'>';
        	print '<td>'.$contactstatic->getNomUrl(1).'</td>';
        	print '<td align="right">'.round($obj->nb).'</td>';
        	print '<td align="right">';
        	$ageyear=convertSecondToTime($obj->averageold*24*3600,'year')-1970;
        	$agemonth=convertSecondToTime($obj->averageold*24*3600,'month')-1;
        	if ($ageyear >= 2) print $ageyear.' '.$langs->trans("DurationYears");
        	else if ($agemonth >= 2) print $agemonth.' '.$langs->trans("DurationMonths");
        	else print $agemonth.' '.$langs->trans("DurationMonth");
        	print '</td>';
        	print '</tr>';
        }

    	$i++;
    }
}
else
{
    dol_print_error($db);
}

print '</table>';


print '</td>';
print '</tr></table>';

dol_fiche_end();


llxFooter();

$db->close();
?>
