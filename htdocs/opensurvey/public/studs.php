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
dol_include_once("/opensurvey/class/opensurveysondage.class.php");

include_once('../bandeaux_local.php');
include_once('../fonctions.php');

// Init vars
$numsondageadmin=GETPOST("sondage");
$numsondage=substr($numsondageadmin, 0, 16);
$object=new Opensurveysondage($db);
$object->fetch(0,$numsondageadmin);

if ($numsondage !== false) {
	$dsondage = get_sondage_from_id($numsondage);
	if($dsondage === false) {
		$err |= NO_POLL;
	}
} else {
	$err |= NO_POLL_ID;
}

$nbcolonnes = substr_count($dsondage->sujet, ',') + 1;


/*
 * Actions
 */

$listofvoters=explode(',',$_SESSION["savevoter"]);

// Add comment
if (isset($_POST['ajoutcomment']) || isset($_POST['ajoutcomment_x']))
{
	if (isset($_SESSION['nom'])) {
		// Si le nom vient de la session, on le de-htmlentities
		$comment_user = $_SESSION['nom'];
	} elseif(issetAndNoEmpty('commentuser')) {
		$comment_user = $_POST["commentuser"];
	} elseif(isset($_POST["commentuser"])) {
		$err |= COMMENT_USER_EMPTY;
	} else {
		$comment_user = _('anonyme');
	}

	if(issetAndNoEmpty('comment') === false) {
		$err |= COMMENT_EMPTY;
	}

	if (isset($_POST["comment"]) && !is_error(COMMENT_EMPTY) && !is_error(NO_POLL) && !is_error(COMMENT_USER_EMPTY)) {
		// protection contre les XSS : htmlentities
		$comment = GETPOST('comment');

		$sql = 'INSERT INTO '.MAIN_DB_PREFIX.'opensurvey_comments (id_sondage, comment, usercomment) VALUES ('.
			$connect->Param('id_sondage').','.
			$connect->Param('comment').','.
			$connect->Param('comment_user').')';

		$sql = $connect->Prepare($sql);

		$comments = $connect->Execute($sql, array($numsondage, $comment, $comment_user));

		if ($comments === false) {
			$err |= COMMENT_INSERT_FAILED;
		}
	}
}


// Add vote
if (isset($_POST["boutonp"]) || isset($_POST["boutonp_x"]))
{
	//Si le nom est bien entré
	if (issetAndNoEmpty('nom'))
	{
		$nouveauchoix = '';
		for ($i=0;$i<$nbcolonnes;$i++)
		{
			if (isset($_POST["choix$i"]) && $_POST["choix$i"] == '1')
			{
				$nouveauchoix.="1";
			}
			else if (isset($_POST["choix$i"]) && $_POST["choix$i"] == '2')
			{
				$nouveauchoix.="2";
			}
			else { // sinon c'est 0
				$nouveauchoix.="0";
			}
		}

		$nom=substr($_POST["nom"],0,64);


		$sql = 'SELECT * FROM '.MAIN_DB_PREFIX.'opensurvey_user_studs WHERE id_sondage='.$connect->Param('numsondage').' ORDER BY id_users';
		$sql = $connect->Prepare($sql);
		$user_studs = $connect->Execute($sql, array($numsondage));
		while($tmpuser = $user_studs->FetchNextObject(false)) {
			if ($nom == $tmpuser->nom) {
				$err |= NAME_TAKEN;
			}
		}

		// Ecriture des choix de l'utilisateur dans la base
		if (!is_error(NAME_TAKEN) && !is_error(NAME_EMPTY)) {
			$sql = 'INSERT INTO '.MAIN_DB_PREFIX.'opensurvey_user_studs (nom,id_sondage,reponses) VALUES ('.
				$connect->Param('nom').', '.
				$connect->Param('numsondage').', '.
				$connect->Param('nouveauchoix').')';
			$sql = $connect->Prepare($sql);

			$connect->Execute($sql, array($nom, $numsondage, $nouveauchoix));

			// Add voter to session
			$_SESSION["savevoter"]=$nom.','.(empty($_SESSION["savevoter"])?'':$_SESSION["savevoter"]);	// Save voter
			$listofvoters=explode(',',$_SESSION["savevoter"]);


			if ($dsondage->mailsonde || /* compatibility for non boolean DB */ $dsondage->mailsonde=="yes" || $dsondage->mailsonde=="true")
			{
				// TODO Use CMailFile
				/*
				$headers="From: ".NOMAPPLICATION." <".ADRESSEMAILADMIN.">\r\nContent-Type: text/plain; charset=\"UTF-8\"\nContent-Transfer-Encoding: 8bit";
				mail ("$dsondage->mail_admin",
					"[".NOMAPPLICATION."] "._("Poll's participation")." : $dsondage->titre",
					"\"$nom\" ".
					_("has filled a line.\nYou can find your poll at the link") . " :\n\n".
					getUrlSondage($numsondage)." \n\n" .
					_("Thanks for your confidence.") . "\n". NOMAPPLICATION,
					$headers);
				*/
			}
		}
	} else {
		$err |= NAME_EMPTY;
	}
}

// Update vote
$sql = 'SELECT * FROM '.MAIN_DB_PREFIX.'opensurvey_user_studs WHERE id_sondage='.$connect->Param('numsondage').' ORDER BY id_users';
$sql = $connect->Prepare($sql);
$user_studs = $connect->Execute($sql, array($numsondage));
$nblignes = $user_studs->RecordCount();
$testmodifier = false;
$ligneamodifier = -1;
for ($i=0; $i<$nblignes; $i++)
{
	if (isset($_POST['modifierligne'.$i])) {
		$ligneamodifier = $i;
	}

	//test pour voir si une ligne est a modifier
	if (isset($_POST['validermodifier'.$i])) {
		$modifier = $i;
		$testmodifier = true;
	}
}
if ($testmodifier)
{
	//var_dump($_POST);exit;
	$nouveauchoix = '';
	for ($i=0;$i<$nbcolonnes;$i++)
	{
		//var_dump($_POST["choix$i"]);
		if (isset($_POST["choix$i"]) && $_POST["choix$i"] == '1')
		{
			$nouveauchoix.="1";
		}
		else if (isset($_POST["choix$i"]) && $_POST["choix$i"] == '2')
		{
			$nouveauchoix.="2";
		}
		else { // sinon c'est 0
			$nouveauchoix.="0";
		}
	}

	$compteur=0;
	while ($data = $user_studs->FetchNextObject(false) )
	{
		if ($compteur == $modifier)
		{
			$sql = 'UPDATE '.MAIN_DB_PREFIX."opensurvey_user_studs SET reponses = '".$db->escape($nouveauchoix)."' WHERE nom = '".$db->escape($data->nom)."' AND id_users = '".$db->escape($data->id_users)."'";
			$resql = $db->query($sql);
			if ($resql <= 0)
			{
				dol_print_error($db);
				exit;
			}

			if ($dsondage->mailsonde=="yes")
			{
				// TODO Use CMailFile
				//$headers="From: ".NOMAPPLICATION." <".ADRESSEMAILADMIN.">\r\nContent-Type: text/plain; charset=\"UTF-8\"\nContent-Transfer-Encoding: 8bit";
				//mail ("$dsondage->mail_admin", "[".NOMAPPLICATION."] " . _("Poll's participation") . " : $dsondage->titre", "\"$data->nom\""."" . _("has filled a line.\nYou can find your poll at the link") . " :\n\n".getUrlSondage($numsondage)." \n\n" . _("Thanks for your confidence.") . "\n".NOMAPPLICATION,$headers);
			}
		}

		$compteur++;
	}
}



/*
 * View
 */

$form=new Form($db);
$object=new OpenSurveySondage($db);

$arrayofjs=array('/opensurvey/block_enter.js');
$arrayofcss=array('/opensurvey/css/style.css');
llxHeaderSurvey($dsondage->titre, "", 0, 0, $arrayofjs, $arrayofcss);

$res=$object->fetch(0,$numsondage);

if ($res <= 0)
{
	print $langs->trans("ErrorPollDoesNotExists",$numsondage);
	llxFooterSurvey();
	exit;
}

// Define format of choices
$toutsujet=explode(",",$object->sujet);
$toutsujet=str_replace("°","'",$toutsujet);

$listofanswers=array();
foreach ($toutsujet as $value)
{
	$tmp=explode('@',$value);
	$listofanswers[]=array('label'=>$tmp[0],'format'=>($tmp[1]?$tmp[1]:'checkbox'));
}

// Show error message
if ($err != 0)
{
	print '<div class="error"><ul style="list-style-type: none;">'."\n";
	if(is_error(NAME_EMPTY)) {
		print '<li class="error">' . $langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Name")) . "</li>\n";
	}
	if(is_error(NAME_TAKEN)) {
		print '<li class="error">' . $langs->trans("VoteNameAlreadyExists") . "</li>\n";
	}
	if(is_error(COMMENT_EMPTY) || is_error(COMMENT_USER_EMPTY)) {
		print '<li class="error">' . $langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Name")) . "</li>\n";
	}
	print '</ul></div>';
}


print '<div class="survey_invitation">'.$langs->trans("YouAreInivitedToVote").'</div>';
print $langs->trans("OpenSurveyHowTo").'<br><br>';

print '<div class="corps"> '."\n";

//affichage du titre du sondage
$titre=str_replace("\\","",$dsondage->titre);
print '<strong>'.$titre.'</strong><br>'."\n";

//affichage du nom de l'auteur du sondage
print $langs->trans("InitiatorOfPoll") .' : '.$dsondage->nom_admin.'<br>'."\n";

//affichage des commentaires du sondage
if ($dsondage->commentaires) {
	print '<br>'.$langs->trans("Description") .' :<br>'."\n";
	$commentaires=dol_nl2br($dsondage->commentaires);
	print $commentaires;
	print '<br>'."\n";
}

print '</div>'."\n";

print '<form name="formulaire" action="studs.php?sondage='.$numsondage.'"'.'#bas" method="POST" onkeypress="javascript:process_keypress(event)">'."\n";
print '<input type="hidden" name="sondage" value="' . $numsondage . '"/>';
// Todo : add CSRF protection
print '<div class="cadre"> '."\n";
print '<br><br>'."\n";

// Debut de l'affichage des resultats du sondage
print '<table class="resultats">'."\n";

//recuperation des utilisateurs du sondage
$sql = 'SELECT * FROM '.MAIN_DB_PREFIX.'opensurvey_user_studs WHERE id_sondage='.$connect->Param('numsondage').' ORDER BY id_users';
$sql = $connect->Prepare($sql);
$user_studs = $connect->Execute($sql, array($numsondage));

//si le sondage est un sondage de date
if ($dsondage->format=="D"||$dsondage->format=="D+")
{
	//affichage des sujets du sondage
	print '<tr>'."\n";
	print '<td></td>'."\n";

	//affichage des années
	$colspan=1;
	for ($i=0;$i<count($toutsujet);$i++)
	{
		if (isset($toutsujet[$i+1]) && date('Y', intval($toutsujet[$i])) == date('Y', intval($toutsujet[$i+1]))) {
			$colspan++;
		} else {
			print '<td colspan='.$colspan.' class="annee">'.date('Y', intval($toutsujet[$i])).'</td>'."\n";
			$colspan=1;
		}
	}

	print '</tr>'."\n";
	print '<tr>'."\n";
	print '<td></td>'."\n";

	//affichage des mois
	$colspan=1;
	for ($i=0;$i<count($toutsujet);$i++) {
		$cur = intval($toutsujet[$i]);	// intval() est utiliser pour supprimer le suffixe @* qui déplaît logiquement à strftime()

		if (isset($toutsujet[$i+1]) === false) {
			$next = false;
		} else {
			$next = intval($toutsujet[$i+1]);
		}

		if ($next && dol_print_date($cur, "%B") == dol_print_date($next, "%B") && dol_print_date($cur, "%Y") == dol_print_date($next, "%Y")){
			$colspan++;
		} else {
			print '<td colspan='.$colspan.' class="mois">'.dol_print_date($cur, "%B").'</td>'."\n";
			$colspan=1;
		}
	}

	print '</tr>'."\n";
	print '<tr>'."\n";
	print '<td></td>'."\n";

	//affichage des jours
	$colspan=1;
	for ($i=0;$i<count($toutsujet);$i++) {
		$cur = intval($toutsujet[$i]);
		if (isset($toutsujet[$i+1]) === false) {
			$next = false;
		} else {
			$next = intval($toutsujet[$i+1]);
		}
		if ($next && dol_print_date($cur, "%a %e") == dol_print_date($next,"%a %e") && dol_print_date($cur, "%B") == dol_print_date($next, "%B")) {
			$colspan++;
		} else {
			print '<td colspan="'.$colspan.'" class="jour">'.dol_print_date($cur, "%a %e").'</td>'."\n";
			$colspan=1;
		}
	}

	print '</tr>'."\n";

	//affichage des horaires
	if (strpos($dsondage->sujet, '@') !== false) {
		print '<tr>'."\n";
		print '<td></td>'."\n";

		for ($i=0; isset($toutsujet[$i]); $i++) {
			$heures=explode('@',$toutsujet[$i]);
			if (isset($heures[1])) {
				print '<td class="heure">'.$heures[1].'</td>'."\n";
			} else {
				print '<td class="heure"></td>'."\n";
			}
		}

		print '</tr>'."\n";
	}
}
else
{
	$toutsujet=str_replace("°","'",$toutsujet);

	//affichage des sujets du sondage
	print '<tr>'."\n";
	print '<td></td>'."\n";

	for ($i=0; isset($toutsujet[$i]); $i++)
	{
		$tmp=explode('@',$toutsujet[$i]);
		print '<td class="sujet">'.$tmp[0].'</td>'."\n";
	}

	print '</tr>'."\n";
}

//Usager pré-authentifié dans la liste?
$user_mod = false;


// Loop on each answer
$sumfor = array();
$sumagainst = array();
$compteur = 0;

$sql = 'SELECT * FROM '.MAIN_DB_PREFIX.'opensurvey_user_studs WHERE id_sondage='.$connect->Param('numsondage').' ORDER BY id_users';
$sql = $connect->Prepare($sql);
$user_studs = $connect->Execute($sql, array($numsondage));

while ($data = $user_studs->FetchNextObject(false))
{
	$ensemblereponses = $data->reponses;
	$nombase=str_replace("°","'",$data->nom);

	print '<tr>'."\n";

	// ligne d'un usager pré-authentifié
	$mod_ok = ($dsondage->format=="A+"||$dsondage->format=="D+") || (! empty($nombase) && in_array($nombase, $listofvoters));
	$user_mod |= $mod_ok;

	// Name
	print '<td class="nom">'.$nombase.'</td>'."\n";

	// pour chaque colonne
	for ($i=0; $i < $nbcolonnes; $i++)
	{
		$car = substr($ensemblereponses, $i, 1);
		if ($compteur == $ligneamodifier)
		{
			print '<td class="vide">';
			if (empty($listofanswers[$i]['format']) || ! in_array($listofanswers[$i]['format'],array('yesno','pourcontre')))
			{
				print '<input type="checkbox" name="choix'.$i.'" value="1" ';
				if ($car == '1') print 'checked="checked"';
				print '>';
			}
			if (! empty($listofanswers[$i]['format']) && $listofanswers[$i]['format'] == 'yesno')
			{
				$arraychoice=array('2'=>'&nbsp;','0'=>$langs->trans("No"),'1'=>$langs->trans("Yes"));
				print $form->selectarray("choix".$i, $arraychoice, $car);
			}
			if (! empty($listofanswers[$i]['format']) && $listofanswers[$i]['format'] == 'pourcontre')
			{
				$arraychoice=array('2'=>'&nbsp;','0'=>$langs->trans("Against"),'1'=>$langs->trans("For"));
				print $form->selectarray("choix".$i, $arraychoice, $car);
			}
			print '</td>'."\n";
		}
		else
		{
			if (empty($listofanswers[$i]['format']) || ! in_array($listofanswers[$i]['format'],array('yesno','pourcontre')))
			{
				if ($car == "1") print '<td class="ok">OK</td>'."\n";
				else print '<td class="non">KO</td>'."\n";
				// Total
				if (isset($sumfor[$i]) === false) $sumfor[$i] = 0;
				if ($car == "1") $sumfor[$i]++;
			}
			if (! empty($listofanswers[$i]['format']) && $listofanswers[$i]['format'] == 'yesno')
			{
				if ($car == "1") print '<td class="ok">'.$langs->trans("Yes").'</td>'."\n";
				else if ($car =="0") print '<td class="non">'.$langs->trans("No").'</td>'."\n";
				else print '<td class="vide">&nbsp;</td>'."\n";
				// Total
				if (! isset($sumfor[$i])) $sumfor[$i] = 0;
				if (! isset($sumagainst[$i])) $sumagainst[$i] = 0;
				if ($car == "1") $sumfor[$i]++;
				if ($car == "0") $sumagainst[$i]++;
			}
			if (! empty($listofanswers[$i]['format']) && $listofanswers[$i]['format'] == 'pourcontre')
			{
				if ($car == "1") print '<td class="ok">'.$langs->trans("For").'</td>'."\n";
				else if ($car =="0") print '<td class="non">'.$langs->trans("Against").'</td>'."\n";
				else print '<td class="vide">&nbsp;</td>'."\n";
				// Total
				if (! isset($sumfor[$i])) $sumfor[$i] = 0;
				if (! isset($sumagainst[$i])) $sumagainst[$i] = 0;
				if ($car == "1") $sumfor[$i]++;
				if ($car == "0") $sumagainst[$i]++;
			}
		}
	}

	//a la fin de chaque ligne se trouve les boutons modifier
	if ($compteur != $ligneamodifier && $mod_ok)
	{
		print '<td class="casevide"><input type="submit" class="button" name="modifierligne'.$compteur.'" value="'.dol_escape_htmltag($langs->trans("Edit")).'"></td>'."\n";
	}

	//demande de confirmation pour modification de ligne
	for ($i=0;$i<$nblignes;$i++) {
		if (isset($_POST["modifierligne$i"])) {
			if ($compteur == $i) {
				print '<td class="casevide"><input type="submit" class="button" name="validermodifier'.$compteur.'" value="'.dol_escape_htmltag($langs->trans("Save")).'"></td>'."\n";
			}
		}
	}

	$compteur++;
	print '</tr>'."\n";
}

// Add line to add new record
if ($ligneamodifier < 0 && (! isset($_SESSION['nom']) || ! $user_mod))
{
	print '<tr>'."\n";
	print '<td class="nom">'."\n";
	if (isset($_SESSION['nom']))
	{
		print '<input type=hidden name="nom" value="'.$_SESSION['nom'].'">'.$_SESSION['nom']."\n";
	} else {
		print '<input type="text" name="nom" placeholder="'.dol_escape_htmltag($langs->trans("Name")).'" maxlength="64" size="24">'."\n";
	}
	print '</td>'."\n";

	// affichage des cases de formulaire checkbox pour un nouveau choix
	for ($i=0;$i<$nbcolonnes;$i++)
	{
		print '<td class="vide">';
		if (empty($listofanswers[$i]['format']) || ! in_array($listofanswers[$i]['format'],array('yesno','pourcontre')))
		{
			print '<input type="checkbox" name="choix'.$i.'" value="1"';
			if (isset($_POST['choix'.$i]) && $_POST['choix'.$i] == '1' && is_error(NAME_EMPTY) )
			{
				print ' checked="checked"';
			}
			print '>';
		}
		if (! empty($listofanswers[$i]['format']) && $listofanswers[$i]['format'] == 'yesno')
		{
			$arraychoice=array('2'=>'&nbsp;','0'=>$langs->trans("No"),'1'=>$langs->trans("Yes"));
			print $form->selectarray("choix".$i, $arraychoice, GETPOST('choix'.$i));
		}
		if (! empty($listofanswers[$i]['format']) && $listofanswers[$i]['format'] == 'pourcontre')
		{
			$arraychoice=array('2'=>'&nbsp;','0'=>$langs->trans("Against"),'1'=>$langs->trans("For"));
			print $form->selectarray("choix".$i, $arraychoice, GETPOST('choix'.$i));
		}
		print '</td>'."\n";
	}

	// Affichage du bouton de formulaire pour inscrire un nouvel utilisateur dans la base
	print '<td><input type="image" name="boutonp" value="' . $langs->trans('Vote') . '" src="'.dol_buildpath('/opensurvey/img/add-24.png',1).'"></td>'."\n";
	print '</tr>'."\n";
}

// Select value of best choice (for checkbox columns only)
$nbofcheckbox=0;
for ($i=0; $i < $nbcolonnes; $i++)
{
	if (! empty($listofanswers[$i]['format']) && $listofanswers[$i]['format'] != 'checkbox') continue;
	$nbofcheckbox++;
	if (isset($sumfor[$i]))
	{
		if ($i == 0)
		{
			$meilleurecolonne = $sumfor[$i];
		}
		if (! isset($meilleurecolonne) || $sumfor[$i] > $meilleurecolonne)
		{
			$meilleurecolonne = $sumfor[$i];
		}
	}
}

// Show line total
print '<tr '.$bc[false].'>'."\n";
print '<td align="center">'. $langs->trans("Total") .'</td>'."\n";
for ($i = 0; $i < $nbcolonnes; $i++)
{
	$showsumfor = isset($sumfor[$i])?$sumfor[$i]:'';
	$showsumagainst = isset($sumagainst[$i])?$sumagainst[$i]:'';
	if (empty($showsumfor)) $showsumfor = 0;
	if (empty($showsumagainst)) $showsumagainst = 0;

	print '<td>';
	if (empty($listofanswers[$i]['format']) || ! in_array($listofanswers[$i]['format'],array('yesno','pourcontre'))) print $showsumfor;
	if (! empty($listofanswers[$i]['format']) && $listofanswers[$i]['format'] == 'yesno') print $langs->trans("Yes").': '.$showsumfor.'<br>'.$langs->trans("No").': '.$showsumagainst;
	if (! empty($listofanswers[$i]['format']) && $listofanswers[$i]['format'] == 'pourcontre') print $langs->trans("For").': '.$showsumfor.'<br>'.$langs->trans("Against").': '.$showsumagainst;
	print '</td>'."\n";
}
print '</tr>';
// Show picto winnner
if ($nbofcheckbox >= 2)
{
	print '<tr>'."\n";
	print '<td class="somme"></td>'."\n";
	for ($i=0; $i < $nbcolonnes; $i++)
	{
		//print 'xx'.(! empty($listofanswers[$i]['format'])).'-'.$sumfor[$i].'-'.$meilleurecolonne;
		if (! empty($listofanswers[$i]['format']) && $listofanswers[$i]['format'] == 'checkbox' && isset($sumfor[$i]) && isset($meilleurecolonne) && $sumfor[$i] == $meilleurecolonne)
		{
			print '<td class="somme"><img src="'.dol_buildpath('/opensurvey/img/medaille.png',1).'"></td>'."\n";
		} else {
			print '<td class="somme"></td>'."\n";
		}
	}
	print '</tr>'."\n";
}
print '</table>'."\n";
print '</div>'."\n";

$toutsujet=explode(",",$dsondage->sujet);
$toutsujet=str_replace("°","'",$toutsujet);

$compteursujet=0;
$meilleursujet = '';

for ($i = 0; $i < $nbcolonnes; $i++) {
	if (isset($sumfor[$i]) && isset($meilleurecolonne) && $sumfor[$i] == $meilleurecolonne) {
		$meilleursujet.=", ";
		if ($dsondage->format=="D"||$dsondage->format=="D+") {
			$meilleursujetexport = $toutsujet[$i];

			if (strpos($toutsujet[$i], '@') !== false) {
				$toutsujetdate = explode("@", $toutsujet[$i]);
				$meilleursujet .= dol_print_date($toutsujetdate[0],'daytext'). ' ('.dol_print_date($toutsujetdate[0],'%A').')' . _("for")  . ' ' . $toutsujetdate[1];
			} else {
				$meilleursujet .= dol_print_date($toutsujet[$i],'daytext'). ' ('.dol_print_date($toutsujet[$i],'%A').')';
			}
		} else {
			$tmps=explode('@',$toutsujet[$i]);
			$meilleursujet .= $tmps[0];
		}

		$compteursujet++;
	}
}

$meilleursujet=substr("$meilleursujet", 1);
$meilleursujet = str_replace("°", "'", $meilleursujet);


// Show best choice
if ($nbofcheckbox >= 2)
{
	$vote_str = $langs->trans('votes');
	print '<p class="affichageresultats">'."\n";

	if ($compteursujet == "1" && isset($meilleurecolonne)) {
		print '<img src="images/medaille.png" alt="Meilleur choix"> ' . $langs->trans('TheBestChoice') . ": <b>$meilleursujet</b> " . $langs->trans('with') . " <b>$meilleurecolonne </b>" . $vote_str . ".\n";
	} elseif (isset($meilleurecolonne)) {
		print '<img src="images/medaille.png" alt="Meilleur choix"> ' . $langs->trans('TheBestChoices')  . ": <b>$meilleursujet</b> " . $langs->trans('with') . "  <b>$meilleurecolonne </b>" . $vote_str . ".\n";
	}

	print '</p><br>';
}

print '<br>';

//affichage des commentaires des utilisateurs existants
$sql = 'select * from '.MAIN_DB_PREFIX.'opensurvey_comments where id_sondage='.$connect->Param('numsondage').' order by id_comment';
$sql = $connect->Prepare($sql);
$comment_user=$connect->Execute($sql, array($numsondage));

if ($comment_user->RecordCount() != 0) {
	print "<br><b>" . $langs->trans("CommentsOfVoters") . " :</b><br>\n";
	while($dcomment = $comment_user->FetchNextObject(false)) {
		print '<div class="comment"><span class="usercomment">'.$dcomment->usercomment. ' :</span> <span class="comment">' . nl2br($dcomment->comment) . '</span></div>';
	}
}

//affichage de la case permettant de rajouter un commentaire par les utilisateurs
print '<div class="addcomment">' .$langs->trans("AddACommentForPoll") . "<br>\n";

print '<textarea name="comment" rows="2" cols="60"></textarea><br>'."\n";
if (isset($_SESSION['nom']) === false)
{
	print $langs->trans("Name") .' : ';
	print '<input type="text" name="commentuser" maxlength="64" /> &nbsp; '."\n";
}
print '<input type="submit" class="button" name="ajoutcomment" value="'.dol_escape_htmltag($langs->trans("AddComment")).'"><br>'."\n";
print '</form>'."\n";
// Focus javascript sur la case de texte du formulaire
print '</div>'."\n";

print '<br><br>';

/*
// Define $urlwithroot
$urlwithouturlroot=preg_replace('/'.preg_quote(DOL_URL_ROOT,'/').'$/i','',trim($dolibarr_main_url_root));
$urlwithroot=$urlwithouturlroot.DOL_URL_ROOT;		// This is to use external domain name found into config file
//$urlwithroot=DOL_MAIN_URL_ROOT;					// This is to use same domain name than current

$message='';
$url=$urlwithouturlroot.dol_buildpath('/opensurvey/public/studs.php',1).'?sondage='.$numsondage;
$urlvcal='<a href="'.$url.'" target="_blank">'.$url.'</a>';
$message.=img_picto('','object_globe.png').' '.$langs->trans("UrlForSurvey").': '.$urlvcal;

print '<center>'.$message.'</center>';
*/


print '<a name="bas"></a>'."\n";

llxFooterSurvey();

$db->close();
?>