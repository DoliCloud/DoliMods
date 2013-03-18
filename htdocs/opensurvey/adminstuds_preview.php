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
$sondage = false;
$numsondageadmin=GETPOST("sondage");
$numsondage=substr($numsondageadmin, 0, 16);
$object=new Opensurveysondage($db);
$object->fetch(0,$numsondageadmin);

// TODO Remove this
if (preg_match(";[\w\d]{24};i", $numsondageadmin))
{
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
}

if (is_object($sujets)) $dsujet=$sujets->FetchObject(false);
if (is_object($sondage)) $dsondage=$sondage->FetchObject(false);

$nbcolonnes = substr_count($dsujet->sujet, ',') + 1;
if (is_object($user_studs)) $nblignes = $user_studs->RecordCount();



/*
 * Actions
 */

// Add vote
if (isset($_POST["boutonp"]) || isset($_POST["boutonp_x"]))
{
	if (issetAndNoEmpty('nom'))
	{
		$erreur_prenom = false;

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
			$sql = 'UPDATE '.MAIN_DB_PREFIX."opensurvey_user_studs SET reponses = '".$db->escape($nouveauchoix)."' WHERE nom = '".$db->escape($data->nom)."' AND id_users = '".$db->escape($data->id_users)."'";
			$sql = $db->query($resql);
		}

		$compteur++;
	}
}

//action quand on ajoute une colonne au format AUTRE
if (isset($_POST["ajoutercolonne"]) && issetAndNoEmpty('nouvellecolonne') && ($dsondage->format == "A" || $dsondage->format == "A+"))
{
	$nouveauxsujets=$dsujet->sujet;

	//on rajoute la valeur a la fin de tous les sujets deja entrés
	$nouveauxsujets.=',';
	$nouveauxsujets.=str_replace(array(",","@"), " ", $_POST["nouvellecolonne"]).(empty($_POST["typecolonne"])?'':'@'.$_POST["typecolonne"]);

	//mise a jour avec les nouveaux sujets dans la base
	$sql = 'UPDATE '.MAIN_DB_PREFIX."opensurvey_sujet_studs SET sujet = '".$db->escape($nouveauxsujets)."' WHERE id_sondage = '".$db->escape($numsondage)."'";
	$resql = $db->query($sql);
}

// Add column with format DATE
if (isset($_POST["ajoutercolonne"]) && ($dsondage->format == "D" || $dsondage->format == "D+"))
{
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


// Delete line
for ($i = 0; $i < $nblignes; $i++)
{
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


// Delete column
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

$result=$object->fetch(0,$numsondage);
if ($result <= 0)
{
	print $langs->trans("ErrorRecordNotFound");
	llxFooter();
	exit;
}

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


print '<form name="formulaire4" action="#" method="POST" onkeypress="javascript:process_keypress(event)">'."\n";

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

// Type
$type=($dsondage->format=="A"||$dsondage->format=="A+")?'classic':'date';
print '<tr><td>'.$langs->trans("Type").'</td><td colspan="2">';
print img_picto('',dol_buildpath('/opensurvey/img/'.($type == 'classic'?'chart-32.png':'calendar-32.png'),1),'width="16"',1);
print ' '.$langs->trans($type=='classic'?"TypeClassic":"TypeDate").'</td></tr>';

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

print '</form>'."\n";

print '<div class="tabsAction">';

print '<a class="butAction" href="public/exportcsv.php?numsondage=' . $numsondage . '">'.$langs->trans("ExportSpreadsheet") .' (.CSV)' . '</a>';

print '</div>';


showlogo();

// reload
$dsujet=$sujets->FetchObject(false);
$dsondage=$sondage->FetchObject(false);


// Add form to add a field
if (GETPOST('ajoutsujet'))
{
	//on recupere les données et les sujets du sondage
	print '<form name="formulaire" action="'.$_SERVER["PHP_SELF"].'" method="POST">'."\n";
	print '<input type="hidden" name="sondage" value="'.$numsondageadmin.'">';
	print '<input type="hidden" name="backtourl" value="'.GETPOST('backtourl').'">';

	print '<div class="center">'."\n";
	print "<br><br>"."\n";

	// Add new column
	if ($dsondage->format=="A"||$dsondage->format=="A+")
	{
		print $langs->trans("AddNewColumn") .' :<br><br>';
		print $langs->trans("Titlprintice").' <input type="text" name="nouvellecolonne" size="40"><br>';
		$tmparray=array('checkbox'=>$langs->trans("CheckBox"),'yesno'=>$langs->trans("YesNoList"),'pourcontre'=>$langs->trans("PourContreList"));
		print $langs->trans("Type").' '.$form->selectarray("typecolonne", $tmparray, GETPOST('typecolonne')).'<br><br>';
		print '<input type="submit" class="button" name="ajoutercolonne" value="'.dol_escape_htmltag($langs->trans("Add")).'">';
		print ' &nbsp; &nbsp; ';
		print '<input type="submit" class="button" name="cancel" value="'.dol_escape_htmltag($langs->trans("Cancel")).'">';
		print '<br><br>'."\n";
	}
	else
	{
		//ajout d'une date avec creneau horaire
		//print _("You can add a new scheduling date to your poll.<br> If you just want to add a new hour to an existant date, put the same date and choose a new hour.") .'<br><br> '."\n";
		print $langs->trans("AddADate") .' :<br><br>'."\n";
		print '<select name="nouveaujour"> '."\n";
		print '<OPTION VALUE="vide"></OPTION>'."\n";
		for ($i=1;$i<32;$i++){
			print '<OPTION VALUE="'.$i.'">'.$i.'</OPTION>'."\n";
		}
		print '</SELECT>'."\n";

		print '<select name="nouveaumois"> '."\n";
		print '<OPTION VALUE="vide"></OPTION>'."\n";
		for($i = 1; $i < 13; $i++) {
			print '<OPTION VALUE="'.$i.'">'.strftime('%B', mktime(0, 0, 0, $i)).'</OPTION>'."\n";
		}
		print '</SELECT>'."\n";


		print '<select name="nouvelleannee"> '."\n";
		print '<OPTION VALUE="vide"></OPTION>'."\n";
		for ($i = date("Y"); $i < (date("Y") + 5); $i++) {
			print '<OPTION VALUE="'.$i.'">'.$i.'</OPTION>'."\n";
		}
		print '</SELECT>'."\n";
		print '<br><br>'. $langs->trans("AddStartHour") .' : <br><br>'."\n";
		print '<select name="nouvelleheuredebut"> '."\n";
		print '<OPTION VALUE="vide"></OPTION>'."\n";
		for ($i = 0; $i < 24; $i++) {
			print '<OPTION VALUE="'.$i.'">'.$i.' H</OPTION>'."\n";
		}
		print '</SELECT>'."\n";
		print '<select name="nouvelleminutedebut"> '."\n";
		print '<OPTION VALUE="vide"></OPTION>'."\n";
		print '<OPTION VALUE="00">00</OPTION>'."\n";
		print '<OPTION VALUE="15">15</OPTION>'."\n";
		print '<OPTION VALUE="30">30</OPTION>'."\n";
		print '<OPTION VALUE="45">45</OPTION>'."\n";
		print '</SELECT>'."\n";
		print '<br><br>'. $langs->trans("AddEndHour") .' : <br><br>'."\n";
		print '<select name="nouvelleheurefin"> '."\n";
		print '<OPTION VALUE="vide"></OPTION>'."\n";
		for ($i = 0; $i < 24; $i++) {
			print '<OPTION VALUE="'.$i.'">'.$i.' H</OPTION>'."\n";
		}
		print '</SELECT>'."\n";
		print '<select name="nouvelleminutefin"> '."\n";
		print '<OPTION VALUE="vide"></OPTION>'."\n";
		print '<OPTION VALUE="00">00</OPTION>'."\n";
		print '<OPTION VALUE="15">15</OPTION>'."\n";
		print '<OPTION VALUE="30">30</OPTION>'."\n";
		print '<OPTION VALUE="45">45</OPTION>'."\n";
		print '</SELECT>'."\n";

		print '<br><br>';
		print' <input type="submit" class="button" name="ajoutercolonne" value="'.dol_escape_htmltag($langs->trans("Add")).'">'."\n";
		print '&nbsp; &nbsp;';
		print '<input type="submit" class="button" name="retoursondage" value="'.$langs->trans("Cancel").'">';
	}

	print '</form>'."\n";
	print '<br><br><br><br>'."\n";
	print '</div>'."\n";

	exit;
}


print $langs->trans("PollAdminDesc",img_picto('','cancel.png@opensurvey'),img_picto('','add-16.png@opensurvey')).'<br><br>';

print '<div class="corps"> '."\n";

//affichage du titre du sondage
$titre=str_replace("\\","",$dsondage->titre);
print '<strong>'.$titre.'</strong><br>'."\n";

//affichage du nom de l'auteur du sondage
print $langs->trans("InitiatorOfPoll") .' : '.$dsondage->nom_admin.'<br>'."\n";

//affichage des commentaires du sondage
if ($dsondage->commentaires)
{
	print '<br>'.$langs->trans("Description") .' :<br>'."\n";
	$commentaires=dol_nl2br($dsondage->commentaires);
	print $commentaires;
	print '<br>'."\n";
}

print '</div>'."\n";


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

$dsujet=$sujets->FetchObject(false);
$dsondage=$sondage->FetchObject(false);

$nbcolonnes=substr_count($dsujet->sujet,',')+1;

print '<form name="formulaire" action="'.$_SERVER["PHP_SELF"].'" method="POST">'."\n";
print '<input type="hidden" name="sondage" value="'.$numsondageadmin.'">';

print '<div class="cadre"> '."\n";
print '<br>'."\n";

//debut de l'affichage de résultats
print '<table class="resultats">'."\n";

//reformatage des données des sujets du sondage
$toutsujet=explode(",",$dsujet->sujet);
$toutsujet=str_replace("°","'",$toutsujet);

print '<tr>'."\n";
print '<td></td>'."\n";
print '<td></td>'."\n";

//boucle pour l'affichage des boutons de suppression de colonne
for ($i = 0; isset($toutsujet[$i]); $i++) {
	print '<td class=somme><input type="image" name="effacecolonne'.$i.'" value="Effacer la colonne" src="'.dol_buildpath('/opensurvey/img/cancel.png',1).'"></td>'."\n";
}

print '</tr>'."\n";


// Show choice titles
if ($dsondage->format=="D"||$dsondage->format=="D+")
{
	//affichage des sujets du sondage
	print '<tr>'."\n";
	print '<td></td>'."\n";
	print '<td></td>'."\n";

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
			print '<td colspan='.$colspan.' class="annee">'.strftime("%Y", $current).'</td>'."\n";
			$colspan=1;
		}
	}

	print '<td class="annee"><a href="'.$_SERVER["PHP_SELF"].'?ajoutsujet=1&sondage='.$dsondage->id_sondage_admin.'">'.$langs->trans("Add").'</a></td>'."\n";
	print '</tr>'."\n";
	print '<tr>'."\n";
	print '<td></td>'."\n";
	print '<td></td>'."\n";

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
			print '<td colspan='.$colspan.' class="mois">'.strftime("%B",$current).'</td>'."\n";

			$colspan=1;
		}
	}

	print '<td class="mois"><a href="'.$_SERVER["PHP_SELF"].'?ajoutsujet=1&sondage='.$dsondage->id_sondage_admin.'">'.$langs->trans("Add").'</a></td>'."\n";
	print '</tr>'."\n";
	print '<tr>'."\n";
	print '<td></td>'."\n";
	print '<td></td>'."\n";

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
			print '<td colspan='.$colspan.' class="jour">'.strftime("%a %e",$current).'</td>'."\n";

			$colspan=1;
		}
	}

	print '<td class="jour"><a href="'.$_SERVER["PHP_SELF"].'?ajoutsujet=1&sondage='.$dsondage->id_sondage_admin.'">'.$langs->trans("Add").'</a></td>'."\n";
	print '</tr>'."\n";

	//affichage des horaires
	if (strpos($dsujet->sujet,'@') !== false) {
		print '<tr>'."\n";
		print '<td></td>'."\n";
		print '<td></td>'."\n";

		for ($i = 0; isset($toutsujet[$i]); $i++) {
			$heures=explode('@', $toutsujet[$i]);
			if (isset($heures[1])) {
				print '<td class="heure">'.$heures[1].'</td>'."\n";
			} else {
				print '<td class="heure"></td>'."\n";
			}
		}

		print '<td class="heure"><a href="'.$_SERVER["PHP_SELF"].'?ajoutsujet=1&sondage='.$dsondage->id_sondage_admin.'">'.$langs->trans("Add").'</a></td>'."\n";
		print '</tr>'."\n";
	}
}
else
{
	//affichage des sujets du sondage
	print '<tr>'."\n";
	print '<td></td>'."\n";
	print '<td></td>'."\n";

	for ($i = 0; isset($toutsujet[$i]); $i++)
	{
		$tmp=explode('@',$toutsujet[$i]);
		print '<td class="sujet">'.$tmp[0].'</td>'."\n";
	}

	print '<td class="sujet"><a href="'.$_SERVER["PHP_SELF"].'?sondage='.$numsondageadmin.'&ajoutsujet=1&backtourl='.urlencode($_SERVER["PHP_SELF"].'?sondage='.$numsondageadmin).'">'.img_picto('',dol_buildpath('/opensurvey/img/add-16.png',1),'',1).'</a></td>'."\n";
	print '</tr>'."\n";
}


// Loop on each answer
$sumfor = array();
$sumagainst = array();
$compteur = 0;
while ($data = $user_studs->FetchNextObject(false))
{
	$ensemblereponses = $data->reponses;

	print '<tr>'."\n";
	print '<td><input type="image" name="effaceligne'.$compteur.'" value="Effacer" src="'.dol_buildpath('/opensurvey/img/cancel.png',1).'"></td>'."\n";

	// Name
	$nombase=str_replace("°","'",$data->nom);
	print '<td class="nom">'.$nombase.'</td>'."\n";

	// si la ligne n'est pas a changer, on affiche les données
	if (! $testligneamodifier)
	{
		for ($i = 0; $i < $nbcolonnes; $i++)
		{
			$car = substr($ensemblereponses, $i, 1);
			if (empty($listofanswers[$i]['format']) || ! in_array($listofanswers[$i]['format'],array('yesno','pourcontre')))
			{
				if ($car == "1") print '<td class="ok">OK</td>'."\n";
				else print '<td class="non">KO</td>'."\n";
				// Total
				if (! isset($sumfor[$i])) $sumfor[$i] = 0;
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
	else
	{
		//sinon on remplace les choix de l'utilisateur par une ligne de checkbox pour recuperer de nouvelles valeurs
		if ($compteur == $ligneamodifier)
		{
			for ($i = 0; $i < $nbcolonnes; $i++)
			{
				$car = substr($ensemblereponses, $i, 1);
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
		}
		else
		{
			for ($i = 0; $i < $nbcolonnes; $i++)
			{
				$car = substr($ensemblereponses, $i, 1);
				if (empty($listofanswers[$i]['format']) || ! in_array($listofanswers[$i]['format'],array('yesno','pourcontre')))
				{
					if ($car == "1") print '<td class="ok">OK</td>'."\n";
					else print '<td class="non">&nbsp;</td>'."\n";
					// Total
					if (! isset($sumfor[$i])) $sumfor[$i] = 0;
					if ($car == "1") $sumfor[$i]++;
				}
				if (! empty($listofanswers[$i]['format']) && $listofanswers[$i]['format'] == 'yesno')
				{
					if ($car == "1") print '<td class="ok">'.$langs->trans("For").'</td>'."\n";
					else if ($car == "0") print '<td class="non">'.$langs->trans("Against").'</td>'."\n";
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
					else if ($car == "0") print '<td class="non">'.$langs->trans("Against").'</td>'."\n";
					else print '<td class="vide">&nbsp;</td>'."\n";
					// Total
					if (! isset($sumfor[$i])) $sumfor[$i] = 0;
					if (! isset($sumagainst[$i])) $sumagainst[$i] = 0;
					if ($car == "1") $sumfor[$i]++;
					if ($car == "0") $sumagainst[$i]++;
				}
			}
		}
	}

	//a la fin de chaque ligne se trouve les boutons modifier
	if ($compteur != $ligneamodifier) {
		print '<td class="casevide"><input type="submit" class="button" name="modifierligne'.$compteur.'" value="'.dol_escape_htmltag($langs->trans("Edit")).'"></td>'."\n";
	}

	//demande de confirmation pour modification de ligne
	for ($i = 0; $i < $nblignes; $i++) {
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
if (empty($testligneamodifier))
{
	print '<tr>'."\n";
	print '<td></td>'."\n";
	print '<td class="nom">'."\n";
	print '<input type="text" placeholder="'.dol_escape_htmltag($langs->trans("Name")).'" name="nom" maxlength="64" size="24">'."\n";
	print '</td>'."\n";

	for ($i = 0; $i < $nbcolonnes; $i++)
	{
		print '<td class="vide">';
		if (empty($listofanswers[$i]['format']) || ! in_array($listofanswers[$i]['format'],array('yesno','pourcontre')))
		{
			print '<input type="checkbox" name="choix'.$i.'" value="1"';
			if ( isset($_POST['choix'.$i]) && $_POST['choix'.$i] == '1' && is_error(NAME_EMPTY) )
			{
				print ' checked="checked"';
			}
			print '>';
		}
		if (! empty($listofanswers[$i]['format']) && $listofanswers[$i]['format'] == 'yesno')
		{
			$arraychoice=array('2'=>'&nbsp;','0'=>$langs->trans("No"),'1'=>$langs->trans("Yes"));
			print $form->selectarray("choix".$i, $arraychoice);
		}
		if (! empty($listofanswers[$i]['format']) && $listofanswers[$i]['format'] == 'pourcontre')
		{
			$arraychoice=array('2'=>'&nbsp;','0'=>$langs->trans("Against"),'1'=>$langs->trans("For"));
			print $form->selectarray("choix".$i, $arraychoice);
		}
		print '</td>'."\n";
	}

	// Affichage du bouton de formulaire pour inscrire un nouvel utilisateur dans la base
	print '<td><input type="image" name="boutonp" value="'.$langs->trans("Vote").'" src="'.dol_buildpath('/opensurvey/img/add-24.png',1).'"></td>'."\n";
	print '</tr>'."\n";
}

// Select value of best choice (for checkbox columns only)
$nbofcheckbox=0;
for ($i=0; $i < $nbcolonnes + 1; $i++)
{
	if (empty($listofanswers[$i]['format']) || ! in_array($listofanswers[$i]['format'],array('yesno','pourcontre')))
	$nbofcheckbox++;
	if (isset($sumfor[$i]))
	{
		if ($i == 0) {
			$meilleurecolonne = $sumfor[$i];
		}
		if (isset($sumfor[$i]) && $sumfor[$i] > $meilleurecolonne){
			$meilleurecolonne = $sumfor[$i];
		}
	}
}


// Show line total
print '<tr>'."\n";
print '<td></td>'."\n";
print '<td align="center">'. $langs->trans("Total") .'</td>'."\n";
for ($i = 0; $i < $nbcolonnes; $i++)
{
	$showsumfor = isset($sumfor[$i])?$sumfor[$i]:'';
	$showsumagainst = isset($sumagainst[$i])?$sumagainst[$i]:'';
	if (empty($showsumfor)) $showsumfor = 0;
	if (empty($showsumagainst)) $showsumagainst = 0;

	print '<td class="somme">';
	if (empty($listofanswers[$i]['format']) || ! in_array($listofanswers[$i]['format'],array('yesno','pourcontre'))) print $showsumfor;
	if (! empty($listofanswers[$i]['format']) && $listofanswers[$i]['format'] == 'pourcontre') print $langs->trans("For").': '.$showsumfor.'<br>'.$langs->trans("Against").': '.$showsumagainst;
	print '</td>'."\n";
}
print '</tr>';
// Show picto winner
if ($nbofcheckbox >= 2)
{
	print '<tr>'."\n";
	print '<td></td>'."\n";
	print '<td class="somme"></td>'."\n";
	for ($i = 0; $i < $nbcolonnes; $i++) {
		if (empty($listofanswers[$i]['format']) || ! in_array($listofanswers[$i]['format'],array('yesno','pourcontre')) && isset($sumfor[$i]) && isset($meilleurecolonne) && $sumfor[$i] == $meilleurecolonne)
		{
			print '<td class="somme"><img src="'.dol_buildpath('/opensurvey/img/medaille.png',1).'"></td>'."\n";
		} else {
			print '<td class="somme"></td>'."\n";
		}
	}
	print '</tr>'."\n";
}

// S'il a oublié de remplir un nom
if ((isset($_POST["boutonp"]) || isset($_POST["boutonp_x"])) && $_POST["nom"] == "") {
	print '<tr>'."\n";
	print "<td colspan=10><font color=#FF0000>" . _("Enter a name !") . "</font>\n";
	print '</tr>'."\n";
}

if (isset($erreur_prenom) && $erreur_prenom) {
	print '<tr>'."\n";
	print "<td colspan=10><font color=#FF0000>" . _("The name you've chosen already exist in this poll!") . "</font></td>\n";
	print '</tr>'."\n";
}

if (isset($erreur_injection) && $erreur_injection) {
	print '<tr>'."\n";
	print "<td colspan=10><font color=#FF0000>" . _("Characters \"  '  < et > are not permitted") . "</font></td>\n";
	print '</tr>'."\n";
}

if (isset($erreur_ajout_date) && $erreur_ajout_date) {
	print '<tr>'."\n";
	print "<td colspan=10><font color=#FF0000>" . _("The date is not correct !") . "</font></td>\n";
	print '</tr>'."\n";
}

//fin du tableau
print '</table>'."\n";
print '</div>'."\n";


$toutsujet = explode(",", $dsujet->sujet);

$compteursujet = 0;
$meilleursujet = '';
for ($i = 0; $i < $nbcolonnes; $i++) {
	if (isset($sumfor[$i]) === true && isset($meilleurecolonne) === true && $sumfor[$i] == $meilleurecolonne){
		$meilleursujet.=", ";

		if ($dsondage->format == "D" || $dsondage->format == "D+") {
			$meilleursujetexport = $toutsujet[$i];

			if (strpos($toutsujet[$i], '@') !== false) {
				$toutsujetdate = explode("@", $toutsujet[$i]);
				$meilleursujet .= dol_print_date($toutsujetdate[0],'daytext'). ' ('.dol_print_date($toutsujetdate[0],'%A').')' . ' - ' . $toutsujetdate[1];
			} else {
				$meilleursujet .= dol_print_date($toutsujet[$i],'daytext'). ' ('.dol_print_date($toutsujet[$i],'%A').')';
			}
		}
		else
		{
			$tmps=explode('@',$toutsujet[$i]);
			$meilleursujet .= $tmps[0];
		}

		$compteursujet++;
	}
}

//adaptation pour affichage des valeurs
$meilleursujet = substr("$meilleursujet", 1);
$meilleursujet = str_replace("°", "'", $meilleursujet);

// Show best choice
if ($nbofcheckbox >= 2)
{
	$vote_str = $langs->trans('votes');
	print '<p class=affichageresultats>'."\n";

	if (isset($meilleurecolonne) && $compteursujet == "1") {
		print "<img src=\"".dol_buildpath('/opensurvey/img/medaille.png',1)."\"> " . $langs->trans('TheBestChoice') . " : <b>$meilleursujet </b>" . $langs->trans("with") . " <b>$meilleurecolonne </b>" . $vote_str . ".<br>\n";
	} elseif (isset($meilleurecolonne)) {
		print "<img src=\"".dol_buildpath('/opensurvey/img/medaille.png',1)."\"> " . $langs->trans('TheBestChoices') . " : <b>$meilleursujet </b>" . $langs->trans("with") . " <b>$meilleurecolonne </b>" . $vote_str . ".<br>\n";
	}
	print '</p><br>'."\n";
}

print '</form>'."\n";

llxFooterSurvey();

$db->close();
?>