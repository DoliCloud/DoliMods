<?php
//==========================================================================
//
//Université de Strasbourg - Direction Informatique
//Auteur : Guilhem BORGHESI
//Création : Février 2008
//
//borghesi@unistra.fr
//
//Ce logiciel est régi par la licence CeCILL-B soumise au droit français et
//respectant les principes de diffusion des logiciels libres. Vous pouvez
//utiliser, modifier et/ou redistribuer ce programme sous les conditions
//de la licence CeCILL-B telle que diffusée par le CEA, le CNRS et l'INRIA
//sur le site "http://www.cecill.info".
//
//Le fait que vous puissiez accéder à cet en-tête signifie que vous avez
//pris connaissance de la licence CeCILL-B, et que vous en avez accepté les
//termes. Vous pouvez trouver une copie de la licence dans le fichier LICENCE.
//
//==========================================================================
//
//Université de Strasbourg - Direction Informatique
//Author : Guilhem BORGHESI
//Creation : Feb 2008
//
//borghesi@unistra.fr
//
//This software is governed by the CeCILL-B license under French law and
//abiding by the rules of distribution of free software. You can  use,
//modify and/ or redistribute the software under the terms of the CeCILL-B
//license as circulated by CEA, CNRS and INRIA at the following URL
//"http://www.cecill.info".
//
//The fact that you are presently reading this means that you have had
//knowledge of the CeCILL-B license and that you accept its terms. You can
//find a copy of this license in the file LICENSE.
//
//==========================================================================

define("NOLOGIN",1);		// This means this output page does not require to be logged.
define("NOCSRFCHECK",1);	// We accept to go on this page from external web site.
$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");
if (! $res && file_exists("../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../dolibarr/htdocs/main.inc.php");     // Used on dev env only
if (! $res && file_exists("../../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../../dolibarr/htdocs/main.inc.php");   // Used on dev env only
if (! $res && file_exists("../../../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../../../dolibarr/htdocs/main.inc.php");   // Used on dev env only
if (! $res) die("Include of main fails");
require_once(DOL_DOCUMENT_ROOT."/core/lib/admin.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/files.lib.php");

include_once('../fonctions.php');
include_once('../bandeaux_local.php');

$langs->load("opensurvey@opensurvey");

$origin=GETPOST('origin','alpha');


// On teste toutes les variables pour supprimer l'ensemble des warnings PHP
// On transforme en entites html les données afin éviter les failles XSS
$post_var = array('titre', 'nom', 'adresse', 'commentaires', 'studsplus', 'mailsonde', 'creation_sondage_date', 'creation_sondage_date_x', 'creation_sondage_autre', 'creation_sondage_autre_x',);
foreach ($post_var as $var)
{
	$$var = GETPOST($var);
}

// On initialise egalement la session car sinon bonjour les warning :-)
$session_var = array('titre', 'nom', 'adresse', 'commentaires', 'mailsonde', 'studsplus', );
foreach ($session_var as $var) {
  if (issetAndNoEmpty($var, $_SESSION) === false) {
    $_SESSION[$var] = null;
  }
}

// On initialise également les autres variables
$erreur_adresse = false;
$erreur_injection_titre = false;
$erreur_injection_nom = false;
$erreur_injection_commentaires = false;
$cocheplus = '';
$cochemail = '';

#tests
if (issetAndNoEmpty("creation_sondage_date") || issetAndNoEmpty("creation_sondage_autre") || issetAndNoEmpty("creation_sondage_date_x") || issetAndNoEmpty("creation_sondage_autre_x"))
{
	$_SESSION["titre"] = $titre;
	$_SESSION["nom"] = $nom;
	$_SESSION["adresse"] = $adresse;
	$_SESSION["commentaires"] = $commentaires;

	unset($_SESSION["studsplus"]);
	if ($studsplus !== null) {
		$_SESSION["studsplus"] = '+';
	} else {
		$_SESSION["studsplus"] = '';
	}

	unset($_SESSION["mailsonde"]);
	if ($mailsonde !== null) {
		$_SESSION["mailsonde"] = true;
	} else {
		$_SESSION["mailsonde"] = false;
	}

	if(validateEmail($adresse) === false) {
		$erreur_adresse = true;
	}

	if (preg_match(';<|>|";',$titre)) {
		$erreur_injection_titre = true;
	}

	if (preg_match(';<|>|";',$nom)) {
		$erreur_injection_nom = true;
	}

	if (preg_match(';<|>|";',$commentaires)) {
		$erreur_injection_commentaires = true;
	}

	//var_dump($titre.' - '.$nom.' - '.$adresse.' - '.!$erreur_adresse.' - '.! $erreur_injection_titre.' - '.! $erreur_injection_commentaires.' - '.! $erreur_injection_nom.' - '.$creation_sondage_date.' - '.$creation_sondage_autre); exit;

	if ($titre && $nom && $adresse && !$erreur_adresse && ! $erreur_injection_titre && ! $erreur_injection_commentaires && ! $erreur_injection_nom)
	{
		if (! empty($creation_sondage_date))
		{
			header("Location: choix_date.php".($origin?'?origin='.$origin:''));
			exit();
		}

		if (! empty($creation_sondage_autre))
		{
			header("Location: choix_autre.php".($origin?'?origin='.$origin:''));
			exit();
		}
	}
}




/*
 * View
 */

$arrayofjs=array();
$arrayofcss=array('/opensurvey/css/style.css');
llxHeaderSurvey($langs->trans("OpenSurvey"), "", 0, 0, $arrayofjs, $arrayofcss);


print '<div class="bandeautitre">'. $langs->trans("CreatePoll").' (1 / 2)' .'</div>'."\n";


// premier sondage ? test l'existence des schémas SQL avant d'aller plus loin
if(!check_table_sondage())
{
	dol_print_error('',"STUdS is not properly installed, please check the 'INSTALL' to setup the database before continuing");
	exit;
}

//debut du formulaire
print '<form name="formulaire" action="infos_sondage.php" method="POST" onkeypress="javascript:process_keypress(event)">'."\n";
print '<input type="hidden" name="origin" value="'.dol_escape_htmltag($origin).'">';

print '<div class=corps>'."\n";
print '<br>'. $langs->trans("YouAreInPollCreateArea") .'<br><br>'."\n";

//Affichage des différents champs textes a remplir
print '<table>'."\n";

print '<tr><td class="fieldrequired">'. $langs->trans("PollTitle") .'</td><td><input type="text" name="titre" size="40" maxlength="80" value="'.$_SESSION["titre"].'"></td>'."\n";
if (!$_SESSION["titre"] && (issetAndNoEmpty('creation_sondage_date') || issetAndNoEmpty('creation_sondage_autre') || issetAndNoEmpty('creation_sondage_date_x') || issetAndNoEmpty('creation_sondage_autre_x'))) {
  print "<td><font color=\"#FF0000\">" . $langs->trans("FieldMandatory") . "</font></td>"."\n";
} elseif ($erreur_injection_titre) {
  print "<td><font color=\"#FF0000\">" . _("Characters < > and \" are not permitted") . "</font></td><br>"."\n";
}

print '</tr>'."\n";
print '<tr><td>'. $langs->trans("Description") .'</td><td><textarea name="commentaires" rows="7" cols="40">'.$_SESSION["commentaires"].'</textarea></td>'."\n";

if ($erreur_injection_commentaires) {
  print "<td><font color=\"#FF0000\">" . _("Characters < > and \" are not permitted") . "</font></td><br>"."\n";
}

print '</tr>'."\n";
print '<tr><td class="fieldrequired">'. $langs->trans("OpenSurveyYourName") .'</td><td>';

if (isset($_SERVER['REMOTE_USER'])) {
  print '<input type="hidden" name="nom" size="40" maxlength="40" value="'.$_SESSION["nom"].'">'.$_SESSION["nom"].'</td>'."\n";
} else {
  print '<input type="text" name="nom" size="40" maxlength="40" value="'.$_SESSION["nom"].'"></td>'."\n";
}

if (!$_SESSION["nom"] && (issetAndNoEmpty('creation_sondage_date') || issetAndNoEmpty('creation_sondage_autre') || issetAndNoEmpty('creation_sondage_date_x') || issetAndNoEmpty('creation_sondage_autre_x'))) {
  print "<td><font color=\"#FF0000\">" . $langs->trans("FieldMandatory")  . "</font></td>"."\n";
} elseif ($erreur_injection_nom) {
  print "<td><font color=\"#FF0000\">" . _("Characters < > and \" are not permitted") . "</font></td><br>"."\n";
}

print '</tr>'."\n";
print '<tr><td class="fieldrequired">'.  $langs->trans("OpenSurveyYourEMail")  .'</td><td>';

if (isset($_SERVER['REMOTE_USER'])) {
  print '<input type="hidden" name="adresse" size="40" maxlength="64" value="'.$_SESSION["adresse"].'">'.$_SESSION["adresse"].'</td>'."\n";
} else {
  print '<input type="text" name="adresse" size="40" maxlength="64" value="'.$_SESSION["adresse"].'"></td>'."\n";
}

if (!$_SESSION["adresse"] && (issetAndNoEmpty('creation_sondage_date') || issetAndNoEmpty('creation_sondage_autre') || issetAndNoEmpty('creation_sondage_date_x') || issetAndNoEmpty('creation_sondage_autre_x'))) {
  print "<td><font color=\"#FF0000\">" .$langs->trans("FieldMandatory")  . " </font></td>"."\n";
} elseif ($erreur_adresse && (issetAndNoEmpty('creation_sondage_date') || issetAndNoEmpty('creation_sondage_autre') || issetAndNoEmpty('creation_sondage_date_x') || issetAndNoEmpty('creation_sondage_autre_x'))) {
  print "<td><font color=\"#FF0000\">" . _("The address is not correct! (You should enter a valid email address in order to receive the link to your poll)") . "</font></td>"."\n";
}

print '</tr>'."\n";
print '</table>'."\n";

//focus javascript sur le premier champ
print '<script type="text/javascript">'."\n";
print 'document.formulaire.titre.focus();'."\n";
print '</script>'."\n";

print '<br>'."\n";

#affichage du cochage par défaut
if (!$_SESSION["studsplus"] && !issetAndNoEmpty('creation_sondage_date') && !issetAndNoEmpty('creation_sondage_autre') && !issetAndNoEmpty('creation_sondage_date_x') && !issetAndNoEmpty('creation_sondage_autre_x')) {
  $_SESSION["studsplus"]="+";
}

if ($_SESSION["studsplus"]=="+") {
  $cocheplus="checked";
}

print '<input type=checkbox name=studsplus '.$cocheplus.'>'. $langs->trans("VotersCanModify") .'<br>'."\n";

if ($_SESSION["mailsonde"]) {
  $cochemail="checked";
}

print '<input type=checkbox name=mailsonde '.$cochemail.'>'. $langs->trans("ToReceiveEMailForEachVote") .'<br>'."\n";

if (GETPOST('choix_sondage'))
{
	if (GETPOST('choix_sondage') == 'date') print '<input type="hidden" name="creation_sondage_date" value="date">';
	else print '<input type="hidden" name="creation_sondage_autre" value="autre">';
	print '<input type="hidden" name="choix_sondage" value="'.GETPOST('choix_sondage').'">';
	print '<br><input type="submit" class="button" name="submit" value="'.$langs->trans("CreatePoll").' ('.(GETPOST('choix_sondage') == 'date'?$langs->trans("TypeDate"):$langs->trans("TypeClassic")).')">';
}
else
{
	//affichage des boutons pour choisir sondage date ou autre
	print '<br><table >'."\n";
	print '<tr><td>'. _("Schedule an event") .'</td><td></td> '."\n";
	print '<td><input type="image" name="creation_sondage_date" value="Trouver une date" src="images/calendar-32.png"></td></tr>'."\n";
	print '<tr><td>'. _("Make a choice") .'</td><td></td> '."\n";
	print '<td><input type="image" name="creation_sondage_autre" value="'. _('Make a poll') . '" src="images/chart-32.png"></td></tr>'."\n";
	print '</table>'."\n";
}
print '<br><br><br>'."\n";
print '</div>'."\n";
print '</form>'."\n";

llxFooterSurvey();

$db->close();
?>
