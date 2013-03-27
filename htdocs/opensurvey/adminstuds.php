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
if (GETPOST('sondage') && strlen(GETPOST('sondage')) == 24)
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

$expiredate=dol_mktime(0, 0, 0, GETPOST('expiremonth'), GETPOST('expireday'), GETPOST('expireyear'));


/*
 * Actions
 */


// Delete
if ($action == 'delete_confirm')
{
	$result=$object->delete($user,'',$numsondageadmin);

	header('Location: '.dol_buildpath('/opensurvey/list.php',1));
	exit();
}

// Update
if ($action == 'update')
{
	$error=0;

	if (! GETPOST('nouveautitre'))
	{
		setEventMessage($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Title")),'errors');
		$error++;
		$action = 'edit';
	}

	if (! $error)
	{
		$res=$object->fetch(0,$numsondageadmin);
		if ($res < 0) dol_print_error($db,$object->error);
	}

	if (! $error)
	{
		$object->titre = GETPOST('nouveautitre');
		$object->commentaires = GETPOST('nouveauxcommentaires');
		$object->mail_admin = GETPOST('nouvelleadresse');
		$object->date_fin = $expiredate;

		$res=$object->update($user);
		if ($res < 0)
		{
			setEventMessage($object->error,'errors');
			$action='edit';
		}
	}
}


// Add comment
if (GETPOST('ajoutcomment'))
{
	$error=0;

	if (! GETPOST('comment'))
	{
		$error++;
		setEventMessage($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Comment")),'errors');
	}
	if (! GETPOST('commentuser'))
	{
		$error++;
		setEventMessage($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("User")),'errors');
	}

	if (! $error)
	{
		$comment = GETPOST("comment");
		$comment_user = GETPOST('commentuser');

		$sql = "INSERT INTO ".MAIN_DB_PREFIX."opensurvey_comments (id_sondage, comment, usercomment)";
		$sql.= " VALUES ('".$db->escape($numsondage)."','".$db->escape($comment)."','".$db->escape($comment_user)."')";
		$resql = $db->query($sql);
		dol_syslog("sql=".$sql);
		if (! $resql)
		{
			$err |= COMMENT_INSERT_FAILED;
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


/*
 * View
 */

$form=new Form($db);

$arrayofjs=array();
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


print '<form name="updatesurvey" action="'.$_SERVER["PHP_SELF"].'?action=update&sondage='.$numsondageadmin.'" method="POST">'."\n";

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
}
else print $object->titre;
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
	print '<textarea name="nouveauxcommentaires" rows="7" cols="80">'.$object->commentaires.'</textarea>'."\n";
}
else print dol_nl2br($object->commentaires);
print '</td></tr>';

// EMail
print '<tr><td>'.$langs->trans("EMail") .'</td><td colspan="2">';
if ($action == 'edit')
{
	print '<input type="text" name="nouvelleadresse" size="40" value="'.$object->mail_admin.'">';
}
else print dol_print_email($object->mail_admin);
print '</td></tr>';

// Can edit other votes
print '<tr><td>'.$langs->trans('CanEditVotes').'</td><td colspan="2">'.yn(preg_match('/\+/',$object->format)).'</td></tr>';

// Expire date
print '<tr><td>'.$langs->trans('ExpireDate').'</td><td colspan="2">';
if ($action == 'edit') print $form->select_date($expiredate?$expiredate:$object->date_fin,'expire');
else print dol_print_date($object->date_fin,'day');
print '</td></tr>';


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

if ($action == 'edit') print '<center><br><input type="submit" class="button" name="save" value="'.dol_escape_htmltag($langs->trans("Save")).'"></center>';

print '</form>'."\n";

dol_fiche_end();


/*
 * Barre d'actions
 */
print '<div class="tabsAction">';

if ($action != 'edit') print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?action=edit&sondage=' . $numsondageadmin . '">'.$langs->trans("Modify") . '</a>';

if ($action != 'edit') print '<a class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?suppressionsondage=1&sondage='.$numsondageadmin.'&amp;action=delete">'.$langs->trans('Delete').'</a>';

print '</div>';

if ($action == 'delete')
{
	print $form->formconfirm($_SERVER["PHP_SELF"].'?&sondage='.$numsondageadmin, $langs->trans("RemovePoll"), $langs->trans("ConfirmRemovalOfPoll",$id), 'delete_confirm', '', '', 1);
}



print '<br>';


print '<form name="formulaire5" action="#" method="POST">'."\n";

print_fiche_titre($langs->trans("CommentsOfVoters"),'','');

// Comment list
$sql = 'SELECT id_comment, usercomment, comment';
$sql.= ' FROM '.MAIN_DB_PREFIX.'opensurvey_comments';
$sql.= " WHERE id_sondage='".$db->escape($numsondage)."'";
$sql.= " ORDER BY id_comment";
$resql = $db->query($sql);
$num_rows=$db->num_rows($resql);
if ($num_rows > 0)
{
	$i = 0;
	while ( $i < $num_rows)
	{
		$obj=$db->fetch_object($resql);
		print '<a href="'.dol_buildpath('/opensurvey/adminstuds.php',1).'?deletecomment='.$obj->id_comment.'&sondage='.$numsondageadmin.'"> '.img_picto('', 'delete.png').'</a> ';
		print $obj->usercomment.' : '.dol_nl2br($obj->comment)." <br>";
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