<?php
/* Copyright (C) 2011 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2010 Jean-Francois FERRY  <jfefe@aternatik.fr>
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
 *   	\file       htdocs/ovh/sms_thirdparty.php
 *		\ingroup    ovh
 *		\brief
 */
$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");
if (! $res && file_exists("../../../../main.inc.php")) $res=@include("../../../../main.inc.php");
if (! $res && file_exists("../../../../../main.inc.php")) $res=@include("../../../../../main.inc.php");
if (! $res && preg_match('/\/nltechno([^\/]*)\//',$_SERVER["PHP_SELF"],$reg)) $res=@include("../../../dolibarr".$reg[1]."/htdocs/main.inc.php"); // Used on dev env only
if (! $res) die("Include of main fails");
require_once(DOL_DOCUMENT_ROOT."/core/lib/company.lib.php");
require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
require_once(DOL_DOCUMENT_ROOT."/contact/class/contact.class.php");
dol_include_once("/ovh/class/ovhsms.class.php");

// Load traductions files requiredby by page
$langs->load("companies");
$langs->load("sms");
$langs->load("ovh@ovh");

// Get parameters
$socid = GETPOST('socid','int')?GETPOST('socid','int'):GETPOST('id','int');
$action = GETPOST('action');
$mesg='';

// Protection if external user
if ($user->societe_id > 0)
{
	accessforbidden();
}



/*******************************************************************
 * ACTIONS
 ********************************************************************/

/* Envoi d'un SMS */
if ($action == 'send' && ! $_POST['cancel'])
{
    $error=0;

    $smsfrom='';
    if (! empty($_POST["fromsms"])) $smsfrom=GETPOST("fromsms");
    if (empty($smsfrom)) $smsfrom=GETPOST("fromname");
    $sendto     = GETPOST("sendto");
    $receiver   = GETPOST('receiver');
    $body       = GETPOST('message');
    $deliveryreceipt= GETPOST("deliveryreceipt");
    $deferred   = GETPOST('deferred');
    $priority   = GETPOST('priority');
    $class      = GETPOST('class');
    $errors_to  = GETPOST("errorstosms");

    $thirdparty=new Societe($db);
    $thirdparty->fetch($socid);

    if ($receiver == 'thirdparty') $sendto=$thirdparty->phone;
    if ((empty($sendto) || ! str_replace('+','',$sendto)) && (! empty($receiver) && $receiver != '-1'))
    {
        $sendto=$thirdparty->contact_get_property($receiver,'mobile');
    }

    // Test param
    if (empty($body))
    {
        setEventMessage($langs->trans("ErrorFieldRequired",$langs->transnoentities("Message")),'errors');
        $action='test';
        $error++;
    }
    if (empty($smsfrom) || ! str_replace('+','',$smsfrom))
    {
        setEventMessage($langs->trans("ErrorFieldRequired",$langs->transnoentities("SmsFrom")),'errors');
        $action='test';
        $error++;
    }
    if ((empty($sendto) || ! str_replace('+','',$sendto)) && (empty($receiver) || $receiver == '-1'))
    {
        setEventMessage($langs->trans("ErrorFieldRequired",$langs->transnoentities("SmsTo")),'errors');
        $action='test';
        $error++;
    }

    if (! $error)
    {
        // Make substitutions into message
        $substitutionarrayfortest=array();
        complete_substitutions_array($substitutionarrayfortest,$langs);
        $body=make_substitutions($body,$substitutionarrayfortest);

        require_once(DOL_DOCUMENT_ROOT."/core/class/CSMSFile.class.php");

        $smsfile = new CSMSFile($sendto, $smsfrom, $body, $deliveryreceipt, $deferred, $priority, $class);  // This define OvhSms->login, pass, session and account
        $result=$smsfile->sendfile(); // This send SMS

        if ($result > 0)
        {
            $mesg='<div class="ok">'.$langs->trans("SmsSuccessfulySent",$smsfrom,$sendto).'</div>';
        }
        else
        {
            $mesg='<div class="error">'.$langs->trans("ResultKo").' (sms from'.$smsfrom.' to '.$sendto.')<br>'.$smsfile->error.'</div>';
        }

        $action='';
    }
}




/***************************************************
 * View
 ****************************************************/

$error=0;

llxHeader('','Ovh','');

$form=new Form($db);


if ($socid)
{
    if (empty($conf->global->OVHSMS_SOAPURL))
    {
        $error++;
        $langs->load("errors");
        $mesg='<div class="error">'.$langs->trans("ErrorModuleSetupNotComplete").'</div>';
    }
    if (empty($conf->global->OVHSMS_ACCOUNT))
    {
        $error++;
        $langs->load("errors");
        $mesg='<div class="error">'.$langs->trans("ErrorModuleSetupNotComplete").'</div>';
    }

	$sms = new OvhSms($db);

	/*
	 * Creation de l'objet client/fournisseur correspondant au socid
	 */

	$soc = new Societe($db);
	$result = $soc->fetch($socid);


	/*
	 * Affichage onglets
	 */
	$head = societe_prepare_head($soc);
	dol_fiche_head($head, 'tabSMS', $langs->trans("ThirdParty"),0,'company');

    if ($mesg)
    {
        if (preg_match('/class="error"/',$mesg)) dol_htmloutput_mesg($mesg,'','error');
        else
        {
            dol_htmloutput_mesg($mesg,'','ok',1);
            print '<br>';
        }
    }

    print '<table class="border" width="100%">';

    print '<tr><td width="20%">'.$langs->trans('Name').'</td>';
    print '<td colspan="3">';
    print $form->showrefnav($soc,'socid','',($user->societe_id?0:1),'rowid','nom');
    print '</td></tr>';

    if (! empty($conf->global->SOCIETE_USEPREFIX))  // Old not used prefix field
    {
        print '<tr><td>'.$langs->trans('Prefix').'</td><td colspan="3">'.$soc->prefix_comm.'</td></tr>';
    }

    if ($soc->client)
    {
        print '<tr><td>';
        print $langs->trans('CustomerCode').'</td><td colspan="3">';
        print $soc->code_client;
        if ($soc->check_codeclient() <> 0) print ' <font class="error">('.$langs->trans("WrongCustomerCode").')</font>';
        print '</td></tr>';
    }

    if ($soc->fournisseur)
    {
        print '<tr><td>';
        print $langs->trans('SupplierCode').'</td><td colspan="3">';
        print $soc->code_fournisseur;
        if ($soc->check_codefournisseur() <> 0) print ' <font class="error">('.$langs->trans("WrongSupplierCode").')</font>';
        print '</td></tr>';
    }

    print '</table><br>';

    print_fiche_titre($langs->trans("Sms"),'','phone.png@ovh');

    // Cree l'objet formulaire mail
    include_once(DOL_DOCUMENT_ROOT.'/core/class/html.formsms.class.php');
    $formsms = new FormSms($db);
    $formsms->fromtype = 'user';
    $formsms->fromid   = $user->id;
    $formsms->fromname = $user->getFullName($langs);
    $formsms->fromsms = $user->user_mobile;
    $formsms->withfrom=1;
    $formsms->withtosocid=$socid;
    $formsms->withfromreadonly=0;
    $formsms->withto=empty($_POST["sendto"])?1:$_POST["sendto"];
    $formsms->withbody=1;
    $formsms->withcancel=0;
    // Tableau des substitutions
    $formsms->substit['__THIRDPARTYREF__']=$soc->ref;
    // Tableau des parametres complementaires du post
    $formsms->param['action']='send';
    $formsms->param['models']='';
    $formsms->param['id']=$soc->id;
    $formsms->param['returnurl']=$_SERVER["PHP_SELF"].'?id='.$soc->id;

    $formsms->show_form('20%');


    dol_fiche_end();
}


llxFooter();

// End of page
$db->close();
?>
