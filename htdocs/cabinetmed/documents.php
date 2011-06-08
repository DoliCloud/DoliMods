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
 *   \version    $Id: documents.php,v 1.3 2011/06/08 16:42:54 eldy Exp $
 */

$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../dolibarr/htdocs/main.inc.php");     // Used on dev env only
if (! $res && file_exists("../../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../../dolibarr/htdocs/main.inc.php");   // Used on dev env only
if (! $res && file_exists("../../../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../../../dolibarr/htdocs/main.inc.php");   // Used on dev env only
if (! $res) die("Include of main fails");
include_once(DOL_DOCUMENT_ROOT."/lib/company.lib.php");
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

$mesgarray=array();

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

/*
 * Generate document
 */
if (GETPOST('action') == 'builddoc')  // En get ou en post
{
    if (is_numeric(GETPOST('model')))
    {
        $mesg=$langs->trans("ErrorFieldRequired",$langs->transnoentities("Model"));
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
        $result=patientoutcomes_doc_create($db, $soc->id, '', $_REQUEST['model'], $outputlangs);
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

llxHeader('',$langs->trans("Consultation"));

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

	print "</table>";

	print '</form>';


	dol_fiche_end();


	print '<table width="100%"><tr><td valign="top" width="100%">';
    print '<a name="builddoc"></a>'; // ancre

    /*
     * Documents generes
     */
    $filedir=$conf->cabinetmed->dir_output.'/'.$societe->id;
    $urlsource=$_SERVER["PHP_SELF"]."?socid=".$societe->id;
    $genallowed=$user->rights->societe->creer;
    $delallowed=$user->rights->societe->supprimer;

    $var=true;

    $instance=new CabinetmedCons($db);
    $instance->fk_soc=$societe->id;
    $hooks=array('objectcard'=>$instance);
    $somethingshown=$formfile->show_documents('cabinetmed',$soc->id,$filedir,$urlsource,$genallowed,$delallowed,'',0,0,0,28,0,'',0,'',$soc->default_lang,$hooks);

    print '</td>';
    print '<td>';
    print '</td>';
    print '</tr>';
    print '</table>';

    print '<br>';
}



$db->close();

llxFooter('$Date: 2011/06/08 16:42:54 $ - $Revision: 1.3 $');
?>
