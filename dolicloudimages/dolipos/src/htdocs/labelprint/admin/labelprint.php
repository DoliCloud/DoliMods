<?php
/* Copyright (C) 2012      Juanjo Menent        <jmenent@2byte.es>
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
 *	\file       htdocs/labelprint/admin/labelprint.php
 *	\ingroup    products
 *	\brief      labels module setup page
 */

$res=@include("../../main.inc.php");					// For root directory
if (! $res) $res=@include("../../../main.inc.php");		// For "custom" directory

require_once(DOL_DOCUMENT_ROOT."/core/lib/admin.lib.php");
dol_include_once("/labelprint/lib/labelprint.lib.php");

$langs->load("admin");
$langs->load("labelprint@labelprint");

$action=GETPOST('action','alpha');
$value=GETPOST('value','int');

if (!$user->admin) accessforbidden();

/*
 * Actions
 */
if (GETPOST("save"))
{
	$db->begin();

	$res=0;

	$res+=dolibarr_set_const($db,'LAB_COMP',trim(GETPOST("labComp")),'chaine',0,'',$conf->entity);
	$res+=dolibarr_set_const($db,'LAB_PROD_LABEL',trim(GETPOST("labProdLabel")),'chaine',0,'',$conf->entity);
	$res+=dolibarr_set_const($db,'LAB_FREE_TEXT',trim(GETPOST("labFreeText")),'chaine',0,'',$conf->entity);
	$res+=dolibarr_set_const($db,'LAB_WEIGHT',trim(GETPOST("labWeight")),'chaine',0,'',$conf->entity);
	$res+=dolibarr_set_const($db,'LAB_LENGTH',trim(GETPOST("labLength")),'chaine',0,'',$conf->entity);
	$res+=dolibarr_set_const($db,'LAB_AREA',trim(GETPOST("labArea")),'chaine',0,'',$conf->entity);
	$res+=dolibarr_set_const($db,'LAB_VOLUME',trim(GETPOST("labVolume")),'chaine',0,'',$conf->entity);
	$res+=dolibarr_set_const($db,'LAB_COUNTRY',trim(GETPOST("labCountry")),'chaine',0,'',$conf->entity);
	$res+=dolibarr_set_const($db,'LAB_START',0,'chaine',0,'',$conf->entity);
	
	if ($res >= 9)
	{
		$db->commit();
		$mesg = '<font class="ok">'.$langs->trans("LabSetupSaved")."</font>";
	}
	else
	{
		$db->rollback();
		header("Location: ".$_SERVER["PHP_SELF"]);
		exit;
	}
}


//Activate Labels
if ($action == 'setlabel')
{
	$status = GETPOST('status','int');

	$labelid="MAIN_MODULE_LABELPRINT_TABS_".$value;
	
	if($status==1)
	{
		
		switch ($value)
		{
			case (0):
				$menutab="supplier_invoice:+labelprint:Labels:@labelprint::/labelprint/invoice_supplier.php?id=__ID__";
				break;
			case (1):
				$menutab="supplier_order:+labelprint:Labels:@labelprint::/labelprint/order_supplier.php?id=__ID__";
				break;
			case (2):
				$menutab="product:+labelprint:Labels:@labelprint::/labelprint/product.php?id=__ID__";
				break;
			case (3):
				$menutab=1;
				break;			
		}
		
		
		if (dolibarr_set_const($db, $labelid,$menutab,'chaine',0,'',$conf->entity) > 0)
		{
			Header("Location: ".$_SERVER["PHP_SELF"]);
			exit;
		}
		else
		{
			dol_print_error($db);
		}
	}
	else 
	{
		if (dolibarr_del_const($db, $labelid))	
		{
			Header("Location: ".$_SERVER["PHP_SELF"]);
			exit;
		}
		else
		{
			dol_print_error($db);
		}
	}
	
}


//Type of Labels
if ($action == 'settypelabel')
{
	$status = GETPOST('status','int');

	$labelid="MAIN_MODULE_LABELPRINT_LABELS_".$value;
	
	if($status==1)
	{
		
		switch ($value)
		{
			case (0):
				dolibarr_del_const($db, "MAIN_MODULE_LABELPRINT_LABELS_1");
				break;
			case (1):
				dolibarr_del_const($db, "MAIN_MODULE_LABELPRINT_LABELS_0");
				break;
			
		}
		
		
		if (dolibarr_set_const($db, $labelid,1,'chaine',0,'',$conf->entity) > 0)
		{
			Header("Location: ".$_SERVER["PHP_SELF"]);
			exit;
		}
		else
		{
			dol_print_error($db);
		}
	}
	else 
	{
		if (dolibarr_del_const($db, $labelid))	
		{
			Header("Location: ".$_SERVER["PHP_SELF"]);
			exit;
		}
		else
		{
			dol_print_error($db);
		}
	}
	
}


/*
 * 	View
 */

clearstatcache();

// read const
$labcomp = dolibarr_get_const($db,"LAB_COMP",1);
$labprodlabel = dolibarr_get_const($db,"LAB_PROD_LABEL",1);
$labweight = dolibarr_get_const($db,"LAB_WEIGHT",1);
$lablength = dolibarr_get_const($db,"LAB_LENGTH",1);
$labarea = dolibarr_get_const($db,"LAB_AREA",1);
$labvolume = dolibarr_get_const($db,"LAB_VOLUME",1);
$labcountry = dolibarr_get_const($db,"LAB_COUNTRY",1);

$form=new Form($db);

$helpurl='EN:Module_Labels|FR:Module_Labels_FR|ES:M&oacute;dulo_Labels';
llxHeader('',$langs->trans("LabelPrintSetup"),$helpurl);
dol_include_once('/labelprint/class/utils.class.php');

$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php">'.$langs->trans("BackToModuleList").'</a>';
print_fiche_titre($langs->trans("LabelPrintSetup"),$linkback,'setup');


$head = labels_admin_prepare_head(null);

dol_fiche_head($head, 'general', $langs->trans("Labels"), 0, 'barcode');

dol_htmloutput_mesg($mesg);

//Show in
print_titre($langs->trans("ShowLabelsIn"));

print '<table class="noborder" width="100%">';
print '<tr class="liste_titre">';
print '<td>'.$langs->trans("Name").'</td>';
print '<td>'.$langs->trans("Description").'</td>';
print '<td align="center">'.$langs->trans("Use").'</td>';
print "</tr>\n";

$profid[0][0]=$langs->trans("InvoiceSuppliers");
$profid[0][1]=$langs->trans('InvoiceSuppliersDesc');
$profid[1][0]=$langs->trans("OrderSuppliers");
$profid[1][1]=$langs->trans('OrderSuppliersDesc');
$profid[2][0]=$langs->trans("Products");
$profid[2][1]=$langs->trans('ProductsDesc');
$profid[3][0]=$langs->trans("MenuProducts");
$profid[3][1]=$langs->trans('MenuProductsDesc');

$var = true;
$i=0;

$nbofloop=count($profid);
while ($i < $nbofloop)
{
	$var = !$var;

	print '<tr '.$bc[$var].'>';
	print '<td>'.$profid[$i][0]."</td><td>\n";
	print $profid[$i][1];
	print '</td>';

	switch($i)
	{
        case 0:
        	$verif=(!$conf->global->MAIN_MODULE_LABELPRINT_TABS_0?false:true);
        	break;
        case 1:
        	$verif=(!$conf->global->MAIN_MODULE_LABELPRINT_TABS_1?false:true);
        	break;
        case 2:
        	$verif=(!$conf->global->MAIN_MODULE_LABELPRINT_TABS_2?false:true);
        	break;
        case 3:
        	$verif=(!$conf->global->MAIN_MODULE_LABELPRINT_TABS_3?false:true);
        	break;
	}

	if ($verif)
	{
		print '<td align="center"><a href="'.$_SERVER['PHP_SELF'].'?action=setlabel&amp;value='.($i).'&amp;status=0">';
		print img_picto($langs->trans("Activated"),'switch_on');
		print '</a></td>';
	}
	else
	{
		print '<td align="center"><a href="'.$_SERVER['PHP_SELF'].'?action=setlabel&amp;value='.($i).'&amp;status=1">';
		print img_picto($langs->trans("Disabled"),'switch_off');
		print '</a></td>';
	}
	print "</tr>\n";
	$i++;
}

print "</table><br>\n";

//Show in
print_titre($langs->trans("TypeLabels"));
print '<table class="noborder" width="100%">';
print '<tr class="liste_titre">';
print '<td>'.$langs->trans("Name").'</td>';
print '<td>'.$langs->trans("Description").'</td>';
print '<td align="center">'.$langs->trans("Use").'</td>';
print "</tr>\n";

$labels[0][0]=$langs->trans("70x36");
$labels[0][1]=$langs->trans('7036Desc');
$labels[1][0]=$langs->trans("70x37");
$labels[1][1]=$langs->trans('7037Desc');

$var = true;
$i=0;

$nbofloop=count($labels);
while ($i < $nbofloop)
{
	$var = !$var;

	print '<tr '.$bc[$var].'>';
	print '<td>'.$labels[$i][0]."</td><td>\n";
	print $labels[$i][1];
	print '</td>';

	switch($i)
	{
        case 0:
        	$verif=(!$conf->global->MAIN_MODULE_LABELPRINT_LABELS_0?false:true);
        	break;
        case 1:
        	$verif=(!$conf->global->MAIN_MODULE_LABELPRINT_LABELS_1?false:true);
        	break;
	}

	if ($verif)
	{
		print '<td align="center"><a href="'.$_SERVER['PHP_SELF'].'?action=settypelabel&amp;value='.($i).'&amp;status=0">';
		print img_picto($langs->trans("Activated"),'switch_on');
		print '</a></td>';
	}
	else
	{
		print '<td align="center"><a href="'.$_SERVER['PHP_SELF'].'?action=settypelabel&amp;value='.($i).'&amp;status=1">';
		print img_picto($langs->trans("Disabled"),'switch_off');
		print '</a></td>';
	}
	print "</tr>\n";
	$i++;
}
print "</table><br>\n";
//fmarcet
print '<form name="catalogconfig" action="'.$_SERVER["PHP_SELF"].'" method="post">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';

/*
 *  General Optiones
*/
$html=new Form($db);
print_titre($langs->trans("ShowOptions"));
print '<table class="noborder" width="100%">';
print '<tr class="liste_titre">';
print '<td>'.$langs->trans("Parameter")." ".$langs->trans("max2").'</td>';
print '<td align="center" width="60">'.$langs->trans("Value").'</td>';
print "</tr>\n";
$var=true;

// Show Company Name
$var=!$var;
print "<tr ".$bc[$var].">";
print "<td>".$langs->trans("ShowCompanyName")."</td>";
print '<td>';
print $html->selectyesno("labComp",$labcomp,1);
print '</td>';
print "</tr>";

// Show prod label
$var=!$var;
print "<tr ".$bc[$var].">";
print "<td>".$langs->trans("ShowProdLabel")."</td>";
print '<td>';
print $html->selectyesno("labProdLabel",$labprodlabel,1);
print '</td>';
print "</tr>";

$var=! $var;
print '<tr '.$bc[$var].'><td colspan=2>';
print $langs->trans("FreeText").'<br>';
print '<textarea name="labFreeText" class="flat" cols="120">'.$conf->global->LAB_FREE_TEXT.'</textarea>';
print '</td></tr>';

print '</table>';

print_titre($langs->trans("OtherShowOptions"));
print '<table class="noborder" width="100%">';
print '<tr class="liste_titre">';
print '<td>'.$langs->trans("Parameter")." ".$langs->trans("max1").'</td>';
print '<td align="center" width="60">'.$langs->trans("Value").'</td>';
print "</tr>\n";
$var=true;

// Show weight
$var=!$var;
print "<tr ".$bc[$var].">";
print "<td>".$langs->trans("ShowWeight")."</td>";
print '<td>';
print $html->selectyesno("labWeight",$labweight,1);
print '</td>';
print "</tr>";

// Show length
$var=!$var;
print "<tr ".$bc[$var].">";
print "<td>".$langs->trans("ShowLength")."</td>";
print '<td>';
print $html->selectyesno("labLength",$lablength,1);
print '</td>';
print "</tr>";

// Show Area
$var=!$var;
print "<tr ".$bc[$var].">";
print "<td>".$langs->trans("ShowArea")."</td>";
print '<td>';
print $html->selectyesno("labArea",$labarea,1);
print '</td>';
print "</tr>";

// Show Volume
$var=!$var;
print "<tr ".$bc[$var].">";
print "<td>".$langs->trans("ShowVolume")."</td>";
print '<td>';
print $html->selectyesno("labVolume",$labvolume,1);
print '</td>';
print "</tr>";

// Show Country
$var=!$var;
print "<tr ".$bc[$var].">";
print "<td>".$langs->trans("ShowCountry")."</td>";
print '<td>';
print $html->selectyesno("labCountry",$labcountry,1);
print '</td>';
print "</tr>";

print '</table>';

print '<br><center>';
print '<input type="submit" name="save" class="button" value="'.$langs->trans("Save").'">';
print "</center>";
print "</form>\n";


dol_fiche_end();

$db->close();

llxFooter();
?>