<?php
/* Copyright (C) 2004-2011      Laurent Destailleur  <eldy@users.sourceforge.net>
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
 *   \file       htdocs/cabinetmed/documents.php
 *   \brief      Tab for courriers
 *   \ingroup    cabinetmed
 *   \version    $Id: documents.php,v 1.9 2011/06/30 22:59:00 eldy Exp $
 */

$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../dolibarr/htdocs/main.inc.php");     // Used on dev env only
if (! $res && file_exists("../../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../../dolibarr/htdocs/main.inc.php");   // Used on dev env only
if (! $res && file_exists("../../../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../../../dolibarr/htdocs/main.inc.php");   // Used on dev env only
if (! $res) die("Include of main fails");
include_once(DOL_DOCUMENT_ROOT."/lib/company.lib.php");
require_once(DOL_DOCUMENT_ROOT."/lib/files.lib.php");
require_once(DOL_DOCUMENT_ROOT."/lib/images.lib.php");
include_once(DOL_DOCUMENT_ROOT."/compta/bank/class/account.class.php");
require_once(DOL_DOCUMENT_ROOT."/core/class/html.formfile.class.php");
include_once("./lib/cabinetmed.lib.php");
include_once("./class/patient.class.php");
include_once("./class/cabinetmedcons.class.php");

$action = GETPOST("action");
$id=GETPOST("id");  // Id consultation

$langs->load("companies");
$langs->load("bills");
$langs->load("banks");
$langs->load("cabinetmed@cabinetmed");

// Security check
$socid = GETPOST("socid");
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
if (! $sortfield) $sortfield='t.datecons';
if (! $sortorder) $sortorder='DESC';
$limit = $conf->liste_limit;

$now=dol_now();

$consult = new CabinetmedCons($db);

$upload_dir = $conf->societe->dir_output . "/" . $socid ;

// Instantiate hooks of thirdparty module
/*if (is_array($conf->hooks_modules) && !empty($conf->hooks_modules))
{
    // If module has hook for hook 'objectcard', then this add on object, the property ->hooks['objectcard'][module_number]
    // with value that is instance of an action class.
    $consult->callHooks('objectcard');
}*/


/*
 * Actions
 */


// Envoie fichier
if ( $_POST["sendit"] && ! empty($conf->global->MAIN_UPLOAD_DOC))
{
    require_once(DOL_DOCUMENT_ROOT."/lib/files.lib.php");

    if (create_exdir($upload_dir) >= 0)
    {
        $resupload=dol_move_uploaded_file($_FILES['userfile']['tmp_name'], $upload_dir . "/" . $_FILES['userfile']['name'],0,0,$_FILES['userfile']['error']);
        if (is_numeric($resupload) && $resupload > 0)
        {
            if (image_format_supported($upload_dir . "/" . $_FILES['userfile']['name']) == 1)
            {
                // Create small thumbs for company (Ratio is near 16/9)
                // Used on logon for example
                $imgThumbSmall = vignette($upload_dir . "/" . $_FILES['userfile']['name'], $maxwidthsmall, $maxheightsmall, '_small', $quality, "thumbs");

                // Create mini thumbs for company (Ratio is near 16/9)
                // Used on menu or for setup page for example
                $imgThumbMini = vignette($upload_dir . "/" . $_FILES['userfile']['name'], $maxwidthmini, $maxheightmini, '_mini', $quality, "thumbs");
            }
            $mesg = '<div class="ok">'.$langs->trans("FileTransferComplete").'</div>';
        }
        else
        {
            $langs->load("errors");
            if (is_numeric($resupload) && $resupload < 0)   // Unknown error
            {
                $errors[] = '<div class="error">'.$langs->trans("ErrorFileNotUploaded").'</div>';
            }
            else if (preg_match('/ErrorFileIsInfectedWithAVirus/',$resupload))  // Files infected by a virus
            {
                $errors[] = '<div class="error">'.$langs->trans("ErrorFileIsInfectedWithAVirus").'</div>';
            }
            else    // Known error
            {
                $errors[] = '<div class="error">'.$langs->trans($resupload).'</div>';
            }
        }
    }
}


/*
 * Generate document
 */
if (GETPOST('action') == 'builddoc')  // En get ou en post
{
    if (is_numeric(GETPOST('model')))
    {
        $errors[]=$langs->trans("ErrorFieldRequired",$langs->transnoentities("Model"));
    }
    else
    {
        require_once(DOL_DOCUMENT_ROOT.'/includes/modules/societe/modules_societe.class.php');

        $soc = new Societe($db);
        $soc->fetch($socid);
        $soc->fetch_thirdparty();

        $consult = new CabinetmedCons($db);
        $soc->fetch($id);

        // Define output language
        $outputlangs = $langs;
        $newlang='';
        if ($conf->global->MAIN_MULTILANGS && empty($newlang) && ! empty($_REQUEST['lang_id'])) $newlang=$_REQUEST['lang_id'];
        //if ($conf->global->MAIN_MULTILANGS && empty($newlang)) $newlang=$fac->client->default_lang;
        if (! empty($newlang))
        {
            $outputlangs = new Translate("",$conf);
            $outputlangs->setDefaultLang($newlang);
        }
        $result=thirdparty_doc_create($db, $soc->id, '', $_REQUEST['model'], $outputlangs);
        if ($result <= 0)
        {
            dol_print_error($db,$result);
            exit;
        }
        else
        {
            Header ('Location: '.$_SERVER["PHP_SELF"].'?socid='.$soc->id.(empty($conf->global->MAIN_JUMP_TAG)?'':'#builddoc'));
            exit;
        }
    }
}



/*
 *	View
 */

$form = new Form($db);
$formfile = new FormFile($db);
$width="242";

llxHeader('',$langs->trans("Courriers"));

if ($socid > 0)
{
    $societe = new Societe($db);
    $societe->fetch($socid);

    if ($id && ! $consult->id)
    {
        $result=$consult->fetch($id);
        if ($result < 0) dol_print_error($db,$consult->error);

        $result=$consult->fetch_bankid();
        if ($result < 0) dol_print_error($db,$consult->error);
    }

	/*
	 * Affichage onglets
	 */
    if ($conf->notification->enabled) $langs->load("mails");

	$head = societe_prepare_head($societe);
	dol_fiche_head($head, 'tabdocument', $langs->trans("ThirdParty"),0,'company');


    // Construit liste des fichiers
    $filearray=dol_dir_list($upload_dir,"files",0,'','\.meta$',$sortfield,(strtolower($sortorder)=='desc'?SORT_ASC:SORT_DESC),1);
    $totalsize=0;
    foreach($filearray as $key => $file)
    {
        $totalsize+=$file['size'];
    }

	print "<form method=\"post\" action=\"".$_SERVER["PHP_SELF"]."\">";
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';

	print '<table class="border" width="100%">';

	print '<tr><td width="25%">'.$langs->trans('ThirdPartyName').'</td>';
	print '<td colspan="3">';
	print $form->showrefnav($societe,'socid','',($user->societe_id?0:1),'rowid','nom');
	print '</td></tr>';

    if ($societe->client)
    {
        print '<tr><td>';
        print $langs->trans('CustomerCode').'</td><td colspan="3">';
        print $societe->code_client;
        if ($societe->check_codeclient() <> 0) print ' <font class="error">('.$langs->trans("WrongCustomerCode").')</font>';
        print '</td></tr>';
    }

    if ($societe->fournisseur)
    {
        print '<tr><td>';
        print $langs->trans('SupplierCode').'</td><td colspan="3">';
        print $societe->code_fournisseur;
        if ($societe->check_codefournisseur() <> 0) print ' <font class="error">('.$langs->trans("WrongSupplierCode").')</font>';
        print '</td></tr>';
    }

    // Nbre fichiers
    print '<tr><td>'.$langs->trans("NbOfAttachedFiles").'</td><td colspan="3">'.sizeof($filearray).'</td></tr>';

    //Total taille
    print '<tr><td>'.$langs->trans("TotalSizeOfAttachedFiles").'</td><td colspan="3">'.$totalsize.' '.$langs->trans("bytes").'</td></tr>';

    print "</table>";

	print '</form>';

	dol_fiche_end();

    if ($mesg) dol_htmloutput_mesg($mesg,'','ok');
	else dol_htmloutput_mesg($error,$errors,'error');

    // Affiche formulaire upload
    $formfile=new FormFile($db);
    $formfile->form_attach_new_file($_SERVER["PHP_SELF"].'?socid='.$socid,'',0,0,$user->rights->societe->creer);



	print '<table width="100%"><tr><td valign="top" width="100%">';
    print '<a name="builddoc"></a>'; // ancre

    /*
     * Documents generes
     */
    $filedir=$conf->societe->dir_output.'/'.$societe->id;
    $urlsource=$_SERVER["PHP_SELF"]."?socid=".$societe->id;
    $genallowed=$user->rights->societe->creer;
    $delallowed=$user->rights->societe->supprimer;

    $var=true;

    $instance=new CabinetmedCons($db);
    $instance->fk_soc=$societe->id;
    $hooks=array(0=>array('modules'=>array($instance)));
    $somethingshown=$formfile->show_documents('company',$societe->id,$filedir,$urlsource,$genallowed,$delallowed,'',0,0,0,64,0,'',0,'',$societe->default_lang,$hooks);

    print '</td>';
    print '<td>';
    print '</td>';
    print '</tr>';
    print '</table>';

    print '<br>';
}



$db->close();

llxFooter('$Date: 2011/06/30 22:59:00 $ - $Revision: 1.9 $');
?>
