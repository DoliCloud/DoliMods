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

// Security check
if (!$user->admin)
	accessforbidden();


include_once('./variables.php');
include_once('./fonctions.php');
include_once('./bandeaux.php');

// Ce fichier index.php se trouve dans le sous-repertoire ADMIN de Studs. Il sert à afficher l'intranet de studs
// pour modifier les sondages directement sans avoir reçu les mails. C'est l'interface d'aministration
// de l'application.


// Affichage des balises standards
/*echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN">'."\n";
 echo '<html>'."\n";
echo '<head>'."\n";
echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">'."\n";
echo '<title>ADMINISTRATEUR de la base '.NOMAPPLICATION.'</title>'."\n";
echo '<link rel="stylesheet" type="text/css" href="../style.css">'."\n";
echo '</head>'."\n";
echo '<body>'."\n";

//Affichage des bandeaux et début du formulaire
logo();
bandeau_tete();
bandeau_titre(_("Polls administrator"));
sous_bandeau_admin();
*/
$langs->load("opensurvey@opensurvey");
llxHeader();


$sondage=$connect->Execute('select * from '.MAIN_DB_PREFIX.'opensurvey_sondage');

echo '<div class=corps>'."\n";

echo '<form action="'.$_SERVER["PHP_SELF"].'" method="POST">'."\n";
// Test et affichage du bouton de confirmation en cas de suppression de sondage
$i=0;
while($dsondage = $sondage->FetchNextObject(false))
{
	if ($_POST["supprimersondage$i"])
	{
		echo '<table>'."\n";
		echo '<tr><td bgcolor="#EE0000" colspan="11">'. _("Confirm removal of the poll ") .'"'.$dsondage->id_sondage.'" : <input type="submit" name="confirmesuppression'.$i.'" value="'. _("Remove this poll!") .'">'."\n";
		echo '<input type="submit" name="annullesuppression" value="'. _("Keep this poll!") .'"></td></tr>'."\n";
		echo '</table>'."\n";
		echo '<br>'."\n";
	}

	// Traitement de la confirmation de suppression
	if ($_POST["confirmesuppression$i"])
	{
		// requetes SQL qui font le ménage dans la base
		$sql='DELETE FROM '.MAIN_DB_PREFIX."opensurvey_comments WHERE id_sondage = '".$dsondage->id_sondage."'";
		dol_syslog("Delete poll sql=".$sql, LOG_DEBUG);
		$connect->Execute($sql);
		$sql='DELETE FROM '.MAIN_DB_PREFIX."opensurvey_sujet_studs WHERE id_sondage = '".$dsondage->id_sondage."'";
		dol_syslog("Delete poll sql=".$sql, LOG_DEBUG);
		$connect->Execute($sql);
		$sql='DELETE FROM '.MAIN_DB_PREFIX."opensurvey_user_studs WHERE id_sondage = '".$dsondage->id_sondage."'";
		dol_syslog("Delete poll sql=".$sql, LOG_DEBUG);
		$connect->Execute($sql);
		$sql='DELETE FROM '.MAIN_DB_PREFIX."opensurvey_sondage WHERE id_sondage = '".$dsondage->id_sondage."'";
		dol_syslog("Delete poll sql=".$sql, LOG_DEBUG);
		$connect->Execute($sql);
	}

	$i++;
}

$sondage=$connect->Execute('select * from '.MAIN_DB_PREFIX.'opensurvey_sondage');
$nbsondages=$sondage->RecordCount();

print_fiche_titre($langs->trans("OpenSurveyArea"));

echo $langs->trans("NoSurveysInDatabase",$nbsondages).'<br><br>'."\n";

// tableau qui affiche tous les sondages de la base
echo '<table class="liste">'."\n";
echo '<tr class="liste_titre"><td>'. _("Poll ID") .'</td><td>'. _("Format") .'</td><td>'. _("Title") .'</td><td>'. _("Author") .'</td><td align="center">'. _("Expiration's date") .'</td><td>'. _("Users") .'</td><td colspan=3>'. _("Actions") .'</td>'."\n";

$i = 0; $var = true;
while($dsondage = $sondage->FetchNextObject(false))
{
	/* possible en 1 bonne requête dans $sondage */
	$sujets=$connect->Execute( 'select * from '.MAIN_DB_PREFIX."opensurvey_sujet_studs where id_sondage='".$dsondage->id_sondage."'");
	$dsujets=$sujets->FetchObject(false);

	$user_studs=$connect->Execute( 'select * from '.MAIN_DB_PREFIX."opensurvey_user_studs where id_sondage='".$dsondage->id_sondage."'");
	$nbuser=$user_studs->RecordCount();

	$var=!$var;
	echo '<tr '.$bc[$var].'>';
	print '<td>';
	print '<a href="'.dol_buildpath('/opensurvey/adminstuds.php',1).'?sondage='.$dsondage->id_sondage_admin.'">'.$dsondage->id_sondage.'</a>';
	print '</td><td>'.$dsondage->format.'</td><td>'.$dsondage->titre.'</td><td>'.$dsondage->nom_admin.'</td>';

	if (strtotime($dsondage->date_fin) > time()) {
		echo '<td align="center">'.dol_print_date($db->jdate($dsondage->date_fin),'day').'</td>';
	} else {
		echo '<td align="center"><font color=#FF0000>'.dol_print_date($db->jdate($dsondage->date_fin),'day').'</font></td>';
	}

	echo'<td>'.$nbuser.'</td>'."\n";
	echo '<td><a href="'.getUrlSondage($dsondage->id_sondage_admin, true).'">'. _("Change the poll") .'</a></td>'."\n";
	echo '<td align="right"><input type="submit" class="button" name="supprimersondage'.$i.'" value="'. _("Remove the poll") .'"></td>'."\n";

	echo '</tr>'."\n";
	$i++;
}

echo '</table>'."\n";
echo'</div>'."\n";
// fin du formulaire et de la page web
echo '</form>'."\n";
echo '</body>'."\n";
echo '</html>'."\n";

// si on annule la suppression, rafraichissement de la page
if ($_POST["annulesuppression"]) {
}