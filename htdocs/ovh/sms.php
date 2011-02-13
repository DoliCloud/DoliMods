<?php
/* Copyright (C) 2007 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2010 Jean-Francois FERRY  <jfefe@aternatik.fr>
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
 *   	\file       ovh/sms.php
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
require_once(DOL_DOCUMENT_ROOT."/lib/company.lib.php");
require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
require_once(DOL_DOCUMENT_ROOT."/contact/class/contact.class.php");
dol_include_once("/ovh/class/ovhsms.class.php");

// Load traductions files requiredby by page
$langs->load("companies");
$langs->load("ovh@ovh");

// Get parameters
$socid = GETPOST("id");

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


if ($socid)
{
    if (empty($conf->global->OVHSMS_SOAPURL))
    {
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


	if ($mesg) print $mesg."<br>";


	print '<table width="100%" class="notopnoleftnoright">';
	print '<tr><td valign="top" class="notopnoleft">';



	// Liste des expéditeurs autorisés
	$resultsender = $sms->SmsSenderList($account);

	print_titre($langs->trans("OvhSmsSend"));
	print '
<script language="javascript">
function limitChars(textarea, limit, infodiv)
{
	var text = textarea.value;
	var textlength = text.length;
	var info = document.getElementById(infodiv);

	if(textlength > limit)
	{
		info.innerHTML = \'Le texte doit faire \'+limit+\' caractères maximum  !\';
		textarea.value = text.substr(0,limit);
		return false;
	}
	else
	{
		info.innerHTML = \' \'+ (limit - textlength) +\' caractères restants.\';
		return true;
	}
}
</script>';

	print '<form method="post" action="">';
	print '<input type="hidden" name="action" value="smsenvoi">';
	print '<table  class="border" width="100%">';
	print '<tr>
   <td>Expediteur</td>
   <td><select name="expe" id="valid" size="1">';

	$i=0;
	while($resultsender[$i]){
		print '<option value="'.$resultsender[$i]->number.'">'.$resultsender[$i]->number.'</option>';
		$i++;
	}
	print '</select></td>
	  ';



	print ' ';

	print '</tr>
   <tr>
	   <td>'.$langs->trans("OvhSmsDestinataire").'</td>
	   <td><input type="text" name="dest" size="15" value="+"><br />'.$langs->trans("OvhSmsInfoNumero").'</td>
   </tr>
   <tr>
	   <td>'.$langs->trans("OvhSmsLabelTexteMessage").'</td>
	   <td><textarea  cols="30" name="message" id="message" rows="4" onkeyup="limitChars(this, 160, \'charlimitinfo\')"> </textarea>
	   <div id="charlimitinfo" style="  color:#aa3333; font-size:12px; font-family:vardana" >'.$langs->trans("OvhSmsInfoCharMax").'</div></td>
   </tr>

   <tr>
   	<td>Envoi differe :</td>
   	<td> <input name="deferred" id="deferred" size="4" value="0"> (Délai avant envoi en Minutes.)</td></tr>

      <tr><td>Type SMS : </td><td>
   <select name="class" id="valid" size="1">
   <option value="0">Flash</option>
   <option value="1" selected="selected">Standard</option>
   <option value="2">SIM</option>
   <option value="3">ToolKit</option>
   </select></td></tr>

   <tr><td>Priorite :</td><td>
   <select name="class" id="valid" size="1">
   <option value="0">0</option>
   <option value="1">1</option>
   <option value="2">2</option>
   <option value="3" selected="selected">3</option>
   </select></td></tr>

   </table>


   <br /><input type="submit" name="Submit" value="'.dol_escape_htmltag($langs->trans("OvhSmsSend")).'" class="button"><br />
   </form>
   ';






	print "</td>\n";


	print '<td valign="top" width="50%" class="notopnoleftnoright">';



	$sms->show_sms_contacts($conf,$langs,$db,$soc);



	print '</div>';
	print "</td></tr>";
	print "</table></div>\n";




}




// }





// End of page
$db->close();
llxFooter('');
?>
