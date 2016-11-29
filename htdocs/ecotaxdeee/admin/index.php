<?php
/* Copyright (C) 2013 Laurent Destailleur  <eldy@users.sourceforge.net>
 */

/**
 *	    \file       htdocs/ecotaxdeee/admin/index.php
 *      \ingroup    ecotaxdee
 *      \brief      Setup page for ecotaxdeee module
 */

$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");
if (! $res && file_exists("../../../../main.inc.php")) $res=@include("../../../../main.inc.php");
if (! $res && file_exists("../../../../../main.inc.php")) $res=@include("../../../../../main.inc.php");
if (! $res && preg_match('/\/nltechno([^\/]*)\//',$_SERVER["PHP_SELF"],$reg)) $res=@include("../../../../dolibarr".$reg[1]."/htdocs/main.inc.php"); // Used on dev env only
if (! $res) die("Include of main fails");
require_once(DOL_DOCUMENT_ROOT."/core/lib/admin.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/date.lib.php");
require_once(DOL_DOCUMENT_ROOT.'/core/class/html.formadmin.class.php');
require_once(DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php');
require_once(DOL_DOCUMENT_ROOT.'/core/class/doleditor.class.php');
dol_include_once("/ecotaxdeee/lib/ecotaxdeee.lib.php");

if (!$user->admin) accessforbidden();

$langs->load("ecotaxdeee@ecotaxdeee");
$langs->load("admin");
$langs->load("other");
$langs->load("orders");
$langs->load("bills");
$langs->load("propal");

$def = array();
$action=GETPOST("action");


/*
 * Actions
 */
if ($action == 'save')
{
    $db->begin();

    $res=dolibarr_set_const($db,'ECOTAXDEEE_USE_ON_CUSTOMER_ORDER',trim($_POST["ECOTAXDEEE_USE_ON_CUSTOMER_ORDER"]),'chaine',0,'',$conf->entity);
    $res=dolibarr_set_const($db,'ECOTAXDEEE_USE_ON_PROPOSAL',trim($_POST["ECOTAXDEEE_USE_ON_PROPOSAL"]),'chaine',0,'',$conf->entity);
    $res=dolibarr_set_const($db,'ECOTAXDEEE_USE_ON_CUSTOMER_INVOICE',trim($_POST["ECOTAXDEEE_USE_ON_CUSTOMER_INVOICE"]),'chaine',0,'',$conf->entity);
    $res=dolibarr_set_const($db,'ECOTAXDEEE_LABEL_LINE',trim($_POST["ECOTAXDEEE_LABEL_LINE"]),'chaine',0,'',$conf->entity);
    $res=dolibarr_set_const($db,'ECOTAXDEEE_DOC_FOOTER',trim($_POST["ECOTAXDEEE_DOC_FOOTER"]),'chaine',0,'',$conf->entity);

    $product_wee=$_POST["WEEE_PRODUCT_ID"];
    if ($product_wee < 0) $product_wee='';
    $res=dolibarr_set_const($db,'WEEE_PRODUCT_ID',$product_wee,'chaine',0,'',$conf->entity);

    if (! $error)
    {
        $db->commit();
        setEventMessage($langs->trans("SetupSaved"));
    }
    else
    {
        $db->rollback();
        setEventMessage($langs->trans("Error"));
    }
}



/*
 * View
 */

$form=new Form($db);
$formadmin=new FormAdmin($db);
$formother=new FormOther($db);

$help_url='EN:Module_EcoTaxDeee_En|FR:Module_EcoTaxDeee|ES:Modulo_EcoTaxDeee';
//$arrayofjs=array('/includes/jquery/plugins/colorpicker/jquery.colorpicker.js');
//$arrayofcss=array('/includes/jquery/plugins/colorpicker/jquery.colorpicker.css');
$arrayofjs=array();
$arrayofcss=array();
llxHeader('',$langs->trans("Setup"),$help_url,'',0,0,$arrayofjs,$arrayofcss);

$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php">'.$langs->trans("BackToModuleList").'</a>';
print_fiche_titre($langs->trans("EcoTaxDeeSetup"),$linkback,'setup');
print '<br>';


$head=ecotaxdeee_prepare_head();

print '<form name="ecotaxdeeeconfig" action="'.$_SERVER["PHP_SELF"].'" method="post">';
print '<input type="hidden" name="action" value="save">';


dol_fiche_head($head, 'tabsetup', $langs->trans("EcoTaxDeee"));

$elements='';

$var=false;
print "<table class=\"noborder\" width=\"100%\">";

print "<tr class=\"liste_titre\">";
print '<td>'.$langs->trans("Parameter")."</td>";
print "<td>".$langs->trans("Value")."</td>";
print "</tr>";
// GETPOST("ECOTAXDEEE_USE_ON_CUSTOMER_ORDER")
$var=!$var;
print "<tr ".$bc[$var].">";
print "<td>".$langs->trans("ECOTAXDEEE_USE_ON_CUSTOMER_ORDER")."</td>";
print "<td>";
$selectedvalue=$conf->global->ECOTAXDEEE_USE_ON_CUSTOMER_ORDER;
print $form->selectyesno("ECOTAXDEEE_USE_ON_CUSTOMER_ORDER",$selectedvalue,1);
print "</td>";
print "</tr>";
// GETPOST("ECOTAXDEEE_USE_ON_PROPOSAL")
$var=!$var;
print "<tr ".$bc[$var].">";
print "<td>".$langs->trans("ECOTAXDEEE_USE_ON_PROPOSAL")."</td>";
print "<td>";
$selectedvalue=$conf->global->ECOTAXDEEE_USE_ON_PROPOSAL;
print $form->selectyesno("ECOTAXDEEE_USE_ON_PROPOSAL",$selectedvalue,1);
print "</td>";
print "</tr>";
// GETPOST("ECOTAXDEEE_USE_ON_CUSTOMER_INVOICE")
$var=!$var;
print "<tr ".$bc[$var].">";
print "<td>".$langs->trans("ECOTAXDEEE_USE_ON_CUSTOMER_INVOICE")."</td>";
print "<td>";
$selectedvalue=$conf->global->ECOTAXDEEE_USE_ON_CUSTOMER_INVOICE;
print $form->selectyesno("ECOTAXDEEE_USE_ON_CUSTOMER_INVOICE",$selectedvalue,1);
print "</td>";
print "</tr>";

// GETPOST("ECOTAXDEEE_LABEL_LINE")
$var=!$var;
print "<tr ".$bc[$var].">";
if (! empty($conf->produit->enabled) || ! empty($conf->service->enabled))
{
    print "<td>".$langs->trans("ECOTAXDEEE_PRODUCT_OR_LABEL_LINE")."</td>";
    print "<td>";
    print $form->select_produits($conf->global->WEEE_PRODUCT_ID, 'WEEE_PRODUCT_ID', '');
    print ' '.$langs->trans("OrLabelOfAFreeLine").' ';
}
else
{
    print "<td>".$langs->trans("ECOTAXDEEE_LABEL_LINE")."</td>";
    print "<td>";
}
$selectedvalue=(empty($conf->global->ECOTAXDEEE_LABEL_LINE)?'':$conf->global->ECOTAXDEEE_LABEL_LINE);
print '<input type="text" class="flat" name="ECOTAXDEEE_LABEL_LINE" value="'.$selectedvalue.'">';
// Add warning if category product does not exists
print "</td>";
print "</tr>";

// ECOTAXDEEE_DOC_FOOTER
$var=!$var;
print "<tr ".$bc[$var].">";
print "<td>".$langs->trans("ECOTAXDEEE_DOC_FOOTER")." (Dolibarr 3.6+)</td>";
print "<td>";
$selectedvalue=(empty($conf->global->ECOTAXDEEE_DOC_FOOTER)?'':$conf->global->ECOTAXDEEE_DOC_FOOTER);
$doleditor=new DolEditor("ECOTAXDEEE_DOC_FOOTER", $selectedvalue,'','300','dolibarr_details','In',1,1,1,ROWS_9,'90%');
$doleditor->Create(0,'');
print '<br>';
print $langs->trans("Example").":<br>\n";
print $langs->trans("EcoTaxDeeDocFooterExample");
// Add warning if category product does not exists
print "</td>";
print "</tr>";

print '</table>';
print '<br>';

/*
// Table of categories
print $langs->trans("AddPageWithWEEEUnitPrices").'<br>';

$var=false;
print "<table class=\"noborder\" width=\"100%\">";

print "<tr class=\"liste_titre\">";
print '<td>'.$langs->trans("Label")."</td>";
print "<td>".$langs->trans("UnitPrice")."</td>";
print "</tr>";
for ($i=0; $i < 8; $i++)
{
	$var=!$var;
	print "<tr ".$bc[$var].">";
	print '<td><input type="text" name="label'.$i.'" size="40"></td>';
	print '<td><input type="text" name="value'.$i.'" size="5"></td>';
	print "</tr>";
}
print '</table>';
print '<br>';
*/


dol_fiche_end();

print '<center>';
//print "<input type=\"submit\" name=\"test\" class=\"button\" value=\"".$langs->trans("TestConnection")."\">";
//print "&nbsp; &nbsp;";
print "<input type=\"submit\" name=\"save\" class=\"button\" value=\"".$langs->trans("Save")."\">";
print "</center>";

print "</form>\n";

$elements=array();
if (! empty($conf->global->ECOTAXDEEE_USE_ON_CUSTOMER_ORDER) && $conf->global->ECOTAXDEEE_USE_ON_CUSTOMER_ORDER != 'no') $elements[]=$langs->transnoentitiesnoconv("CustomersOrders");
if (! empty($conf->global->ECOTAXDEEE_USE_ON_PROPOSAL) && $conf->global->ECOTAXDEEE_USE_ON_PROPOSAL != 'no') $elements[]=$langs->transnoentitiesnoconv("Proposals");
if (! empty($conf->global->ECOTAXDEEE_USE_ON_CUSTOMER_INVOICE) && $conf->global->ECOTAXDEEE_USE_ON_CUSTOMER_INVOICE != 'no') $elements[]=$langs->transnoentitiesnoconv("BillsCustomers");
if (count($elements))
{
	/*if (versioncompare(versiondolibarrarray(),array(3,6,-3)) >= 999)	// >= 0 if we are 3.6.0 alpha or +
	{*/
		$text=$langs->trans("EcoTaxAddedIfDesc",join(', ',$elements));
	/*}
	else
	{
		$text=$langs->trans("EcoTaxAddedIfDescOld",join(',',$elements));
	}*/
	print info_admin($text);
}



// Show message
/*$message='';
$urlgooglehelp='<a href="http://www.google.com/calendar/embed/EmbedHelper_en.html" target="_blank">http://www.google.com/calendar/embed/EmbedHelper_en.html</a>';
$message.=$langs->trans("GoogleSetupHelp",$urlgooglehelp);
print info_admin($message);
*/

llxFooter();

$db->close();
