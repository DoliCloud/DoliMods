<?php
/* Copyright (C) 2004-2014      Laurent Destailleur  <eldy@users.sourceforge.net>
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
 *   \file       htdocs/cabinetmed/documents.php
 *   \brief      Tab for courriers
 *   \ingroup    cabinetmed
 */

$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");
if (! $res && file_exists("../../../../main.inc.php")) $res=@include("../../../../main.inc.php");
if (! $res && preg_match('/\/nltechno([^\/]*)\//',$_SERVER["PHP_SELF"],$reg)) $res=@include("../../../dolibarr".$reg[1]."/htdocs/main.inc.php"); // Used on dev env only
if (! $res) die("Include of main fails");
require_once(DOL_DOCUMENT_ROOT."/core/lib/company.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/files.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/images.lib.php");
require_once(DOL_DOCUMENT_ROOT."/contact/class/contact.class.php");
require_once(DOL_DOCUMENT_ROOT."/compta/bank/class/account.class.php");
require_once(DOL_DOCUMENT_ROOT."/core/class/html.formfile.class.php");
include_once("./lib/cabinetmed.lib.php");
include_once("./class/patient.class.php");
include_once("./class/cabinetmedcons.class.php");
include_once("./class/html.formfilecabinetmed.class.php");

$action=GETPOST("action");
$idconsult=GETPOST('idconsult','int')?GETPOST('idconsult','int'):GETPOST('idconsult','int');  // Id consultation
$confirm=GETPOST('confirm');
$mesg=GETPOST('mesg');

$langs->load("companies");
$langs->load("bills");
$langs->load("banks");
$langs->load("other");
$langs->load("cabinetmed@cabinetmed");

// Security check
$id=(GETPOST('socid','int') ? GETPOST('socid','int') : GETPOST('id','int'));
$socid=$id;
if ($user->societe_id) $socid=$user->societe_id;
$result = restrictedArea($user, 'societe', $socid);

if (!$user->rights->cabinetmed->read) accessforbidden();

$error=0;
$errors=array();

$sortfield = GETPOST("sortfield",'alpha');
$sortorder = GETPOST("sortorder",'alpha');
$page = GETPOST("page",'int');
if ($page == -1) { $page = 0; }
$offset = $conf->liste_limit * $page;
$pageprev = $page - 1;
$pagenext = $page + 1;
if (! $sortfield) $sortfield='date';
if (! $sortorder) $sortorder='DESC';
$limit = $conf->liste_limit;

$now=dol_now();

$object = new Societe($db);
$consult = new CabinetmedCons($db);

if ($id > 0 || ! empty($ref))
{
	$result = $object->fetch($id, $ref);

	$upload_dir = $conf->societe->multidir_output[$object->entity] . "/" . $object->id ;
	$courrier_dir = $conf->societe->multidir_output[$object->entity] . "/courrier/" . get_exdir($object->id);
}

// Initialize technical object to manage hooks of thirdparties. Note that conf->hooks_modules contains array array
include_once(DOL_DOCUMENT_ROOT.'/core/class/hookmanager.class.php');
$hookmanager=new HookManager($db);
$hookmanager->initHooks(array('documentcabinetmed'));



/*
 * Actions
 */

include_once DOL_DOCUMENT_ROOT . '/core/tpl/document_actions_pre_headers.tpl.php';



// Generate document
if ($action == 'builddoc')  // En get ou en post
{
    if (! GETPOST('model'))
    {
        $errors[]=$langs->trans("WarningNoDocumentModelActivated");
    }
    else if (is_numeric(GETPOST('model')))
    {
        $errors[]=$langs->trans("ErrorFieldRequired",$langs->transnoentities("Model"));
    }
    else
    {
        require_once(DOL_DOCUMENT_ROOT.'/core/modules/societe/modules_societe.class.php');

        $soc = new Societe($db);
        $soc->fetch($socid);
        $soc->fetch_thirdparty();

        $consult = new CabinetmedCons($db);
        $consult->fetch($idconsult);

        // Define output language
        $outputlangs = $langs;
        $newlang='';
        if ($conf->global->MAIN_MULTILANGS && empty($newlang) && GETPOST('lang_id')) $newlang=GETPOST('lang_id');
        //if ($conf->global->MAIN_MULTILANGS && empty($newlang)) $newlang=$fac->client->default_lang;
        if (! empty($newlang))
        {
            $outputlangs = new Translate("",$conf);
            $outputlangs->setDefaultLang($newlang);
        }
        $result=thirdparty_doc_create($db, $soc, '', GETPOST('model','alpha'), $outputlangs);
        if ($result <= 0)
        {
            dol_print_error($db,$result);
            exit;
        }
    }
}

/*
 * Add file in email form
 */
if ($_POST['addfile'])
{
    require_once(DOL_DOCUMENT_ROOT."/core/lib/files.lib.php");

    // Set tmp user directory TODO Use a dedicated directory for temp mails files
    $vardir=$conf->user->dir_output."/".$user->id;
    $upload_dir_tmp = $vardir.'/temp';

    $mesg=dol_add_file_process($upload_dir_tmp,0,0);

    $action='presend';
    $_POST["action"]='presend';
}

/*
 * Remove file in email form
 */
if (! empty($_POST['removedfile']))
{
    require_once(DOL_DOCUMENT_ROOT."/core/lib/files.lib.php");

    // Set tmp user directory
    $vardir=$conf->user->dir_output."/".$user->id;
    $upload_dir_tmp = $vardir.'/temp';

	// TODO Delete only files that was uploaded from email form
    $mesg=dol_remove_file_process($_POST['removedfile'],0);

    $action='presend';
    $_POST["action"]='presend';
}

/*
 * Send mail
 */
if ($_POST['action'] == 'send' && ! $_POST['addfile'] && ! $_POST['removedfile'] && ! $_POST['cancel'])
{
    $error=0;

    if (! GETPOST('subject'))
    {
        $langs->load("other");
        $mesg='<div class="error">'.$langs->trans('ErrorFieldRequired',$langs->transnoentitiesnoconv("Subject")).'</div>';
    }

    $langs->load('mails');

    $result=$object->fetch($_POST["socid"]);
    if ($result > 0)
    {
        $objectref = dol_sanitizeFileName($object->ref);

        if ($_POST['sendto'])
        {
            // Le destinataire a ete fourni via le champ libre
            $sendto = $_POST['sendto'];
            $sendtoid = 0;
        }
        elseif ($_POST['receiver'] != '-1')
        {
            // Recipient was provided from combo list
            if ($_POST['receiver'] == 'thirdparty') // Id of third party
            {
                $sendto = $object->email;
                $sendtoid = 0;
            }
            else    // Id du contact
            {
                $sendto = $object->contact_get_property($_POST['receiver'],'email');
                $sendtoid = $_POST['receiver'];
            }
        }

        if (dol_strlen($sendto))
        {
            $langs->load("commercial");

            $from = $_POST['fromname'] . ' <' . $_POST['frommail'] .'>';
            $replyto = $_POST['replytoname']. ' <' . $_POST['replytomail'].'>';
            $message = $_POST['message'];
            $sendtocc = $_POST['sendtocc'];
            $deliveryreceipt = $_POST['deliveryreceipt'];

            // Create form object
            include_once(DOL_DOCUMENT_ROOT.'/core/class/html.formmail.class.php');
            $formmail = new FormMail($db);

            $attachedfiles=$formmail->get_attached_files();
            $filepath = $attachedfiles['paths'];
            $filename = $attachedfiles['names'];
            $mimetype = $attachedfiles['mimes'];

            if ($_POST['action'] == 'send')
            {
                $subject = GETPOST('subject');
                $actiontypecode='AC_CABMED';
                $actionmsg = $langs->transnoentities('MailSentBy').' '.$from.' '.$langs->transnoentities('To').' '.$sendto;
                if ($message)
                {
					if ($sendtocc) $actionmsg = dol_concatdesc($actionmsg, $langs->transnoentities('Bcc') . ": " . $sendtocc);
					$actionmsg = dol_concatdesc($actionmsg, $langs->transnoentities('MailTopic') . ": " . $subject);
					$actionmsg = dol_concatdesc($actionmsg, $langs->transnoentities('TextUsedInTheMessageBody') . ":");
					$actionmsg = dol_concatdesc($actionmsg, $message);
                }
                $actionmsg2=$langs->transnoentities('Action'.$actiontypecode,join(',',$attachedfiles['names']));
            }

            // Envoi de la propal
            require_once(DOL_DOCUMENT_ROOT.'/core/class/CMailFile.class.php');
            $mailfile = new CMailFile($subject,$sendto,$from,$message,$filepath,$mimetype,$filename,$sendtocc,'',$deliveryreceipt);
            if ($mailfile->error)
            {
                $mesg='<div class="error">'.$mailfile->error.'</div>';
            }
            else
            {
                $result=$mailfile->sendfile();
                if ($result)
                {
                    $mesg=$langs->trans('MailSuccessfulySent',$mailfile->getValidAddress($from,2),$mailfile->getValidAddress($sendto,2));   // Must not contain "

                    $error=0;

                    // Initialisation donnees
                    $object->sendtoid       = $sendtoid;
                    $object->socid          = $object->id;
                    $object->actiontypecode = $actiontypecode;
                    $object->actionmsg      = $actionmsg;
                    $object->actionmsg2     = $actionmsg2;

                    // Appel des triggers
                    include_once(DOL_DOCUMENT_ROOT . "/core/class/interfaces.class.php");
                    $interface=new Interfaces($db);
                    $result=$interface->run_triggers('CABINETMED_SENTBYMAIL',$object,$user,$langs,$conf);
                    if ($result < 0) { $error++; }
                    // Fin appel triggers

                    if ($error)
                    {
                        dol_print_error($db);
                    }
                    else
                    {
                        // Redirect here
                        // This avoid sending mail twice if going out and then back to page
                        Header('Location: '.$_SERVER["PHP_SELF"].'?socid='.$object->id.'&mesg='.urlencode($mesg));
                        exit;
                    }
                }
                else
                {
                    $langs->load("other");
                    $mesg='<div class="error">';
                    if ($mailfile->error)
                    {
                        $mesg.=$langs->trans('ErrorFailedToSendMail',$from,$sendto);
                        $mesg.='<br>'.$mailfile->error;
                    }
                    else
                    {
                        $mesg.='No mail sent. Feature is disabled by option MAIN_DISABLE_ALL_MAILS';
                    }
                    $mesg.='</div>';
                }
            }
        }
        else
        {
            $action='presend';
            $langs->load("other");
            $mesg='<div class="error">'.$langs->trans('ErrorMailRecipientIsEmpty').' !</div>';
            dol_syslog('Recipient email is empty');
        }
    }
    else
    {
        $langs->load("other");
        $mesg='<div class="error">'.$langs->trans('ErrorFailedToReadEntity',$langs->transnoentitiesnoconv("Proposal")).'</div>';
        dol_syslog('Impossible de lire les donnees de la facture. Le fichier propal n\'a peut-etre pas ete genere.');
    }
}


/*
 *	View
 */

$form = new Form($db);
$formfile = new FormFile($db);
$contactstatic = new Contact($db);

$width="242";

llxHeader('',$langs->trans("Courriers"));

if ($object->id)
{
    if ($idconsult && ! $consult->id)
    {
        $result=$consult->fetch($idconsult);
        if ($result < 0) dol_print_error($db,$consult->error);

        $result=$consult->fetch_bankid();
        if ($result < 0) dol_print_error($db,$consult->error);
    }

    /*
     * Affichage onglets
     */
    if ($conf->notification->enabled) $langs->load("mails");

    $head = societe_prepare_head($object);
    dol_fiche_head($head, 'tabdocument', $langs->trans("Patient"),0,'company');


    // Construit liste des fichiers
    $filearray=dol_dir_list($upload_dir,"files",0,'','(\.meta|_preview\.png)$',$sortfield,(strtolower($sortorder)=='desc'?SORT_DESC:SORT_ASC),1);
    $totalsize=0;
    foreach($filearray as $key => $file)
    {
        $totalsize+=$file['size'];
    }

    print "<form method=\"post\" action=\"".$_SERVER["PHP_SELF"]."\">";
    print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';

    print '<table class="border" width="100%">';

    print '<tr><td width="25%">'.$langs->trans('PatientName').'</td>';
    print '<td colspan="3">';
    print $form->showrefnav($object,'socid','',($user->societe_id?0:1),'rowid','nom');
    print '</td></tr>';

	// Prefix
	if (! empty($conf->global->SOCIETE_USEPREFIX))  // Old not used prefix field
	{
		print '<tr><td>'.$langs->trans('Prefix').'</td><td colspan="3">'.$object->prefix_comm.'</td></tr>';
	}

    if ($object->client)
    {
        print '<tr><td>';
        print $langs->trans('CustomerCode').'</td><td colspan="3">';
        print $object->code_client;
        if ($object->check_codeclient() <> 0) print ' <font class="error">('.$langs->trans("WrongCustomerCode").')</font>';
        print '</td></tr>';
    }

    if ($object->fournisseur)
    {
        print '<tr><td>';
        print $langs->trans('SupplierCode').'</td><td colspan="3">';
        print $object->code_fournisseur;
        if ($object->check_codefournisseur() <> 0) print ' <font class="error">('.$langs->trans("WrongSupplierCode").')</font>';
        print '</td></tr>';
    }

    // Nbre fichiers
    print '<tr><td>'.$langs->trans("NbOfAttachedFiles").'</td><td colspan="3">'.count($filearray).'</td></tr>';

    //Total taille
    print '<tr><td>'.$langs->trans("TotalSizeOfAttachedFiles").'</td><td colspan="3">'.$totalsize.' '.$langs->trans("bytes").'</td></tr>';

    print '</table>';

    print '</form>';

    dol_fiche_end();

	/*
	$modulepart = 'societe';
	$permission = $user->rights->societe->creer;
	$param = '&id=' . $object->id;
	include_once DOL_DOCUMENT_ROOT . '/core/tpl/document_actions_post_headers.tpl.php';
	*/

    if ($mesg) dol_htmloutput_mesg($mesg);
    else dol_htmloutput_mesg($error,$errors,'error');

    $param='';

    if ($action == 'delete')
    {
		$langs->load("companies");	// Need for string DeleteFile+ConfirmDeleteFiles
		$ret = $form->form_confirm(
				$_SERVER["PHP_SELF"] . '?id=' . $object->id . '&urlfile=' . urlencode(GETPOST("urlfile")) . '&linkid=' . GETPOST('linkid', 'int') . (empty($param)?'':$param),
				$langs->trans('DeleteFile'),
				$langs->trans('ConfirmDeleteFile'),
				'confirm_deletefile',
				'',
				0,
				1
		);
		if ($ret == 'html') print '<br>';
    }




    // Affiche formulaire upload
    $formfile=new FormFile($db);
    $title=img_picto('','filenew').' '.$langs->trans("AttachANewFile");
    $formfile->form_attach_new_file($_SERVER["PHP_SELF"].'?socid='.$socid,$title,0,0,$user->rights->societe->creer, 40, $object, '', 1, '', 1);


    print '<table width="100%"><tr><td valign="top" width="100%">';
    print '<a name="builddoc"></a>'; // ancre

    /*
     * Documents generes
     */
    $filedir=$conf->societe->dir_output.'/'.$object->id;
    $urlsource=$_SERVER["PHP_SELF"]."?socid=".$object->id;
    $genallowed=$user->rights->societe->creer;
    $delallowed=$user->rights->societe->supprimer;

    $var=true;

    $title=img_picto('','filenew').' '.$langs->trans("GenerateADocument");
    //$somethingshown=$formfile->show_documents('company',$object->id,$filedir,$urlsource,$genallowed,$delallowed,'',0,0,0,64,0,'',$title,'',$object->default_lang,$hookmanager);
    print $formfile->showdocuments('company','','',$urlsource,$genallowed,$delallowed,'',0,0,0,64,0,'',$title,'',$object->default_lang,$hookmanager);

    // List of document
    print '<br><br>';
    $param='&socid='.$object->id;

    $formfilecabinetmed=new FormFileCabinetmed($db);
    $formfilecabinetmed->list_of_documents($filearray,$object,'societe',$param);

	print "<br>";

	//List of links
	$formfile->listOfLinks($object, $delallowed, $action, GETPOST('linkid', 'int'), $param);

    print '</td>';
    print '<td>';
    print '</td>';
    print '</tr>';
    print '</table>';

    print '<br>';


    /*
     * Action presend
     */
    if ($action == 'presend')
    {
        $fullpathfile=$upload_dir . '/' . GETPOST('urlfile');

        $withtolist=array();

        $lesTypes = $object->liste_type_contact('external', 'libelle', 1);

        // List of contacts
        foreach(array('external') as $source)
        {
            $tab = $object->liste_contact(-1,$source);
            $num=count($tab);

            $i = 0;
            while ($i < $num)
            {
                $contactstatic->id=$tab[$i]['id'];
                $contactstatic->civility=$tab[$i]['civility'];
                $contactstatic->name=$tab[$i]['lastname'];
                $contactstatic->firstname=$tab[$i]['firstname'];
                $name=$contactstatic->getFullName($langs,1);
                $email=$tab[$i]['email'];
                $withtolist[$contactstatic->id]=$name.' <'.$email.'>'.($tab[$i]['code']?' - '.(empty($lesTypes[$tab[$i]['code']])?'':$lesTypes[$tab[$i]['code']]):'');
                //print 'xx'.$withtolist[$email];
                $i++;
            }

        }

        print '<br>';
        print_titre($langs->trans('SendOutcomeByEmail'));

        // Create form object
        include_once(DOL_DOCUMENT_ROOT.'/core/class/html.formmail.class.php');
        $formmail = new FormMail($db);
        $formmail->fromtype = 'user';
        $formmail->fromid   = $user->id;
        $formmail->fromname = $user->getFullName($langs);
        $formmail->frommail = $user->email;
        $formmail->withfrom=1;

        $formmail->withto=$withtolist;
        $formmail->withtosocid=0;
        $formmail->withtocc=0;
        $formmail->withtoccsocid=0;
        $formmail->withtoccc=$conf->global->MAIN_EMAIL_USECCC;
        $formmail->withtocccsocid=0;
        $formmail->withtopic=$langs->trans('SendOutcome',$object->name);
        $formmail->withfile=2;
        $formmail->withbody=$langs->trans("ThisIsADocumentForYou");
        $formmail->withdeliveryreceipt=0;
        $formmail->withcancel=1;

        // Tableau des substitutions
        $formmail->substit['__NAME__']=$object->getFullAddress();
        $formmail->substit['__SIGNATURE__']=$user->signature;
        $formmail->substit['__PERSONALIZED__']='';
        // Tableau des parametres complementaires
        $formmail->param['action']='send';
        $formmail->param['models']='outcome_send';
        $formmail->param['socid']=$object->id;
        $formmail->param['returnurl']=$_SERVER["PHP_SELF"].'?socid='.$object->id;

        // Init list of files
        if (GETPOST("mode")=='init')
        {
            $formmail->clear_attached_files();
            $formmail->add_attached_files($fullpathfile,basename($fullpathfile),dol_mimetype($fullpathfile));
        }

        $formmail->show_form();

        print '<br>';
    }
}


llxFooter();

$db->close();
