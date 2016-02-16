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
 *   	\file       statistics/index.php
 *		\ingroup    statistics
 *		\brief      Module statistics
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
if (! $res && file_exists("../../../../main.inc.php")) $res=@include("../../../../main.inc.php");
if (! $res && @file_exists("../../../../../main.inc.php")) $res=@include("../../../../../main.inc.php");
if (! $res && preg_match('/\/(?:custom|nltechno)([^\/]*)\//',$_SERVER["PHP_SELF"],$reg)) $res=@include("../../../dolibarr".$reg[1]."/htdocs/main.inc.php"); // Used on dev env only
if (! $res) die("Include of main fails");
dol_include_once("/statistics/core/modules/statistic/modules_statistic.php");
//require_once(DOL_DOCUMENT_ROOT."/includes/modules/propale/modules_propale.php");

// Change this following line to use the correct relative path from htdocs (do not remove DOL_DOCUMENT_ROOT)
//require_once(DOL_DOCUMENT_ROOT."/statistic/class/skeleton_class.class.php");

// Load traductions files requiredby by page
$langs->load("companies");
$langs->load("other");

// Get parameters
$myparam = isset($_GET["myparam"])?$_GET["myparam"]:'';

// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}



/*
 * Actions
 */

if ($_REQUEST["action"] == 'add')
{
	$myobject=new Skeleton_class($db);
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


/*
 * View
 */

llxHeader('','Statistic','');

$form=new Form($db);

$h = 0;
$head = array();

$head[$h][0] = DOL_URL_ROOT.'/statistic/stock.php';
$head[$h][1] = $langs->trans("Stock");
$head[$h][2] = 'stock';
$h++;

$title=$langs->trans("Statistics");

dol_fiche_head($head, 'stock', $title, 0, 'accounting');

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
for($i = 0;$i<=40;$i++)
{
	print '<option value="'.($i+2010).'">'.(2010+$i).'</option>';
}
print '</select>';
print '<input style="margin-left:20px" type="submit" value="G&eacute;n&eacute;rer"  name="btGenerate" class="button">';

print '</form>';

dol_fiche_end();

llxFooter();

$db->close();

