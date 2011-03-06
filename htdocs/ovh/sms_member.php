<?php
/* Copyright (C) 2007-2011 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 *   	\file       ovh/sms_member.php
 *		\ingroup    ovh
 *		\brief
 */
$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../dolibarr/htdocs/main.inc.php");     // Used on dev env only
if (! $res && file_exists("../../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../../dolibarr/htdocs/main.inc.php");   // Used on dev env only
if (! $res && file_exists("../../../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../../../dolibarr/htdocs/main.inc.php");   // Used on dev env only
if (! $res) die("Include of main fails");
require_once(DOL_DOCUMENT_ROOT."/lib/member.lib.php");
require_once(DOL_DOCUMENT_ROOT."/adherents/class/adherent.class.php");
require_once(DOL_DOCUMENT_ROOT."/contact/class/contact.class.php");
dol_include_once("/ovh/class/ovhsms.class.php");

// Load traductions files requiredby by page
$langs->load("companies");
$langs->load("members");
$langs->load("ovh@ovh");

// Get parameters
$id = GETPOST("id");

// Protection if external user
if ($user->societe_id > 0)
{
	accessforbidden();
}



/*******************************************************************
 * ACTIONS
 ********************************************************************/

/* Envoi d'un SMS */
if (GETPOST("action") == 'smsenvoi' && $user->rights->ovhsms->envoyer)
{

	$sms = new OvhSms($db);
	$sms->expe = $_POST['expe'];
	$sms->dest = $_POST['dest'];
	$sms->message = $_POST['message'];
	$sms->deferred = $_POST['deferred'];
	$resultsend = $sms->SmsSend();
	if ($resultsend > 0)
	{

		$mesg = '<p class="ok">Message correctement envoyé à '.$sms->dest.' sous la référence '.$resultsend.'</p>';
	}

	else $mesg = '<p class="error">'.$sms->error.'</p>';
}




/***************************************************
 * View
 ****************************************************/

llxHeader('','Ovh','');

$form=new Form($db);


if ($id)
{
    if (empty($conf->global->OVHSMS_SOAPURL))
    {
        $langs->load("errors");
        $mesg='<div class="error">'.$langs->trans("ErrorModuleSetupNotComplete").'</div>';
    }

	$sms = new OvhSms($db);

	/*
	 * Creation de l'objet adherent correspondant a id
	 */

	$member = new Adherent($db);
	$result = $member->fetch($id);


	/*
	 * Affichage onglets
	 */
	$head = member_prepare_head($member);
	dol_fiche_head($head, 'tabSMS', $langs->trans("Member"),0,'company');


	if ($mesg) print $mesg."<br>";


    // Cree l'objet formulaire mail
    include_once(DOL_DOCUMENT_ROOT.'/core/class/html.formsms.class.php');
    $formsms = new FormSms($db);
    $formsms->fromtype = 'user';
    $formsms->fromid   = $user->id;
    $formsms->fromname = $user->getFullName($langs);
    $formsms->fromsms = $user->user_mobile;
    $formsms->withfrom=1;
    $formsms->withfromreadonly=0;
    $formsms->withto=empty($_POST["sendto"])?1:$_POST["sendto"];
    $formsms->withbody=1;
    $formsms->withcancel=0;
    // Tableau des substitutions
    $formsms->substit['__FACREF__']=$object->ref;
    // Tableau des parametres complementaires du post
    $formsms->param['action']=$action;
    $formsms->param['models']=$modelmail;
    $formsms->param['facid']=$object->id;
    $formsms->param['returnurl']=$_SERVER["PHP_SELF"].'?id='.$object->id;

    $formsms->show_form();


	dol_fiche_end();
}


// End of page
$db->close();
llxFooter('');
?>
