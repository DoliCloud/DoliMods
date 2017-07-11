<?php
/* Copyright (C) 2012      Mikael Carlavan        <mcarlavan@qis-network.com>
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

/**	    \file       htdocs/cmcic/admin/tpl/cmcic_config.php
 *		\ingroup    cmcic
 *		\brief      Page to setup cmcic module
 */

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

require_once(DOL_DOCUMENT_ROOT."/core/lib/admin.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/security.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/class/doleditor.class.php");


$langs->load("admin");
$langs->load("other");
$langs->load("cmcic@cmcic");

if (!$user->admin) accessforbidden();

$updated = false;
$error = false;

$err = '';

if (isset($_POST["action"]) && $_POST["action"] == 'update'){
    $emails = $_POST["CMCIC_CC_EMAILS"];

    if (!empty($emails)){
        $emailsList = explode(',', $emails);

        foreach($emailsList as $email){
            if (!isValidEMail($email)){
                $error = true;
                $err = $langs->trans('CMCIC_BAD_EMAIL');
            }
        }
    }
}


if (isset($_POST["action"]) && $_POST["action"] == 'update' && !$error)
{

    $base_url = preg_replace('/'.preg_quote(DOL_URL_ROOT,'/').'$/i','', $dolibarr_main_url_root);
    $url_ok = $base_url .DOL_URL_ROOT. '/public/cmcic/success.php';
    $url_ko = $base_url .DOL_URL_ROOT. '/public/cmcic/error.php';
    $url_ret = $base_url .DOL_URL_ROOT. '/public/cmcic/return.php';
    //

    $result = dolibarr_set_const($db, "CMCIC_API_TEST",$_POST["CMCIC_API_TEST"],'chaine',0,'',$conf->entity);
    $result = dolibarr_set_const($db, "CMCIC_TPE_NUMBER",$_POST["CMCIC_TPE_NUMBER"],'chaine',0,'',$conf->entity);
    $result = dolibarr_set_const($db, "CMCIC_SOCIETY_ID",$_POST["CMCIC_SOCIETY_ID"],'chaine',0,'',$conf->entity);
    $result = dolibarr_set_const($db, "CMCIC_KEY",$_POST["CMCIC_KEY"],'chaine',0,'',$conf->entity);
    $result = dolibarr_set_const($db, "CMCIC_BANK_SERVER",$_POST["CMCIC_BANK_SERVER"],'chaine',0,'',$conf->entity);
    $result = dolibarr_set_const($db, "CMCIC_SECURITY_TOKEN",$_POST["CMCIC_SECURITY_TOKEN"],'chaine',0,'',$conf->entity);
    $result = dolibarr_set_const($db, "CMCIC_CC_EMAIL",$_POST["CMCIC_CC_EMAIL"],'chaine',0,'',$conf->entity);
    $result = dolibarr_set_const($db, "CMCIC_CC_EMAILS",$_POST["CMCIC_CC_EMAILS"],'chaine',0,'',$conf->entity);
    $result = dolibarr_set_const($db, "CMCIC_DELIVERY_RECEIPT_EMAIL",$_POST["CMCIC_DELIVERY_RECEIPT_EMAIL"],'chaine',0,'',$conf->entity);
    $result = dolibarr_set_const($db, "CMCIC_BANK_ACCOUNT_ID", $_POST["CMCIC_BANK_ACCOUNT_ID"], 'chaine',0,'',$conf->entity);
    $result = dolibarr_set_const($db, "CMCIC_UPDATE_INVOICE_STATUT",$_POST["CMCIC_UPDATE_INVOICE_STATUT"],'chaine',0,'',$conf->entity);
    //
    $result = dolibarr_set_const($db, "CMCIC_RETURN_URL", $url_ret,'chaine',0,'',$conf->entity);
    $result = dolibarr_set_const($db, "CMCIC_URL_OK", $url_ok,'chaine',0,'',$conf->entity);
    $result = dolibarr_set_const($db, "CMCIC_URL_KO", $url_ko,'chaine',0,'',$conf->entity);

	if ($result >= 0)
  	{
        $updated = true;
  	}
  	else
  	{
        $error = true;
		$err = $db->error();
    }
}
/*
 *	View
 */

$form = new Form($db);
$linkback = '<a href="'.DOL_URL_ROOT.'/admin/modules.php">'.$langs->trans("BackToModuleList").'</a>';
$params = $conf->global;

//Prepare head
$h = 0;
$head = array();

$head[$h][0] = dol_buildpath("/cmcic/admin/cmcic_config.php",1);
$head[$h][1] = $langs->trans("CMCIC_ACCOUNT");
$head[$h][2] = 'account';
$h++;

$current_head = 'account';

$htmltooltips = array(
    'CMCIC_API_TEST'    => $langs->trans("CMCIC_API_TEST_TOOLTIP"),
    'CMCIC_BANK_SERVER' => $langs->trans("CMCIC_BANK_SERVER_TOOLTIP"),
    'CMCIC_TPE_NUMBER'  => $langs->trans("CMCIC_TPE_NUMBER_TOOLTIP"),
    'CMCIC_SOCIETY_ID'  => $langs->trans("CMCIC_SOCIETY_ID_TOOLTIP"),
    'CMCIC_KEY' => $langs->trans("CMCIC_KEY_TOOLTIP"),
    'CMCIC_SECURITY_TOKEN'=> $langs->trans("CMCIC_SECURITY_TOKEN_TOOLTIP"),
    'CMCIC_DELIVERY_RECEIPT_EMAIL'  => $langs->trans("CMCIC_DELIVERY_RECEIPT_EMAIL_TOOLTIP"),
    'CMCIC_CC_EMAIL' => $langs->trans("CMCIC_CC_EMAIL_TOOLTIP"),
    'CMCIC_CC_EMAILS' => $langs->trans("CMCIC_CC_EMAILS_TOOLTIP"),
    'CMCIC_BANK_ACCOUNT_ID' => $langs->trans("CMCIC_BANK_ACCOUNT_ID_TOOLTIP"),
    'CMCIC_UPDATE_INVOICE_STATUT' => $langs->trans("CMCIC_UPDATE_INVOICE_STATUT_TOOLTIP")
);

require_once('tpl/config_form.php');

$db->close();

