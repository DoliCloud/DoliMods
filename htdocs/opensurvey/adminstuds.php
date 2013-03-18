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
dol_include_once("/opensurvey/class/opensurveysondage.class.php");

// Security check
if (!$user->admin)
	accessforbidden();


include_once('./variables.php');
include_once('./fonctions.php');
include_once('./bandeaux_local.php');


// Initialisation des variables
$action=GETPOST('action');
$numsondageadmin = '';
$sondage = false;

// recuperation du numero de sondage admin (24 car.) dans l'URL
if (issetAndNoEmpty('sondage', $_GET) && is_string($_GET['sondage']) && strlen($_GET['sondage']) === 24)
{
	$numsondageadmin=GETPOST("sondage",'alpha');
	//on découpe le résultat pour avoir le numéro de sondage (16 car.)
	$numsondage=substr($numsondageadmin, 0, 16);
}


if (preg_match(";[\w\d]{24};i", $numsondageadmin))
{
	$sql = 'SELECT * FROM '.MAIN_DB_PREFIX.'opensurvey_sondage WHERE id_sondage_admin = '.$connect->Param('numsondageadmin');
	$sql = $connect->Prepare($sql);
	$sondage = $connect->Execute($sql, array($numsondageadmin));

	if ($sondage !== false) {
		$sql = 'SELECT * FROM '.MAIN_DB_PREFIX.'opensurvey_sujet_studs WHERE id_sondage = '.$connect->Param('numsondage');
		$sql = $connect->Prepare($sql);
		$sujets = $connect->Execute($sql, array($numsondage));

		$sql = 'SELECT * FROM '.MAIN_DB_PREFIX.'opensurvey_user_studs WHERE id_sondage = '.$connect->Param('numsondage').' order by id_users';
		$sql = $connect->Prepare($sql);
		$user_studs = $connect->Execute($sql, array($numsondage));
	}
}

$dsujet=$sujets->FetchObject(false);
$dsondage=$sondage->FetchObject(false);

$nbcolonnes = substr_count($dsujet->sujet, ',') + 1;
$nblignes = $user_studs->RecordCount();

$object=new Opensurveysondage($db);



/*
 * Actions
 */

// Delete
if ($action == 'delete_confirm')
{
	$object->delete($user,'',$numsondageadmin);

	header('Location: '.dol_buildpath('/opensurvey/list.php',1));
	exit();
}

if (isset($_POST["boutonnouveautitre"]) || isset($_POST["boutonnouveautitre_x"])) {
	if(issetAndNoEmpty('nouveautitre') === false) {
		$err |= TITLE_EMPTY;
	} else {
		//modification de la base SQL avec le nouveau titre
		$nouveautitre = GETPOST('nouveautitre');
		$sql = 'UPDATE '.MAIN_DB_PREFIX."opensurvey_sondage SET titre = '".$db->escape($nouveautitre)."' WHERE id_sondage = '".$db->escape($numsondage)."'";
		dol_syslog($sql);
		$resql = $db->query($sql);
		if ($resql < 0) dol_print_error($db,'');
	}
}

// si le bouton est activé, quelque soit la valeur du champ textarea
if (isset($_POST["boutonnouveauxcommentaires"]) || isset($_POST["boutonnouveauxcommentaires_x"])) {
	if(issetAndNoEmpty('nouveautitre') === false) {
		$err |= COMMENT_EMPTY;
	} else {
		$commentaires = GETPOST('nouveauxcommentaires');

		//modification de la base SQL avec les nouveaux commentaires
		$sql = 'UPDATE '.MAIN_DB_PREFIX.'opensurvey_sondage SET commentaires = '.$connect->Param('commentaires').' WHERE id_sondage = '.$connect->Param('numsondage');
		$sql = $connect->Prepare($sql);

		$connect->Execute($sql, array($commentaires, $numsondage));
	}
}

//si la valeur de la nouvelle adresse est valide et que le bouton est activé
if (isset($_POST["boutonnouvelleadresse"]) || isset($_POST["boutonnouvelleadresse_x"])) {
	if(issetAndNoEmpty('nouvelleadresse') === false || validateEmail($_POST["nouvelleadresse"]) === false) {
		$err |= INVALID_EMAIL;
	} else {
		$nouvelleadresse = GETPOST('nouvelleadresse');

		//modification de la base SQL avec la nouvelle adresse
		$sql = 'UPDATE '.MAIN_DB_PREFIX.'opensurvey_sondage SET mail_admin = '.$connect->Param('nouvelleadresse').' WHERE id_sondage = '.$connect->Param('numsondage');
		$sql = $connect->Prepare($sql);

		$connect->Execute($sql, array($nouvelleadresse, $numsondage));
	}
}


// quand on ajoute un commentaire utilisateur
if (isset($_POST['ajoutcomment']) || isset($_POST['ajoutcomment_x']))
{
	if(issetAndNoEmpty('commentuser') === false) {
		$err |= COMMENT_USER_EMPTY;
	} else {
		$comment_user = GETPOST("commentuser");
	}

	if(issetAndNoEmpty('comment') === false) {
		$err |= COMMENT_EMPTY;
	}

	if (issetAndNoEmpty('comment') && !is_error(COMMENT_EMPTY) && !is_error(NO_POLL) && !is_error(COMMENT_USER_EMPTY)) {
		$comment = GETPOST("comment");

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


//action si le bouton participer est cliqué
if (isset($_POST["boutonp"]) || isset($_POST["boutonp_x"]))
{
	//si on a un nom dans la case texte
	if (issetAndNoEmpty('nom')){
		$nouveauchoix = '';
		$erreur_prenom = false;

		for ($i=0;$i<$nbcolonnes;$i++){
			//si la checkbox est cochée alors valeur est egale à 1
			if (isset($_POST["choix$i"])){
				$nouveauchoix.="1";
			} else { //sinon 0
				$nouveauchoix.="0";
			}
		}

		$nom = $_POST["nom"];
		while ($tmpuser = $user_studs->FetchNextObject(false)) {
			if ($nom == $tmpuser->nom){
				$erreur_prenom="yes";
			}
		}

		// Ecriture des choix de l'utilisateur dans la base
		if (!$erreur_prenom) {
			$sql = 'INSERT INTO '.MAIN_DB_PREFIX.'opensurvey_user_studs (nom, id_sondage, reponses) VALUES ('.
				$connect->Param('nom').','.
				$connect->Param('numsondage').','.
				$connect->Param('nouveauchoix').')';

			$sql = $connect->Prepare($sql);
			$connect->Execute($sql, array($nom, $numsondage, $nouveauchoix));

			$_SESSION["savevoter"]=$nom.','.isset($_SESSION["savevoter"])?$_SESSION["savevoter"]:'';	// Save voter
		}
	}
}


//action quand on ajoute une colonne au format AUTRE
if (isset($_POST["ajoutercolonne"]) && issetAndNoEmpty('nouvellecolonne') && ($dsondage->format == "A" || $dsondage->format == "A+")) {
	$nouveauxsujets=$dsujet->sujet;

	//on rajoute la valeur a la fin de tous les sujets deja entrés
	$nouveauxsujets.=",";
	$nouveauxsujets.=',';
	$nouveauxsujets.=str_replace(array(",","@"), " ", $_POST["nouvellecolonne"]).(empty($_POST["typecolonne"])?'':'@'.$_POST["typecolonne"]);

	//mise a jour avec les nouveaux sujets dans la base
	$sql = 'UPDATE '.MAIN_DB_PREFIX.'opensurvey_sujet_studs SET sujet = '.$connect->Param('nouveauxsujets').' WHERE id_sondage = '.$connect->Param('numsondage');
	$sql = $connect->Prepare($sql);
	$connect->Execute($sql, array($nouveauxsujets, $numsondage));
}


//action quand on ajoute une colonne au format DATE
if (isset($_POST["ajoutercolonne"]) && ($dsondage->format == "D" || $dsondage->format == "D+")) {
	$nouveauxsujets=$dsujet->sujet;

	if (isset($_POST["nouveaujour"]) && $_POST["nouveaujour"] != "vide" &&
		isset($_POST["nouveaumois"]) && $_POST["nouveaumois"] != "vide" &&
		isset($_POST["nouvelleannee"]) && $_POST["nouvelleannee"] != "vide") {

		$nouvelledate=dol_mktime(0, 0, 0, $_POST["nouveaumois"], $_POST["nouveaujour"], $_POST["nouvelleannee"]);

		if (isset($_POST["nouvelleheuredebut"]) && $_POST["nouvelleheuredebut"]!="vide"){
			$nouvelledate.="@";
			$nouvelledate.=$_POST["nouvelleheuredebut"];
			$nouvelledate.="h";

			if ($_POST["nouvelleminutedebut"]!="vide") {
				$nouvelledate.=$_POST["nouvelleminutedebut"];
			}
		}

		if (isset($_POST["nouvelleheurefin"]) && $_POST["nouvelleheurefin"]!="vide"){
			$nouvelledate.="-";
			$nouvelledate.=$_POST["nouvelleheurefin"];
			$nouvelledate.="h";

			if ($_POST["nouvelleminutefin"]!="vide") {
				$nouvelledate.=$_POST["nouvelleminutefin"];
			}
		}

		if($_POST["nouvelleheuredebut"] == "vide" || (isset($_POST["nouvelleheuredebut"]) && isset($_POST["nouvelleheurefin"]) && (($_POST["nouvelleheuredebut"] < $_POST["nouvelleheurefin"]) || (($_POST["nouvelleheuredebut"] == $_POST["nouvelleheurefin"]) && ($_POST["nouvelleminutedebut"] < $_POST["nouvelleminutefin"]))))) {
			$erreur_ajout_date = false;
		} else {
			$erreur_ajout_date = "yes";
		}

		//on rajoute la valeur dans les valeurs
		$datesbase = explode(",",$dsujet->sujet);
		$taillebase = sizeof($datesbase);

		//recherche de l'endroit de l'insertion de la nouvelle date dans les dates deja entrées dans le tableau
		if ($nouvelledate < $datesbase[0]) {
			$cleinsertion = 0;
		} elseif ($nouvelledate > $datesbase[$taillebase-1]) {
			$cleinsertion = count($datesbase);
		} else {
			for ($i = 0; $i < count($datesbase); $i++) {
				$j = $i + 1;
				if ($nouvelledate > $datesbase[$i] && $nouvelledate < $datesbase[$j]) {
					$cleinsertion = $j;
				}
			}
		}

		array_splice($datesbase, $cleinsertion, 0, $nouvelledate);
		$cle = array_search($nouvelledate, $datesbase);
		$dateinsertion = '';
		for ($i = 0; $i < count($datesbase); $i++) {
			$dateinsertion.=",";
			$dateinsertion.=$datesbase[$i];
		}

		$dateinsertion = substr("$dateinsertion", 1);

		//mise a jour avec les nouveaux sujets dans la base
		if (isset($erreur_ajout_date) && !$erreur_ajout_date){
			$sql = 'UPDATE '.MAIN_DB_PREFIX.'opensurvey_sujet_studs SET sujet = '.$connect->Param('dateinsertion').' WHERE id_sondage = '.$connect->Param('numsondage');
			$sql = $connect->Prepare($sql);
			$connect->Execute($sql, array($dateinsertion, $numsondage));

			if ($nouvelledate > strtotime($dsondage->date_fin)) {
				$date_fin=$nouvelledate+200000;
				$sql = 'UPDATE '.MAIN_DB_PREFIX.'opensurvey_sondage SET date_fin = '.$connect->Param('date_fin').' WHERE id_sondage = '.$connect->Param('numsondage');
				$sql = $connect->Prepare($sql);
				$connect->Execute($sql, array($date_fin, $numsondage));
			}
		}

		//mise a jour des reponses actuelles correspondant au sujet ajouté
		$sql = 'UPDATE '.MAIN_DB_PREFIX.'opensurvey_user_studs SET reponses = '.$connect->Param('reponses').' WHERE nom = '.$connect->Param('nom').' AND id_users='.$connect->Param('id_users');
		$sql = $connect->Prepare($sql);
		while ($data = $user_studs->FetchNextObject(false)) {
			$ensemblereponses=$data->reponses;
			$newcar = '';

			//parcours de toutes les réponses actuelles
			for ($j = 0; $j < $nbcolonnes; $j++) {
				$car=substr($ensemblereponses,$j,1);

				//si les reponses ne concerne pas la colonne ajoutée, on concatene
				if ($j==$cle) {
					$newcar.="0";
				}

				$newcar.=$car;
			}

			//mise a jour des reponses utilisateurs dans la base
			if (isset($erreur_ajout_date) && !$erreur_ajout_date){
				$connect->Execute($sql, array($newcar, $data->nom, $data->id_users));
			}
		}

		//envoi d'un mail pour prévenir l'administrateur du changement
		$adresseadmin = $dsondage->mail_admin;
	} else {
		$erreur_ajout_date="yes";
	}
}


//suppression de ligne dans la base
for ($i = 0; $i < $nblignes; $i++) {
	if (isset($_POST["effaceligne$i"]) || isset($_POST['effaceligne'.$i.'_x'])) {
		$compteur=0;
		$sql = 'DELETE FROM '.MAIN_DB_PREFIX.'opensurvey_user_studs WHERE nom = '.$connect->Param('nom').' AND id_users = '.$connect->Param('id_users');
		$sql = $connect->Prepare($sql);

		while ($data=$user_studs->FetchNextObject(false)) {
			if ($compteur==$i){
				$connect->Execute($sql, array($data->nom, $data->id_users));
			}

			$compteur++;
		}
	}
}


// Delete comment
$idcomment=GETPOST('deletecomment','int');
if ($idcomment)
{
	$sql = 'DELETE FROM '.MAIN_DB_PREFIX.'opensurvey_comments WHERE id_comment = '.$idcomment;
	$resql = $db->query($sql);
}


//suppression de colonnes dans la base
for ($i = 0; $i < $nbcolonnes; $i++)
{
	if ((isset($_POST["effacecolonne$i"]) || isset($_POST['effacecolonne'.$i.'_x'])) && $nbcolonnes > 1)
	{
		$toutsujet = explode(",",$dsujet->sujet);
		$j = 0;
		$nouveauxsujets = '';

		//parcours de tous les sujets actuels
		while (isset($toutsujet[$j])) {
			//si le sujet n'est pas celui qui a été effacé alors on concatene
			if ($i != $j) {
				$nouveauxsujets .= ',';
				$nouveauxsujets .= $toutsujet[$j];
			}

			$j++;
		}

		//on enleve la virgule au début
		$nouveauxsujets = substr("$nouveauxsujets", 1);

		//nettoyage des reponses actuelles correspondant au sujet effacé
		$compteur = 0;
		$sql = 'UPDATE '.MAIN_DB_PREFIX.'opensurvey_user_studs SET reponses = '.$connect->Param('reponses').' WHERE nom = '.$connect->Param('nom').' AND id_users = '.$connect->Param('id_users');
		$sql = $connect->Prepare($sql);

		while ($data = $user_studs->FetchNextObject(false)) {
			$newcar = '';
			$ensemblereponses = $data->reponses;

			//parcours de toutes les réponses actuelles
			for ($j = 0; $j < $nbcolonnes; $j++) {
				$car=substr($ensemblereponses, $j, 1);
				//si les reponses ne concerne pas la colonne effacée, on concatene
				if ($i != $j) {
					$newcar .= $car;
				}
			}

			$compteur++;

			//mise a jour des reponses utilisateurs dans la base
			$connect->Execute($sql, array($newcar, $data->nom, $data->id_users));
		}

		//mise a jour des sujets dans la base
		$sql = 'UPDATE '.MAIN_DB_PREFIX.'opensurvey_sujet_studs SET sujet = '.$connect->Param('nouveauxsujets').' WHERE id_sondage = '.$connect->Param('numsondage');
		$sql = $connect->Prepare($sql);
		$connect->Execute($sql, array($nouveauxsujets, $numsondage));
	}
}



/*
 * View
 */

$form=new Form($db);

$arrayofjs=array('/opensurvey/block_enter.js');
$arrayofcss=array('/opensurvey/css/style.css');
llxHeader('',$dsondage->titre, 0, 0, 0, 0, $arrayofjs, $arrayofcss);

$object->fetch(0,$numsondage);

// Define format of choices
$toutsujet=explode(",",$object->sujet);
$listofanswers=array();
foreach ($toutsujet as $value)
{
	$tmp=explode('@',$value);
	$listofanswers[]=array('label'=>$tmp[0],'format'=>($tmp[1]?$tmp[1]:'checkbox'));
}
$toutsujet=str_replace("@","<br>",$toutsujet);
$toutsujet=str_replace("°","'",$toutsujet);


print '<form name="formulaire4" action="'.$_SERVER["PHP_SELF"].'?sondage='.$numsondageadmin.'" method="POST">'."\n";

$head = array();

$head[0][0] = '';
$head[0][1] = $langs->trans("Card");
$head[0][2] = 'general';
$h++;

$head[1][0] = 'adminstuds_preview.php?sondage='.$object->id_sondage_admin;
$head[1][1] = $langs->trans("SurveyResults").'/'.$langs->trans("Preview");
$head[1][2] = 'preview';
$h++;

print dol_get_fiche_head($head,'general',$langs->trans("Survey"),0,dol_buildpath('/opensurvey/img/object_opensurvey.png',1),1);


print '<table class="border" width="100%">';

$linkback = '<a href="'.dol_buildpath('/opensurvey/list.php',1).'">'.$langs->trans("BackToList").'</a>';

// Ref
print '<tr><td width="18%">'.$langs->trans('Ref').'</td>';
print '<td colspan="3">';
print $form->showrefnav($object, 'sondage', $linkback, 1, 'id_sondage_admin', 'id_sondage_admin');
print '</td>';
print '</tr>';

// Type
$type=($dsondage->format=="A"||$dsondage->format=="A+")?'classic':'date';
print '<tr><td>'.$langs->trans("Type").'</td><td colspan="2">';
print img_picto('',dol_buildpath('/opensurvey/img/'.($type == 'classic'?'chart-32.png':'calendar-32.png'),1),'width="16"',1);
print ' '.$langs->trans($type=='classic'?"TypeClassic":"TypeDate").'</td></tr>';

// Title
print '<tr><td>';
$adresseadmin=$dsondage->mail_admin;
print $langs->trans("Title") .'</td><td colspan="2">';
if ($action == 'edit')
{
print '<input type="text" name="nouveautitre" size="40" value="'.dol_escape_htmltag($object->titre).'">';
	print '<input type="submit" class="button" name="boutonnouveautitre" value="'.dol_escape_htmltag($langs->trans("Save")).'">'."\n";
}
else print $object->titre;
//si la valeur du nouveau titre est invalide : message d'erreur
if ((isset($_POST["boutonnouveautitre"]) || isset($_POST["boutonnouveautitre_x"])) && !issetAndNoEmpty('nouveautitre')) {
	print '<font color="#FF0000">'. $langs->trans("ErrorFieldRequired").'</font><br>'."\n";
}
print '</td></tr>';

// Auteur
print '<tr><td>';
print $langs->trans("Author") .'</td><td colspan="2">';
print $object->nom_admin;
print '</td></tr>';

// Description
print '<tr><td>'.$langs->trans("Description") .'</td><td colspan="2">';
if ($action == 'edit')
{
print '<textarea name="nouveauxcommentaires" rows="7" cols="80">'.$object->commentaires.'</textarea><br><input type="submit" class="button" name="boutonnouveauxcommentaires" value="'.dol_escape_htmltag($langs->trans("Save")).'">'."\n";
}
else print dol_nl2br($object->commentaires);
print '</td></tr>';

// EMail
print '<tr><td>'.$langs->trans("EMail") .'</td><td colspan="2">';
if ($action == 'edit')
{
	print '<input type="text" name="nouvelleadresse" size="40" value="'.$object->mail_admin.'"> <input type="submit" class="button" name="boutonnouvelleadresse" value="'.dol_escape_htmltag($langs->trans("Save")).'">';
}
else print dol_print_email($object->mail_admin);
//si l'adresse est invalide ou le champ vide : message d'erreur
if ((isset($_POST["boutonnouvelleadresse"]) || isset($_POST["boutonnouvelleadresse_x"])) && !issetAndNoEmpty('nouvelleadresse')) {
	print '<font color="#FF0000">'. $langs->trans("ErorFieldRequired") .'</font><br><br>'."\n";
}
print '</td></tr>';

// Can edit other votes
print '<tr><td>'.$langs->trans('CanEditVotes').'</td><td colspan="2">'.yn(preg_match('/\+/',$object->format)).'</td></tr>';


// Link
print '<tr><td>'.img_picto('','object_globe.png').' '.$langs->trans("UrlForSurvey",'').'</td><td colspan="2">';

// Define $urlwithroot
$urlwithouturlroot=preg_replace('/'.preg_quote(DOL_URL_ROOT,'/').'$/i','',trim($dolibarr_main_url_root));
$urlwithroot=$urlwithouturlroot.DOL_URL_ROOT;		// This is to use external domain name found into config file
//$urlwithroot=DOL_MAIN_URL_ROOT;					// This is to use same domain name than current

$url=$urlwithouturlroot.dol_buildpath('/opensurvey/public/studs.php',1).'?sondage='.$numsondage;
$urlvcal='<a href="'.$url.'" target="_blank">'.$url.'</a>';
print $urlvcal;

print '</table>';

print '</form>'."\n";

dol_fiche_end();


/*
 * Barre d'actions
 */
print '<div class="tabsAction">';

print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?action=edit&sondage=' . $numsondageadmin . '">'.$langs->trans("Modify") . '</a>';

print '<a class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?suppressionsondage=1&sondage='.$numsondageadmin.'&amp;action=delete"';
print '>'.$langs->trans('Delete').'</a>';

print '</div>';

if ($action == 'delete')
{
	print $form->formconfirm($_SERVER["PHP_SELF"].'?&sondage='.$numsondageadmin, $langs->trans("RemovePoll"), $langs->trans("ConfirmRemovalOfPoll",$id), 'delete_confirm', '', '', 1);
}



print '<br>';


print '<form name="formulaire5" action="#" method="POST">'."\n";

print_fiche_titre($langs->trans("CommentsOfVoters"),'','');

// Comment list
$sql = 'SELECT id_comment, usercomment, comment FROM '.MAIN_DB_PREFIX.'opensurvey_comments WHERE id_sondage='.$connect->Param('numsondage').' ORDER BY id_comment';
$sql = $connect->Prepare($sql);
$comment_user = $connect->Execute($sql, array($numsondage));
if ($comment_user->RecordCount() != 0)
{
	$i = 0;
	while ( $dcomment=$comment_user->FetchNextObject(false))
	{
		print '<a href="'.dol_buildpath('/opensurvey/adminstuds.php',1).'?deletecomment='.$dcomment->id_comment.'&sondage='.$numsondageadmin.'"> '.img_picto('', 'delete.png').'</a> '.$dcomment->usercomment.' : '.$dcomment->comment." <br>";
		$i++;
	}
}
else
{
	print $langs->trans("NoCommentYet").'<br>';;
}

print '<br>';

// Add comment
print $langs->trans("AddACommentForPoll") . '<br>';
print '<textarea name="comment" rows="2" cols="80"></textarea><br>'."\n";
print $langs->trans("Name") .' : <input type=text name="commentuser"><br>'."\n";
print '<input type="submit" class="button" name="ajoutcomment" value="'.dol_escape_htmltag($langs->trans("AddComment")).'"><br>'."\n";
if (isset($erreur_commentaire_vide) && $erreur_commentaire_vide=="yes") {
	print "<font color=#FF0000>" . $langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Name")) . "</font>";
}

print '</form>';

llxFooterSurvey();

$db->close();
?>