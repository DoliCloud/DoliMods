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
if (!$user->admin) accessforbidden();


include_once('./variables.php');
include_once('./fonctions.php');
include_once('./bandeaux_local.php');


// Init vars
$action=GETPOST('action');
$numsondageadmin = false;
$numsondage=false;
$sondage = false;

// recuperation du numero de sondage admin (24 car.) dans l'URL
if (issetAndNoEmpty('sondage', $_GET) && is_string($_GET['sondage']) && strlen($_GET['sondage']) === 24)
{
	$numsondageadmin=$_GET["sondage"];
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

//verification de l'existence du sondage, s'il n'existe pas on met une page d'erreur
if (!$sondage || $sondage->RecordCount() != 1)
{
	dol_print_error('',"This poll doesn't exist !");
	exit;
}

$dsujet=$sujets->FetchObject(false);
$dsondage=$sondage->FetchObject(false);

$nbcolonnes = substr_count($dsujet->sujet, ',') + 1;
$nblignes = $user_studs->RecordCount();



/*
 * Actions
 */

if (GETPOST('annullesuppression'))
{
	$action='';
}

//action si bouton confirmation de suppression est activé
if (isset($_POST["confirmesuppression"]) || isset($_POST["confirmesuppression_x"]))
{
	$nbuser=$user_studs->RecordCount();

	//destruction des données dans la base SQL
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

	header('Location: '.dol_buildpath('/opensurvey/list.php',1));
	exit();
}

if (isset($_POST["boutonnouveautitre"]) || isset($_POST["boutonnouveautitre_x"])) {
	if(issetAndNoEmpty('nouveautitre') === false) {
		$err |= TITLE_EMPTY;
	} else {
		//modification de la base SQL avec le nouveau titre
		$nouveautitre = htmlentities(html_entity_decode($_POST['nouveautitre'], ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8');
		$sql = 'UPDATE '.MAIN_DB_PREFIX.'opensurvey_sondage SET titre = '.$connect->Param('nouveautitre').' WHERE id_sondage = '.$connect->Param('numsondage');
		$sql = $connect->Prepare($sql);

		$connect->Execute($sql, array($nouveautitre, $numsondage));
	}
}

// si le bouton est activé, quelque soit la valeur du champ textarea
if (isset($_POST["boutonnouveauxcommentaires"]) || isset($_POST["boutonnouveauxcommentaires_x"])) {
	if(issetAndNoEmpty('nouveautitre') === false) {
		$err |= COMMENT_EMPTY;
	} else {
		$commentaires = htmlentities(html_entity_decode($_POST['nouveauxcommentaires'], ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8');

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
		$nouvelleadresse = htmlentities(html_entity_decode($_POST['nouvelleadresse'], ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8');

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
		$comment_user = htmlentities(html_entity_decode($_POST["commentuser"], ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8');
	}

	if(issetAndNoEmpty('comment') === false) {
		$err |= COMMENT_EMPTY;
	}

	if (issetAndNoEmpty('comment') && !is_error(COMMENT_EMPTY) && !is_error(NO_POLL) && !is_error(COMMENT_USER_EMPTY)) {
		$comment = htmlentities(html_entity_decode($_POST["comment"], ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8');

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
	//si on a un nom dans la case texte
	if (issetAndNoEmpty('nom'))
	{
		$nouveauchoix = '';
		$erreur_prenom = false;

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

		while ($tmpuser = $user_studs->FetchNextObject(false)) {
			if ($nom == $tmpuser->nom)
			{
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


// Update vote
$testmodifier = false;
$testligneamodifier = false;
$ligneamodifier = -1;
for ($i=0; $i<$nblignes; $i++)
{
	if (isset($_POST['modifierligne'.$i])) {
		$ligneamodifier=$i;
		$testligneamodifier=true;
	}

	//test pour voir si une ligne est a modifier
	if (isset($_POST['validermodifier'.$i])) {
		$modifier=$i;
		$testmodifier=true;
	}
}
if ($testmodifier)
{
	//var_dump($_POST);exit;
	$nouveauchoix = '';
	for ($i = 0; $i < $nbcolonnes; $i++)
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

	while ($data=$user_studs->FetchNextObject(false)) 
	{
		if ($compteur==$modifier) 
		{
			$sql = 'UPDATE '.MAIN_DB_PREFIX.'opensurvey_user_studs SET reponses = '.$connect->Param('reponses').' WHERE nom = '.$connect->Param('nom').' AND id_users = '.$connect->Param('id_users');
			$sql = $connect->Prepare($sql);
			$connect->Execute($sql, array($nouveauchoix, $data->nom, $data->id_users));
		}

		$compteur++;
	}
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


// delete comment
$sql = 'SELECT * FROM '.MAIN_DB_PREFIX.'opensurvey_comments WHERE id_sondage='.$connect->Param('numsondage').' ORDER BY id_comment';
$sql = $connect->Prepare($sql);
$comment_user = $connect->Execute($sql, array($numsondage));
$i = 0;
while ($dcomment = $comment_user->FetchNextObject(false)) {
	if (isset($_POST['suppressioncomment'.$i.'_x'])) {
		$sql = 'DELETE FROM '.MAIN_DB_PREFIX.'opensurvey_comments WHERE id_comment = '.$connect->Param('id_comment');
		$sql = $connect->Prepare($sql);
		$connect->Execute($sql, array($dcomment->id_comment));
	}

	$i++;
}


// delete column
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

		while ($data = $user_studs->FetchNextObject(false))
		{
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
$object=new OpenSurveySondage($db);

$arrayofjs=array('/opensurvey/block_enter.js');
$arrayofcss=array('/opensurvey/css/style.css');
llxHeader('',$dsondage->titre, 0, 0, 0, 0, $arrayofjs, $arrayofcss);

$object->fetch(0,$numsondage);

echo '<form name="formulaire4" action="#" method="POST" onkeypress="javascript:process_keypress(event)">'."\n";

$head = array();

$head[0][0] = 'adminstuds.php?sondage='.$object->id_sondage_admin;
$head[0][1] = $langs->trans("Card");
$head[0][2] = 'general';
$h++;

$head[1][0] = '';
$head[1][1] = $langs->trans("SurveyResults").'/'.$langs->trans("Preview");
$head[1][2] = 'preview';
$h++;

print dol_get_fiche_head($head,'preview',$langs->trans("Survey"),0,dol_buildpath('/opensurvey/img/object_opensurvey.png',1),1);


print '<table class="border" width="100%">';

$linkback = '<a href="'.dol_buildpath('/opensurvey/list.php',1).(! empty($socid)?'?socid='.$socid:'').'">'.$langs->trans("BackToList").'</a>';

// Ref
print '<tr><td width="18%">'.$langs->trans('Ref').'</td>';
print '<td colspan="3">';
print $form->showrefnav($object, 'sondage', $linkback, 1, 'id_sondage_admin', 'id_sondage_admin');
print '</td>';
print '</tr>';

print '<tr><td>'.$langs->trans("Type").'</td><td colspan="2">'.$langs->trans(($dsondage->format=="A"||$dsondage->format=="A+")?"TypeClassic":"TypeDate").'</td></tr>';

// Link
print '<tr><td>'.img_picto('','object_globe.png').' '.$langs->trans("UrlForSurvey",'').'</td><td>';

// Define $urlwithroot
$urlwithouturlroot=preg_replace('/'.preg_quote(DOL_URL_ROOT,'/').'$/i','',trim($dolibarr_main_url_root));
$urlwithroot=$urlwithouturlroot.DOL_URL_ROOT;		// This is to use external domain name found into config file
//$urlwithroot=DOL_MAIN_URL_ROOT;					// This is to use same domain name than current

$url=$urlwithouturlroot.dol_buildpath('/opensurvey/public/studs.php',1).'?sondage='.$numsondage;
$urlvcal='<a href="'.$url.'" target="_blank">'.$url.'</a>';
print $urlvcal;


print '</table>';

dol_fiche_end();

echo '</form>'."\n";


showlogo();

// reload
$dsujet=$sujets->FetchObject(false);
$dsondage=$sondage->FetchObject(false);

if (isset($_POST["ajoutsujet"]) || isset($_POST["ajoutsujet_x"])) {

	//on recupere les données et les sujets du sondage
	echo '<form name="formulaire" action="'.getUrlSondage($numsondageadmin, true).'" method="POST" onkeypress="javascript:process_keypress(event)">'."\n";

	echo '<div class="center">'."\n";
	echo "<br><br>"."\n";

	// Add new column
	if ($dsondage->format=="A"||$dsondage->format=="A+")
	{
		echo $langs->trans("AddNewColumn") .' :<br><br>';
		echo $langs->trans("TitleChoice").' <input type="text" name="nouvellecolonne" size="40"><br>';
		$tmparray=array('checkbox'=>$langs->trans("CheckBox"),'yesno'=>$langs->trans("YesNoList"),'pourcontre'=>$langs->trans("PourContreList"));
		print $langs->trans("Type").' '.$form->selectarray("typecolonne", $tmparray, GETPOST('typecolonne')).'<br><br>';
		print '<input type="submit" class="button" name="ajoutercolonne" value="'.dol_escape_htmltag($langs->trans("Add")).'">';
		print '<br><br>'."\n";
	}
	else
	{
		//ajout d'une date avec creneau horaire
		echo _("You can add a new scheduling date to your poll.<br> If you just want to add a new hour to an existant date, put the same date and choose a new hour.") .'<br><br> '."\n";
		echo _("Add a date") .' :<br><br>'."\n";
		echo '<select name="nouveaujour"> '."\n";
		echo '<OPTION VALUE="vide"></OPTION>'."\n";
		for ($i=1;$i<32;$i++){
			echo '<OPTION VALUE="'.$i.'">'.$i.'</OPTION>'."\n";
		}
		echo '</SELECT>'."\n";

		echo '<select name="nouveaumois"> '."\n";
		echo '<OPTION VALUE="vide"></OPTION>'."\n";
		for($i = 1; $i < 13; $i++) {
			echo '<OPTION VALUE="'.$i.'">'.strftime('%B', mktime(0, 0, 0, $i)).'</OPTION>'."\n";
		}
		echo '</SELECT>'."\n";


		echo '<select name="nouvelleannee"> '."\n";
		echo '<OPTION VALUE="vide"></OPTION>'."\n";
		for ($i = date("Y"); $i < (date("Y") + 5); $i++) {
			echo '<OPTION VALUE="'.$i.'">'.$i.'</OPTION>'."\n";
		}
		echo '</SELECT>'."\n";
		echo '<br><br>'. _("Add a start hour (optional)") .' : <br><br>'."\n";
		echo '<select name="nouvelleheuredebut"> '."\n";
		echo '<OPTION VALUE="vide"></OPTION>'."\n";
		for ($i = 0; $i < 24; $i++) {
			echo '<OPTION VALUE="'.$i.'">'.$i.' H</OPTION>'."\n";
		}
		echo '</SELECT>'."\n";
		echo '<select name="nouvelleminutedebut"> '."\n";
		echo '<OPTION VALUE="vide"></OPTION>'."\n";
		echo '<OPTION VALUE="00">00</OPTION>'."\n";
		echo '<OPTION VALUE="15">15</OPTION>'."\n";
		echo '<OPTION VALUE="30">30</OPTION>'."\n";
		echo '<OPTION VALUE="45">45</OPTION>'."\n";
		echo '</SELECT>'."\n";
		echo '<br><br>'. _("Add a end hour (optional)") .' : <br><br>'."\n";
		echo '<select name="nouvelleheurefin"> '."\n";
		echo '<OPTION VALUE="vide"></OPTION>'."\n";
		for ($i = 0; $i < 24; $i++) {
			echo '<OPTION VALUE="'.$i.'">'.$i.' H</OPTION>'."\n";
		}
		echo '</SELECT>'."\n";
		echo '<select name="nouvelleminutefin"> '."\n";
		echo '<OPTION VALUE="vide"></OPTION>'."\n";
		echo '<OPTION VALUE="00">00</OPTION>'."\n";
		echo '<OPTION VALUE="15">15</OPTION>'."\n";
		echo '<OPTION VALUE="30">30</OPTION>'."\n";
		echo '<OPTION VALUE="45">45</OPTION>'."\n";
		echo '</SELECT>'."\n";

		echo '<br><br><input type="image" name="retoursondage" value="Retourner au sondage" src="images/cancel.png"> '."\n";
		echo' <input type="submit" class="button" name="ajoutercolonne" value="'.dol_escape_htmltag($langs->trans("Add")).'">'."\n";
	}

	echo '</form>'."\n";
	echo '<br><br><br><br>'."\n";
	echo '</div>'."\n";

	exit;
}


echo '<div class="corps"> '."\n";

//affichage du titre du sondage
$titre=str_replace("\\","",$dsondage->titre);
echo '<strong>'.$titre.'</strong><br>'."\n";

//affichage du nom de l'auteur du sondage
echo $langs->trans("InitiatorOfPoll") .' : '.$dsondage->nom_admin.'<br>'."\n";

//affichage des commentaires du sondage
if ($dsondage->commentaires)
{
	echo '<br>'.$langs->trans("Description") .' :<br>'."\n";
	$commentaires=dol_nl2br($dsondage->commentaires);
	echo $commentaires;
	echo '<br>'."\n";
}

echo '</div>'."\n";


//recuperation des donnes de la base
$sql = 'SELECT * FROM '.MAIN_DB_PREFIX.'opensurvey_sondage WHERE id_sondage_admin = '.$connect->Param('numsondageadmin');
$sql = $connect->Prepare($sql);
$sondage = $connect->Execute($sql, array($numsondageadmin));

if ($sondage !== false)
{
	$sql = 'SELECT * FROM '.MAIN_DB_PREFIX.'opensurvey_sujet_studs WHERE id_sondage = '.$connect->Param('numsondage');
	$sql = $connect->Prepare($sql);
	$sujets = $connect->Execute($sql, array($numsondage));

	$sql = 'SELECT * FROM '.MAIN_DB_PREFIX.'opensurvey_user_studs WHERE id_sondage = '.$connect->Param('numsondage').' order by id_users';
	$sql = $connect->Prepare($sql);
	$user_studs = $connect->Execute($sql, array($numsondage));
}
else
{
	dol_print_error('',"This poll doesn't exist !");

	llxFooterSurvey();
	exit;
}

$dsujet=$sujets->FetchObject(false);
$dsondage=$sondage->FetchObject(false);

// Define format of choices
$toutsujet=explode(",",$object->sujet);
$listofanswers=array();
foreach ($toutsujet as $value)
{
	$tmp=explode('@',$value);
	$listofanswers[]=array('label'=>$tmp[0],'format'=>$tmp[1]);
}
$toutsujet=str_replace("@","<br>",$toutsujet);
$toutsujet=str_replace("°","'",$toutsujet);
$nbcolonnes=substr_count($dsujet->sujet,',')+1;

echo '<form name="formulaire" action="'.getUrlSondage($numsondageadmin, true).'" method="POST" onkeypress="javascript:process_keypress(event)">'."\n";
echo '<div class="cadre"> '."\n";
echo $langs->trans("PollAdminDesc",img_picto('','cancel.png@opensurvey'),img_picto('','add-16.png@opensurvey'));
echo '<br><br>'."\n";

//debut de l'affichage de résultats
echo '<table class="resultats">'."\n";

//reformatage des données des sujets du sondage
$toutsujet=explode(",",$dsujet->sujet);
echo '<tr>'."\n";
echo '<td></td>'."\n";
echo '<td></td>'."\n";

//boucle pour l'affichage des boutons de suppression de colonne
for ($i = 0; isset($toutsujet[$i]); $i++) {
	echo '<td class=somme><input type="image" name="effacecolonne'.$i.'" value="Effacer la colonne" src="'.dol_buildpath('/opensurvey/img/cancel.png',1).'"></td>'."\n";
}

echo '</tr>'."\n";

//si le sondage est un sondage de date
if ($dsondage->format=="D"||$dsondage->format=="D+")
{
	//affichage des sujets du sondage
	echo '<tr>'."\n";
	echo '<td></td>'."\n";
	echo '<td></td>'."\n";

	//affichage des années
	$colspan=1;
	for ($i=0;$i<count($toutsujet);$i++)
	{
		$current = $toutsujet[$i];

		if (strpos($toutsujet[$i], '@') !== false) {
			$current = substr($toutsujet[$i], 0, strpos($toutsujet[$i], '@'));
		}

		if (isset($toutsujet[$i+1]) && strpos($toutsujet[$i+1], '@') !== false) {
			$next = substr($toutsujet[$i+1], 0, strpos($toutsujet[$i+1], '@'));
		} elseif (isset($toutsujet[$i+1])) {
			$next = $toutsujet[$i+1];
		}

		if (isset($toutsujet[$i+1]) && strftime("%Y",$current) == strftime("%Y",$next)){
			$colspan++;
		} else {
			echo '<td colspan='.$colspan.' class="annee">'.strftime("%Y", $current).'</td>'."\n";
			$colspan=1;
		}
	}

	echo '<td class="annee"><input type="image" name="ajoutsujet" src="'.dol_buildpath('/opensurvey/img/add-16.png',1).'"  alt="' . _('Add') . '"></td>'."\n";
	echo '</tr>'."\n";
	echo '<tr>'."\n";
	echo '<td></td>'."\n";
	echo '<td></td>'."\n";

	//affichage des mois
	$colspan = 1;
	for ($i = 0; $i < count($toutsujet); $i++) {
		$current = $toutsujet[$i];
		if (strpos($toutsujet[$i], '@') !== false) {
			$current = substr($toutsujet[$i], 0, strpos($toutsujet[$i], '@'));
		}

		if (isset($toutsujet[$i+1]) && strpos($toutsujet[$i+1], '@') !== false) {
			$next = substr($toutsujet[$i+1], 0, strpos($toutsujet[$i+1], '@'));
		} elseif (isset($toutsujet[$i+1])) {
			$next = $toutsujet[$i+1];
		}

		if (isset($toutsujet[$i+1]) && strftime("%B", $current) == strftime("%B", $next) && strftime("%Y", $current) == strftime("%Y", $next)){
			$colspan++;
		} else {
			if ($_SESSION["langue"]=="EN") {
				echo '<td colspan='.$colspan.' class="mois">'.date("F",$current).'</td>'."\n";
			} else {
				echo '<td colspan='.$colspan.' class="mois">'.strftime("%B",$current).'</td>'."\n";
			}

			$colspan=1;
		}
	}

	echo '<td class="mois"><input type="image" name="ajoutsujet" src="'.dol_buildpath('/opensurvey/img/add-16.png',1).'"  alt="' . _('Add') . '"></td>'."\n";
	echo '</tr>'."\n";
	echo '<tr>'."\n";
	echo '<td></td>'."\n";
	echo '<td></td>'."\n";

	//affichage des jours
	$colspan = 1;
	for ($i = 0; $i < count($toutsujet); $i++) {
		$current = $toutsujet[$i];

		if (strpos($toutsujet[$i], '@') !== false) {
			$current = substr($toutsujet[$i], 0, strpos($toutsujet[$i], '@'));
		}

		if (isset($toutsujet[$i+1]) && strpos($toutsujet[$i+1], '@') !== false) {
			$next = substr($toutsujet[$i+1], 0, strpos($toutsujet[$i+1], '@'));
		} elseif (isset($toutsujet[$i+1])) {
			$next = $toutsujet[$i+1];
		}

		if (isset($toutsujet[$i+1]) && strftime("%a %e",$current)==strftime("%a %e",$next)&&strftime("%B",$current)==strftime("%B",$next)){
			$colspan++;
		} else {
			if ($_SESSION["langue"]=="EN") {
				echo '<td colspan='.$colspan.' class="jour">'.date("D jS",$current).'</td>'."\n";
			} else {
				echo '<td colspan='.$colspan.' class="jour">'.strftime("%a %e",$current).'</td>'."\n";
			}

			$colspan=1;
		}
	}

	echo '<td class="jour"><input type="image" name="ajoutsujet" src="'.dol_buildpath('/opensurvey/img/add-16.png',1).'"  alt="' . _('Add') . '"></td>'."\n";
	echo '</tr>'."\n";

	//affichage des horaires
	if (strpos($dsujet->sujet,'@') !== false) {
		echo '<tr>'."\n";
		echo '<td></td>'."\n";
		echo '<td></td>'."\n";

		for ($i = 0; isset($toutsujet[$i]); $i++) {
			$heures=explode("@", $toutsujet[$i]);
			if (isset($heures[1])) {
				echo '<td class="heure">'.$heures[1].'</td>'."\n";
			} else {
				echo '<td class="heure"></td>'."\n";
			}
		}

		echo '<td class="heure"><input type="image" name="ajoutsujet" src="'.dol_buildpath('/opensurvey/img/add-16.png',1).'"  alt="' . _('Add') . '"></td>'."\n";
		echo '</tr>'."\n";
	}
}
else
{
	$toutsujet=str_replace("°","'",$toutsujet);

	//affichage des sujets du sondage
	echo '<tr>'."\n";
	echo '<td></td>'."\n";
	echo '<td></td>'."\n";

	for ($i = 0; isset($toutsujet[$i]); $i++)
	{
		$tmp=explode('@',$toutsujet[$i]);
		echo '<td class="sujet">'.$tmp[0].'</td>'."\n";
	}

	echo '<td class="sujet"><input type="image" name="ajoutsujet" src="'.dol_buildpath('/opensurvey/img/add-16.png',1).'"  alt="' . _('Add') . '"></td>'."\n";
	echo '</tr>'."\n";
}


// Loop on each answer
$somme = array();
$compteur = 0;
while ($data = $user_studs->FetchNextObject(false))
{
	$ensemblereponses = $data->reponses;

	echo '<tr>'."\n";
	echo '<td><input type="image" name="effaceligne'.$compteur.'" value="Effacer" src="'.dol_buildpath('/opensurvey/img/cancel.png',1).'"  alt="Icone efface"></td>'."\n";

	// Name
	$nombase=str_replace("°","'",$data->nom);
	echo '<td class="nom">'.$nombase.'</td>'."\n";

	// si la ligne n'est pas a changer, on affiche les données
	if (! $testligneamodifier)
	{
		for ($i = 0; $i < $nbcolonnes; $i++)
		{
			$car = substr($ensemblereponses, $i, 1);
			if (empty($listofanswers[$i]['format']) || $listofanswers[$i]['format'] == 'yesno')
			{
				if ($car == "1") echo '<td class="ok">OK</td>'."\n";
				else echo '<td class="non">KO</td>'."\n";
				// Total
				if (isset($somme[$i]) === false) $somme[$i] = 0;
				if ($car == "1") $somme[$i]++;
			}
			if (! empty($listofanswers[$i]['format']) && $listofanswers[$i]['format'] == 'pourcontre')
			{
				if ($car == "1") echo '<td class="ok">'.$langs->trans("For").'</td>'."\n";
				else if ($car =="0") echo '<td class="non">'.$langs->trans("Against").'</td>'."\n";
				else echo '<td class="vide">&nbsp;</td>'."\n";
				// Total
				if (isset($somme[$i]) === false) $somme[$i] = 0;
				if ($car == "1") $somme[$i]++;
			}
		}
	}
	else
	{ //sinon on remplace les choix de l'utilisateur par une ligne de checkbox pour recuperer de nouvelles valeurs
		if ($compteur == $ligneamodifier)
		{
			for ($i = 0; $i < $nbcolonnes; $i++)
			{
				$car = substr($ensemblereponses, $i, 1);
				echo '<td class="vide">';
				if (empty($listofanswers[$i]['format']) || $listofanswers[$i]['format'] == 'yesno')
				{
					print '<input type="checkbox" name="choix'.$i.'" value="1" ';
					if ($car == '1') echo 'checked="checked"';
					echo '>';
				}
				if (! empty($listofanswers[$i]['format']) && $listofanswers[$i]['format'] == 'pourcontre')
				{
					$arraychoice=array('2'=>'&nbsp;','0'=>$langs->trans("Against"),'1'=>$langs->trans("For"));
					print $form->selectarray("choix".$i, $arraychoice, $car);
				}
				print '</td>'."\n";
			}
		}
		else
		{
			for ($i = 0; $i < $nbcolonnes; $i++)
			{
				$car = substr($ensemblereponses, $i, 1);
				if (empty($listofanswers[$i]['format']) || $listofanswers[$i]['format'] == 'yesno')
				{
					if ($car == "1") echo '<td class="ok">OK</td>'."\n";
					else echo '<td class="non">&nbsp;</td>'."\n";
					// Total
					if (isset($somme[$i]) === false) $somme[$i] = 0;
					if ($car == "1") $somme[$i]++;
				}
				if (! empty($listofanswers[$i]['format']) && $listofanswers[$i]['format'] == 'pourcontre')
				{
					if ($car == "1") echo '<td class="ok">'.$langs->trans("For").'</td>'."\n";
					else if ($car == "0") echo '<td class="non">'.$langs->trans("Against").'</td>'."\n";
					else echo '<td class="vide">&nbsp;</td>'."\n";
					// Total
					if (isset($somme[$i]) === false) $somme[$i] = 0;
					if ($car == "1") $somme[$i]++;
				}
			}
		}
	}

	//a la fin de chaque ligne se trouve les boutons modifier
	if ($compteur != $ligneamodifier) {
		echo '<td class="casevide"><input type="submit" class="button" name="modifierligne'.$compteur.'" value="'.dol_escape_htmltag($langs->trans("Edit")).'"></td>'."\n";
	}

	//demande de confirmation pour modification de ligne
	for ($i = 0; $i < $nblignes; $i++) {
		if (isset($_POST["modifierligne$i"])) {
			if ($compteur == $i) {
				echo '<td class="casevide"><input type="submit" class="button" name="validermodifier'.$compteur.'" value="'.dol_escape_htmltag($langs->trans("Save")).'"></td>'."\n";
			}
		}
	}

	$compteur++;
	echo '</tr>'."\n";
}


// Add line to add new record
echo '<tr>'."\n";
echo '<td></td>'."\n";
echo '<td class="nom">'."\n";
echo '<input type="text" name="nom" maxlength="64">'."\n";
echo '</td>'."\n";

for ($i = 0; $i < $nbcolonnes; $i++)
{
	echo '<td class="vide">';
	if (empty($listofanswers[$i]['format']) || $listofanswers[$i]['format'] == 'yesno')
	{
		print '<input type="checkbox" name="choix'.$i.'" value="1"';
		if ( isset($_POST['choix'.$i]) && $_POST['choix'.$i] == '1' && is_error(NAME_EMPTY) )
		{
			echo ' checked="checked"';
		}
		echo '>';
	}
	if (! empty($listofanswers[$i]['format']) && $listofanswers[$i]['format'] == 'pourcontre')
	{
		$arraychoice=array('2'=>'&nbsp;','0'=>$langs->trans("Against"),'1'=>$langs->trans("For"));
		print $form->selectarray("choix".$i, $arraychoice);
	}
	print '</td>'."\n";
}

// Affichage du bouton de formulaire pour inscrire un nouvel utilisateur dans la base
echo '<td><input type="image" name="boutonp" value="'.$langs->trans("Vote").'" src="'.dol_buildpath('/opensurvey/img/add-24.png',1).'"></td>'."\n";
echo '</tr>'."\n";

// select best choice
for ($i=0; $i < $nbcolonnes + 1; $i++) {
	if (isset($somme[$i]) === true) {
		if ($i == "0") {
			$meilleurecolonne = $somme[$i];
		}

		if (isset($somme[$i]) && $somme[$i] > $meilleurecolonne){
			$meilleurecolonne = $somme[$i];
		}
	}
}


// Show line total
echo '<tr>'."\n";
echo '<td></td>'."\n";
echo '<td align="right">'. $langs->trans("Total") .'</td>'."\n";

for ($i = 0; $i < $nbcolonnes; $i++) {
	if (isset($somme[$i]) === true) {
		$affichesomme = $somme[$i];
	} else {
		$affichesomme = '';
	}

	if ($affichesomme == "") {
		$affichesomme = "0";
	}

	if (isset($somme[$i]) === true && isset($meilleurecolonne) === true && $somme[$i] == $meilleurecolonne){
		echo '<td class="somme">'.$affichesomme.'</td>'."\n";
	} else {
		echo '<td class="somme">'.$affichesomme.'</td>'."\n";
	}
}

echo '<tr>'."\n";
echo '<td></td>'."\n";
echo '<td class="somme"></td>'."\n";

for ($i = 0; $i < $nbcolonnes; $i++) {
	if (isset($somme[$i]) === true && isset($meilleurecolonne) === true && $somme[$i] == $meilleurecolonne){
		echo '<td class="somme"><img src="'.dol_buildpath('/opensurvey/img/medaille.png',1).'"></td>'."\n";
	} else {
		echo '<td class="somme"></td>'."\n";
	}
}

echo '</tr>'."\n";


// S'il a oublié de remplir un nom
if ((isset($_POST["boutonp"]) || isset($_POST["boutonp_x"])) && $_POST["nom"] == "") {
	echo '<tr>'."\n";
	print "<td colspan=10><font color=#FF0000>" . _("Enter a name !") . "</font>\n";
	echo '</tr>'."\n";
}

if (isset($erreur_prenom) && $erreur_prenom) {
	echo '<tr>'."\n";
	print "<td colspan=10><font color=#FF0000>" . _("The name you've chosen already exist in this poll!") . "</font></td>\n";
	echo '</tr>'."\n";
}

if (isset($erreur_injection) && $erreur_injection) {
	echo '<tr>'."\n";
	print "<td colspan=10><font color=#FF0000>" . _("Characters \"  '  < et > are not permitted") . "</font></td>\n";
	echo '</tr>'."\n";
}

if (isset($erreur_ajout_date) && $erreur_ajout_date) {
	echo '<tr>'."\n";
	print "<td colspan=10><font color=#FF0000>" . _("The date is not correct !") . "</font></td>\n";
	echo '</tr>'."\n";
}

//fin du tableau
echo '</table>'."\n";
echo '</div>'."\n";


$toutsujet = explode(",", $dsujet->sujet);

$compteursujet = 0;
$meilleursujet = '';
for ($i = 0; $i < $nbcolonnes; $i++) {
	if (isset($somme[$i]) === true && isset($meilleurecolonne) === true && $somme[$i] == $meilleurecolonne){
		$meilleursujet.=", ";

		if ($dsondage->format == "D" || $dsondage->format == "D+") {
			$meilleursujetexport = $toutsujet[$i];

			if (strpos($toutsujet[$i], '@') !== false) {
				$toutsujetdate = explode("@", $toutsujet[$i]);
				$meilleursujet .= dol_print_date($toutsujetdate[0],'daytext'). ' ('.dol_print_date($toutsujetdate[0],'%A').')' . _("for")  . ' ' . $toutsujetdate[1];
			} else {
				$meilleursujet .= dol_print_date($toutsujet[$i],'daytext'). ' ('.dol_print_date($toutsujet[$i],'%A').')';
			}
		}
		else
		{
			$tmp=explode('@',$toutsujet[$i]);
			$meilleursujet.=$tmp[0];
		}

		$compteursujet++;
	}
}

//adaptation pour affichage des valeurs
$meilleursujet = substr("$meilleursujet", 1);
$meilleursujet = str_replace("°", "'", $meilleursujet);

//ajout du S si plusieurs votes
$vote_str = _('vote');
if (isset($meilleurecolonne) && $meilleurecolonne > 1) {
	$vote_str = _('votes');
}

echo '<p class=affichageresultats>'."\n";

//affichage de la phrase annoncant le meilleur sujet
if (isset($meilleurecolonne) && $compteursujet == "1") {
	print "<img src=\"".dol_buildpath('/opensurvey/img/medaille.png',1)."\" alt=\"Meilleur resultat\"> " . $langs->trans('TheBestChoice') . " : <b>$meilleursujet </b>" . $langs->trans("with") . " <b>$meilleurecolonne </b>" . $vote_str . ".<br>\n";
} elseif (isset($meilleurecolonne)) {
	print "<img src=\"".dol_buildpath('/opensurvey/img/medaille.png',1)."\" alt=\"Meilleur resultat\"> " . $langs->trans('TheBestChoices') . " : <b>$meilleursujet </b>" . $langs->trans("with") . " <b>$meilleurecolonne </b>" . $vote_str . ".<br>\n";
}

echo '<br>'."\n";

echo '</form>'."\n";

llxFooterSurvey();

$db->close();
