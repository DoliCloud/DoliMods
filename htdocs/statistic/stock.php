<?php
/* Copyright (C) 2007-2010 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) ---Put here your own copyright and developer email---
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
 *   	\file       htdocs/statistics/stock.php
 *		\ingroup    statistics
 *		\brief      File of statistics module
 */

// Load Dolibarr environment
$res=0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (! $res && ! empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) $res=@include str_replace("..", "", $_SERVER["CONTEXT_DOCUMENT_ROOT"])."/main.inc.php";
// Try main.inc.php into web root detected using web root caluclated from SCRIPT_FILENAME
$tmp=empty($_SERVER['SCRIPT_FILENAME'])?'':$_SERVER['SCRIPT_FILENAME'];$tmp2=realpath(__FILE__); $i=strlen($tmp)-1; $j=strlen($tmp2)-1;
while ($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i]==$tmp2[$j]) { $i--; $j--; }
if (! $res && $i > 0 && file_exists(substr($tmp, 0, ($i+1))."/main.inc.php")) $res=@include substr($tmp, 0, ($i+1))."/main.inc.php";
if (! $res && $i > 0 && file_exists(dirname(substr($tmp, 0, ($i+1)))."/main.inc.php")) $res=@include dirname(substr($tmp, 0, ($i+1)))."/main.inc.php";
// Try main.inc.php using relative path
if (! $res && file_exists("../main.inc.php")) $res=@include "../main.inc.php";
if (! $res && file_exists("../../main.inc.php")) $res=@include "../../main.inc.php";
if (! $res && file_exists("../../../main.inc.php")) $res=@include "../../../main.inc.php";
if (! $res) die("Include of main fails");

dol_include_once("/statistic/core/modules/statistic/modules_statistic.php");
//require_once(DOL_DOCUMENT_ROOT."/includes/modules/propale/modules_propale.php");

// Change this following line to use the correct relative path from htdocs (do not remove DOL_DOCUMENT_ROOT)
//require_once(DOL_DOCUMENT_ROOT."/statistic/class/skeleton_class.class.php");

// Load traductions files requiredby by page
$langs->load("companies");
$langs->load("other");

// Get parameters
$myparam = isset($_GET["myparam"])?$_GET["myparam"]:'';

// Protection if external user
if ($user->societe_id > 0) {
	//accessforbidden();
}



/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/

if ($_REQUEST["action"] == 'add') {
	$myobject=new Skeleton_class($db);
	$myobject->prop1=$_POST["field1"];
	$myobject->prop2=$_POST["field2"];
	$result=$myobject->create($user);
	if ($result > 0) {
		// Creation OK
	}
	{
		// Creation KO
		$mesg=$myobject->error;
	}
}

if ($_POST['btGenerate']) {
	if ($_POST['month'] !=-1 && $_POST['year']!=-1) {
		if (strlen($_POST['month']) == 1) {
			$_POST['month'] = "0".$_POST['month'];
		}
		$date = $_POST['year']."-".$_POST['month'];
		statistic_pdf_create($db, $propal->id, $date, "test", $outputlangs);

		 header('Content-Type: application/octet-stream');
		header('Content-Length: '. $poids);
		header('Content-disposition: attachment; filename=stat_stock_'.$date.'.pdf');
		header('Pragma: no-cache');
		header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
		header('Expires: 0');
		readfile("../../documents/statistic/stat_stock_".$date.".pdf");
	}
}

/***************************************************
* PAGE
*
* Put here all code to build page
****************************************************/

llxHeader('', 'Statistic', '');

$form=new Form($db);

$h = 0;
$head = array();

$head[$h][0] = DOL_URL_ROOT.'/statistic/stock.php';
$head[$h][1] = $langs->trans("Stock");
$head[$h][2] = 'stock';
$h++;

$title=$langs->trans("Statistics");

dol_fiche_head($head, 'stock', $title, 0, 'statistics');

print '<form method="post" action="stock.php">';


print '<select name="month" class="flat">';
print '<option selected="true" value="-1">-'.$langs->trans("Month").'-</option>';
print '<option value="1">'.$langs->trans("January").'</option>';
print '<option value="2">'.$langs->trans("February").'</option>';
print '<option value="3">'.$langs->trans("March").'</option>';
print '<option value="4">'.$langs->trans("April").'</option>';
print '<option value="5">'.$langs->trans("May").'</option>';
print '<option value="6">'.$langs->trans("June").'</option>';
print '<option value="7">'.$langs->trans("July").'</option>';
print '<option value="8">'.$langs->trans("August").'</option>';
print '<option value="9">'.$langs->trans("September").'</option>';
print '<option value="10">'.$langs->trans("October").'</option>';
print '<option value="11">'.$langs->trans("November").'</option>';
print '<option value="12">'.$langs->trans("December").'</option>';
print '</select>';

print '<select style="margin-left:10px" name="year" class="flat">';
print '<option value="-1">-'.$langs->trans("Year").'-</option>';
for ($i = 0;$i<=40;$i++) {
	print '<option value="'.($i+2010).'">'.(2010+$i).'</option>';
}
print '</select>';
print '<input style="margin-left:20px" type="submit" value="G&eacute;n&eacute;rer" name="btGenerate" class="button">';

print '</form>';

print '</div>';


// End of page
$db->close();
llxFooter();
