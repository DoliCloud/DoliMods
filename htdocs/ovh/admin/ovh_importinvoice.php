<?php

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

dol_include_once('/ovh/class/ovh.class.php');
dol_include_once("/ovh/lib/ovh.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/admin.lib.php");
require_once(DOL_DOCUMENT_ROOT."/user/class/user.class.php");
require_once(DOL_DOCUMENT_ROOT.'/fourn/class/paiementfourn.class.php');
require_once(DOL_DOCUMENT_ROOT.'/fourn/class/fournisseur.facture.class.php');
require_once(DOL_DOCUMENT_ROOT.'/core/lib/fourn.lib.php');
require_once(DOL_DOCUMENT_ROOT.'/product/class/product.class.php');
require_once(DOL_DOCUMENT_ROOT."/core/lib/files.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/class/html.formfile.class.php");
require_once(NUSOAP_PATH.'/nusoap.php');     // Include SOAP

require __DIR__ . '/../includes/autoload.php';
use \Ovh\Api;
use GuzzleHttp\Client as GClient;


$langs->load("ovh@ovh");
$langs->load("admin");
$langs->load("companies");
$langs->load("sms");

$error=0;

$action = GETPOST('action','aZ09');

// Protection if external user
if ($user->societe_id > 0) accessforbidden();

$endpoint = empty($conf->global->OVH_ENDPOINT)?'ovh-eu':$conf->global->OVH_ENDPOINT;



/*
 * Actions
 */

if ($action == 'setvalue' && $user->admin)
{
	$idproduct = (GETPOST("OVH_IMPORT_SUPPLIER_INVOICE_PRODUCT_ID") > 0 ? GETPOST("OVH_IMPORT_SUPPLIER_INVOICE_PRODUCT_ID") : 0);

    $result1=dolibarr_set_const($db, "OVH_THIRDPARTY_IMPORT", GETPOST("OVH_THIRDPARTY_IMPORT"), 'chaine', 0, '', $conf->entity);
    $result2=dolibarr_set_const($db, "OVH_IMPORT_SUPPLIER_INVOICE_PRODUCT_ID", $idproduct, 'chaine', 0, '', $conf->entity);
    $result3=dolibarr_set_const($db, "OVH_DEFAULT_BANK_ACCOUNT", (GETPOST("OVH_DEFAULT_BANK_ACCOUNT") > 0 ? GETPOST("OVH_DEFAULT_BANK_ACCOUNT") : 0), 'chaine', 0, '', $conf->entity);
    if ($result1 >= 0 && $result2 >= 0 && $result3 >= 0)
    {
        $mesg='<div class="ok">'.$langs->trans("SetupSaved").'</div>';
    }
    else
    {
        dol_print_error($db);
    }
}




/*
 * View
 */

$form=new Form($db);

$WS_DOL_URL = $conf->global->OVHSMS_SOAPURL;
dol_syslog("Will use URL=".$WS_DOL_URL, LOG_DEBUG);

$login = $conf->global->OVHSMS_NICK;
$password = $conf->global->OVH_SMS_PASS;

$logindol=$user->login;



$morejs = '';
llxHeader('', $langs->trans('OvhSmsSetup'), '', '', '', '', $morejs, '', 0, 0);

$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php">'.$langs->trans("BackToModuleList").'</a>';

print_fiche_titre($langs->trans('OvhSmsSetup'),$linkback,'setup');

$head=ovhadmin_prepare_head();

dol_htmloutput_mesg($mesg);



// Formulaire d'ajout de compte SMS qui sera valable pour tout Dolibarr
print '<form method="post" action="'.$_SERVER["PHP_SELF"].'">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="setvalue">';


if (! empty($conf->global->OVH_OLDAPI) && (empty($conf->global->OVHSMS_NICK) || empty($WS_DOL_URL)))  // For old API
{
    echo '<div class="warning">'.$langs->trans("OvhSmsNotConfigured").'</div>';
}
else
{
    $var=true;

    dol_fiche_head($head, 'getinvoices', $langs->trans("Ovh"), -1);

    if (empty($conf->global->OVH_OLDAPI) && (empty($conf->global->OVHAPPKEY) || empty($conf->global->OVHAPPSECRET) || empty($conf->global->OVHCONSUMERKEY)))
    {
        echo '<div class="warning">'.$langs->trans("OvhAuthenticationPartNotConfigured").'</div>';
    }

    print '<table class="noborder" width="100%">';
    print '<tr class="liste_titre">';
    print '<td>'.$langs->trans("Parameter").'</td>';
    print '<td>'.$langs->trans("Value").'</td>';
    print '<td>&nbsp;</td>';
    print "</tr>\n";

/*
    $var=!$var;
    print '<tr '.$bc[$var].'><td class="fieldrequired">';
    print $langs->trans("UserMakingImport").'</td><td>';
    print '<input size="64" type="text" name="OVH_USER_LOGIN" value="'.$logindol.'">';
    print '<td>';
    print '</td></tr>';
*/

    $var=!$var;
    print '<tr '.$bc[$var].'><td class="fieldrequired">';
    print $langs->trans("SupplierToUseForImport").'</td><td>';
    print $form->select_company($conf->global->OVH_THIRDPARTY_IMPORT,'OVH_THIRDPARTY_IMPORT','s.fournisseur = 1',1,'supplier');
    print '<td>';
    print '</td></tr>';

    if ($conf->product->enabled || $conf->service->enabled)
    {
        $var=!$var;
        print '<tr '.$bc[$var].'><td>';
        print $langs->trans("ProductGenericToUseForImport").'</td><td>';
        print $form->select_produits($conf->global->OVH_IMPORT_SUPPLIER_INVOICE_PRODUCT_ID, 'OVH_IMPORT_SUPPLIER_INVOICE_PRODUCT_ID', '', 0, 0, -1);
        print '<td>';
        print $langs->trans("KeepEmptyToSaveLinesAsFreeLines");
        print '</td></tr>';
    }

    if ($conf->banque->enabled)
    {
    	$var=!$var;
    	print '<tr '.$bc[$var].'><td>';
    	print $langs->trans("OvhDefaultBankAccount").'</td><td>';
    	print $form->select_comptes($conf->global->OVH_DEFAULT_BANK_ACCOUNT, 'OVH_DEFAULT_BANK_ACCOUNT', 0, '', 1);
    	print '<td>';
    	//print $langs->trans("KeepEmptyToSaveLinesAsFreeLines");
    	print '</td></tr>';
    }

    print '</table>';

    dol_fiche_end();

    print '<div class="center"><input type="submit" class="button" value="'.$langs->trans("Modify").'"></div>';
}

print '</form>';


llxFooter();

$db->close();

