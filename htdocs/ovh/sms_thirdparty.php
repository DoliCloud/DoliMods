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
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");
if (! $res) die("Include of main fails");

require_once(DOL_DOCUMENT_ROOT."/core/lib/company.lib.php");
require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
require_once(DOL_DOCUMENT_ROOT."/contact/class/contact.class.php");
dol_include_once("/ovh/class/ovhsms.class.php");

require __DIR__ . '/includes/autoload.php';
use \Ovh\Api;


// Load traductions files requiredby by page
$langs->load("companies");
$langs->load("sms");
$langs->load("ovh@ovh");

// Get parameters
$socid = GETPOST('socid','int')?GETPOST('socid','int'):GETPOST('id','int');
$action = GETPOST('action','aZ09');
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
    	/*$actionmsg2 = $langs->transnoentities('SMSSentBy').' '.$smsfrom;
    	if ($body)
    	{
    		$actionmsg = $langs->transnoentities('SMSFrom').': '.dol_escape_htmltag($smsfrom);
    		$actionmsg = dol_concatdesc($actionmsg, $langs->transnoentities('To').': '.dol_escape_htmltag($sendto));
    		$actionmsg = dol_concatdesc($actionmsg, $langs->transnoentities('TextUsedInTheMessageBody').":");
    		$actionmsg = dol_concatdesc($actionmsg, $body);
    	}

    	$triggersendname = 'SMS_SENT';*/

        // Make substitutions into message
        $substitutionarrayfortest=array();
        complete_substitutions_array($substitutionarrayfortest, $langs);
        $body=make_substitutions($body,$substitutionarrayfortest);

        require_once(DOL_DOCUMENT_ROOT."/core/class/CSMSFile.class.php");

        //if (empty($sendcontext)) $sendcontext = 'standard';
        $smsfile = new CSMSFile($sendto, $smsfrom, $body, $deliveryreceipt, $deferred, $priority, $class);  // This define OvhSms->login, pass, session and account

        $smsfile->nostop=GETPOST('disablestop');
        $smsfile->socid = $socid;
        $smsfile->contactid = 0;
        $smsfile->fk_project = 0;

        // Send the SMS
        $result=$smsfile->sendfile(); // This send SMS. It also includes run of triggers 'SENTBYSMS'.

        if ($result > 0)
        {
        	$object = $thirdparty;

            setEventMessages($langs->trans("SmsSuccessfulySent", $smsfrom, $sendto), null);
        }
        else
        {
            setEventMessages($langs->trans("ResultKo").' (sms from'.$smsfrom.' to '.$sendto.')<br>'.$smsfile->error, null, 'errors');
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
	if (! empty($conf->global->OVH_OLDAPI))
	{
		if (empty($conf->global->OVHSMS_SOAPURL))
	    {
	        $error++;
	        $langs->load("errors");
	        $mesg='<div class="error">'.$langs->trans("ErrorModuleSetupNotComplete").'</div>';
	    }
	}
	else
	{
	    if (empty($conf->global->OVHSMS_ACCOUNT))
	    {
	        $error++;
	        $langs->load("errors");
	        $mesg='<div class="error">'.$langs->trans("ErrorModuleSetupNotComplete").'</div>';
	    }
	}

	$sms = new OvhSms($db);

	/*
	 * Creation de l'objet client/fournisseur correspondant au socid
	 */

	$object = new Societe($db);
	$result = $object->fetch($socid);


    print "<form method=\"POST\" name=\"smsform\" enctype=\"multipart/form-data\" action=\"".$_SERVER["PHP_SELF"].'?id='.$object->id."\">\n";
    if ((float) DOL_VERSION >= 11.0) {
    	print '<input type="hidden" name="token" value="'.newToken().'">';
    } else {
    	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
    }


	/*
	 * Show tabs
	 */
	$head = societe_prepare_head($object);
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

    dol_banner_tab($object,'id','',$user->rights->user->user->lire || $user->admin);

    print '<div class="underbanner clearboth"></div>';

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
    $formsms->substit['__THIRDPARTYREF__']=$object->ref;
    // Tableau des parametres complementaires du post
    $formsms->param['action']='send';
    $formsms->param['models']='';
    $formsms->param['id']=$object->id;
    $formsms->param['returnurl']=$_SERVER["PHP_SELF"].'?id='.$object->id;

    $formsms->show_form('', 0);

    dol_fiche_end();

    print '<div class="center">';
    print '<input class="button" type="submit" name="sendmail" value="'.dol_escape_htmltag($langs->trans("SendSms")).'">';
    if ($formsms->withcancel)
    {
    	print '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
    	print '<input class="button" type="submit" name="cancel" value="'.dol_escape_htmltag($langs->trans("Cancel")).'">';
    }
    print '</div>';

    print "</form>\n";
}


llxFooter();

// End of page
$db->close();

