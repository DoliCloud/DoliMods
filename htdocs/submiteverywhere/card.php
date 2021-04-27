<?PHP
/* Copyright (C) 2005-2012 Laurent Destailleur  <eldy@uers.sourceforge.net>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 * or see http://www.gnu.org/
 */

/**
 *       \file       htdocs/submiteverywhere/card.php
 *       \ingroup    submitew
 *       \brief      Fiche message
 */

// Load Dolibarr environment
$res=0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (! $res && ! empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) $res=@include $_SERVER["CONTEXT_DOCUMENT_ROOT"]."/main.inc.php";
// Try main.inc.php into web root detected using web root caluclated from SCRIPT_FILENAME
$tmp=empty($_SERVER['SCRIPT_FILENAME'])?'':$_SERVER['SCRIPT_FILENAME'];$tmp2=realpath(__FILE__); $i=strlen($tmp)-1; $j=strlen($tmp2)-1;
while ($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i]==$tmp2[$j]) { $i--; $j--; }
if (! $res && $i > 0 && file_exists(substr($tmp, 0, ($i+1))."/main.inc.php")) $res=@include substr($tmp, 0, ($i+1))."/main.inc.php";
if (! $res && $i > 0 && file_exists(dirname(substr($tmp, 0, ($i+1)))."/main.inc.php")) $res=@include dirname(substr($tmp, 0, ($i+1)))."/main.inc.php";
// Try main.inc.php using relative path
if (! $res && file_exists("../main.inc.php")) $res=@include "../main.inc.php";
if (! $res && file_exists("../../main.inc.php")) $res=@include "../../main.inc.php";
if (! $res && file_exists("../../../main.inc.php")) $res=@include "../../../main.inc.php";
if (! $res) die("Include of main fails");

require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
require_once DOL_DOCUMENT_ROOT."/core/lib/functions2.lib.php";
require_once DOL_DOCUMENT_ROOT."/core/class/html.formother.class.php";
require_once DOL_DOCUMENT_ROOT."/core/class/html.formadmin.class.php";
dol_include_once("/submiteverywhere/core/lib/submiteverywhere.lib.php");
dol_include_once("/submiteverywhere/class/SubmitewMessage.class.php");

$id=GETPOST('id', 'int');
$action=GETPOST('action', 'aZ09');
$confirm=GETPOST('confirm');

$langs->load("mails");
$langs->load("submiteverywhere@submiteverywhere");

//if (! $user->rights->mailing->lire || $user->societe_id > 0) accessforbidden();

$message = '';

// Tableau des substitutions possibles
$substitutionarray=array(
'__ID__' => 'IdRecord',
'__EMAIL__' => 'EMail',
'__LASTNAME__' => 'Lastname',
'__FIRSTNAME__' => 'Firstname',
'__OTHER1__' => 'Other1',
'__OTHER2__' => 'Other2',
'__OTHER3__' => 'Other3',
'__OTHER4__' => 'Other4',
'__OTHER5__' => 'Other5',
'__SIGNATURE__'=>'Signature',
'__PERSONALIZED__'=>'Personalized'
);
$substitutionarrayfortest=array(
'__ID__' => 'TESTIdRecord',
'__EMAIL__' => 'TESTEMail',
'__LASTNAME__' => 'TESTLastname',
'__FIRSTNAME__' => 'TESTFirstname',
'__OTHER1__' => 'TESTOther1',
'__OTHER2__' => 'TESTOther2',
'__OTHER3__' => 'TESTOther3',
'__OTHER4__' => 'TESTOther4',
'__OTHER5__' => 'TESTOther5',
'__SIGNATURE__'=>'TESTSignature',
'__PERSONALIZED__'=>'TESTPersonalized'
);


// Action clone object
if ($action == 'confirm_clone' && $confirm == 'yes') {
	if (empty($_REQUEST["clone_content"]) && empty($_REQUEST["clone_receivers"])) {
		$mesg='<div class="error">'.$langs->trans("NoCloneOptionsSpecified").'</div>';
	} else {
		$object=new SubmitewMessage($db);
		$result=$object->createFromClone($_REQUEST['id'], $_REQUEST["clone_content"], $_REQUEST["clone_receivers"]);
		if ($result > 0) {
			header("Location: ".$_SERVER['PHP_SELF'].'?id='.$result);
			exit;
		} else {
			$mesg=$object->error;
			$action='';
		}
	}
}

// Action send emailing for everybody
if ($action == 'sendallconfirmed' && $confirm == 'yes') {
	$object=new SubmitewMessage($db);
	$result=$object->fetch($id);

	$upload_dir = $conf->submiteverywhere->dir_output . "/" . get_exdir($object->id, 2, 0, 1, $object, 'submiteverywhere');

	if ($object->statut == 0) {
		dol_print_error('', 'ErrorMessageIsNotValidated');
		exit;
	}

	$id       = $object->id;
	$subject  = $object->title;
	$message  = $object->body;
	$from     = $object->email_from;
	$replyto  = $object->email_replyto;
	$errorsto = $object->email_errorsto;
	// Le message est-il en html
	$msgishtml=-1;	// Unknown by default
	if (preg_match('/[\s\t]*<html>/i', $message)) $msgishtml=1;

	// Warning, we must not use begin-commit transaction here
	// because we want to save update for each mail sent.

	$nbok=0; $nbko=0;

	// On choisit les cibles non deja envoyes pour ce message (statut=0)
	// ou envoyes en erreur (statut=-1)
	$sql = "SELECT mc.rowid, mc.label, mc.targetcode, mc.langcode, mc.source_url, mc.login, mc.pass, mc.comment, mc.position, mc.titlelength, mc.descshortlength, mc.desclonglength";
	$sql .= " FROM ".MAIN_DB_PREFIX."submitew_targets as mc";
	$sql .= " WHERE mc.statut < 1 AND mc.fk_mailing = ".$id;

	dol_syslog("card.php: select targets sql=".$sql, LOG_DEBUG);
	$resql=$db->query($sql);
	if ($resql) {
		$num = $db->num_rows($resql);	// nb of possible recipients

		if ($num) {
			dol_syslog("comm/mailing/card.php: nb of targets = ".$num, LOG_DEBUG);

			// Positionne date debut envoi
			$sql="UPDATE ".MAIN_DB_PREFIX."mailing SET date_envoi=".$db->idate(gmmktime())." WHERE rowid=".$id;
			$resql2=$db->query($sql);
			if (! $resql2) {
				dol_print_error($db);
			}

			// Boucle sur chaque adresse et envoie le mail
			$i = 0;

			while ($i < $num && $i < $conf->global->MAILING_LIMIT_SENDBYWEB) {
				$res=1;

				$obj = $db->fetch_object($resql);

				// Make substitutions on topic and body. From (AA=YY;BB=CC;...) we keep YY, CC, ...
				$other=explode(';', $obj->other);
				$tmpfield=explode('=', $other[0], 2); $other1=(isset($tmpfield[1])?$tmpfield[1]:$tmpfield[0]);
				$tmpfield=explode('=', $other[1], 2); $other2=(isset($tmpfield[1])?$tmpfield[1]:$tmpfield[0]);
				$tmpfield=explode('=', $other[2], 2); $other3=(isset($tmpfield[1])?$tmpfield[1]:$tmpfield[0]);
				$tmpfield=explode('=', $other[3], 2); $other4=(isset($tmpfield[1])?$tmpfield[1]:$tmpfield[0]);
				$tmpfield=explode('=', $other[4], 2); $other5=(isset($tmpfield[1])?$tmpfield[1]:$tmpfield[0]);
				$substitutionarray=array(
					'__ID__' => $obj->source_id,
					'__EMAIL__' => $obj->email,
					'__OTHER1__' => $other1,
					'__OTHER2__' => $other2,
					'__OTHER3__' => $other3,
					'__OTHER4__' => $other4,
					'__OTHER5__' => $other5
				);

				$substitutionisok=true;
				complete_substitutions_array($substitutionarray, $langs);
				$newsubject=make_substitutions($subject, $substitutionarray);
				$newmessage=make_substitutions($message, $substitutionarray);

				$arr_file = array();
				$arr_mime = array();
				$arr_name = array();
				$arr_css  = array();

				$listofpaths=dol_dir_list($upload_dir, 'all', 0, '', '', 'name', SORT_ASC, 0);
				if (count($listofpaths)) {
					foreach ($listofpaths as $key => $val) {
						$arr_file[]=$listofpaths[$key]['fullname'];
						$arr_mime[]=dol_mimetype($listofpaths[$key]['name']);
						$arr_name[]=$listofpaths[$key]['name'];
					}
				}

				// TODO

				// Fabrication du mail
				//$mail = new CMailFile($newsubject, $sendto, $from, $newmessage,
				//						$arr_file, $arr_mime, $arr_name,
				//						'', '', 0, $msgishtml, $errorsto, $arr_css);

				if ($mail->error) {
					$res=0;
				}
				if (! $substitutionisok) {
					$mail->error='Some substitution failed';
					$res=0;
				}

				// Send mail
				if ($res) {
					$res=$mail->sendfile();
				}

				if ($res) {
					// Mail successful
					$nbok++;

					dol_syslog("comm/mailing/card.php: ok for #".$i.($mail->error?' - '.$mail->error:''), LOG_DEBUG);

					$sql="UPDATE ".MAIN_DB_PREFIX."mailing_cibles";
					$sql.=" SET statut=1, date_envoi=".$db->idate(gmmktime())." WHERE rowid=".$obj->rowid;
					$resql2=$db->query($sql);
					if (! $resql2) {
						dol_print_error($db);
					}
				} else {
					// Mail failed
					$nbko++;

					dol_syslog("comm/mailing/card.php: error for #".$i.($mail->error?' - '.$mail->error:''), LOG_WARNING);

					$sql="UPDATE ".MAIN_DB_PREFIX."mailing_cibles";
					$sql.=" SET statut=-1, date_envoi=".$db->idate(gmmktime())." WHERE rowid=".$obj->rowid;
					$resql2=$db->query($sql);
					if (! $resql2) {
						dol_print_error($db);
					}
				}

				$i++;
			}
		}

		// Loop finished, set global statut of mail
		if ($nbko > 0) {
			$statut=2;	// Status 'sent partially' (because at least one error)
		} else {
			if ($nbok >= $num) $statut=3;	// Send to everybody
			else $statut=2;	// Status 'sent partially' (because not send to everybody)
		}

		$sql="UPDATE ".MAIN_DB_PREFIX."mailing SET statut=".$statut." WHERE rowid=".$id;
		dol_syslog("comm/mailing/card.php: update global status sql=".$sql, LOG_DEBUG);
		$resql2=$db->query($sql);
		if (! $resql2) {
			dol_print_error($db);
		}
	} else {
		dol_syslog($db->error());
		dol_print_error($db);
	}
	$message='';
	$action = '';
}

// Action send test emailing
if ($action == 'send' && empty($_POST["cancel"])) {
	$object = new SubmitewMessage($db);
	$result=$object->fetch($id);

	$error=0;

	$upload_dir = $conf->mailing->dir_output . "/" . get_exdir($object->id, 2, 0, 1, $object, 'submiteverywhere');

	$object->sendto = $_POST["sendto"];
	if (! $object->sendto) {
		$message='<div class="error">'.$langs->trans("ErrorFieldRequired", $langs->trans("MailTo")).'</div>';
		$error++;
	}

	if (! $error) {
		// Le message est-il en html
		$msgishtml=-1;	// Inconnu par defaut
		if (preg_match('/[\s\t]*<html>/i', $message)) $msgishtml=1;

		// Pratique les substitutions sur le title et message
		$object->title=make_substitutions($object->title, $substitutionarrayfortest, $langs);
		$object->body=make_substitutions($object->body, $substitutionarrayfortest, $langs);

		$arr_file = array();
		$arr_mime = array();
		$arr_name = array();
		$arr_css  = array();

		// Ajout CSS
		if (!empty($object->bgcolor)) $arr_css['bgcolor'] = (preg_match('/^#/', $object->bgcolor)?'':'#').$object->bgcolor;
		if (!empty($object->bgimage)) $arr_css['bgimage'] = $object->bgimage;

		// Attached files
		$listofpaths=dol_dir_list($upload_dir, 'all', 0, '', '', 'name', SORT_ASC, 0);
		if (count($listofpaths)) {
			foreach ($listofpaths as $key => $val) {
				$arr_file[]=$listofpaths[$key]['fullname'];
				$arr_mime[]=dol_mimetype($listofpaths[$key]['name']);
				$arr_name[]=$listofpaths[$key]['name'];
			}
		}

		$mailfile = new CMailFile($object->title, $object->sendto, $object->email_from, $object->body,
		$arr_file, $arr_mime, $arr_name, '', '', 0, $msgishtml, $object->email_errorsto, $arr_css);

		$result=$mailfile->sendfile();
		if ($result) {
			$message='<div class="ok">'.$langs->trans("MailSuccessfulySent", $mailfile->getValidAddress($object->email_from, 2), $mailfile->getValidAddress($object->sendto, 2)).'</div>';
		} else {
			$message='<div class="error">'.$langs->trans("ResultKo").'<br>'.$mailfile->error.' '.$result.'</div>';
		}

		$action='';
		$id=$object->id;
	}
}

// Action add emailing
if ($action == 'add') {
	$message='';

	$object = new SubmitewMessage($db);

	$object->email_from     = trim($_POST["from"]);
	$object->email_replyto  = trim($_POST["replyto"]);
	$object->email_errorsto = trim($_POST["errorsto"]);
	$object->label          = trim($_POST["label"]);
	$object->title          = trim($_POST["title"]);
	$object->body           = trim($_POST["shortdesc"]);
	$object->body_long      = trim($_POST["longdesc"]);

	if (! $object->label) $message.=($message?'<br>':'').$langs->trans("ErrorFieldRequired", $langs->trans("MailTitle"));
	if (! $object->title) $message.=($message?'<br>':'').$langs->trans("ErrorFieldRequired", $langs->trans("MailTopic"));
	if (! $object->body)  $message.=($message?'<br>':'').$langs->trans("ErrorFieldRequired", $langs->trans("MailBody"));

	if (! $message) {
		if ($object->create($user) >= 0) {
			Header("Location: ".$_SERVER["PHP_SELF"]."?id=".$object->id);
			exit;
		}
		$message=$object->error;
	}

	$message='<div class="error">'.$message.'</div>';
	$action="create";
}

// Action update description of emailing
if ($action == 'setdesc' || $action == 'setfrom' || $action == 'setreplyto' || $action == 'seterrorsto') {
	$object = new SubmitewMessage($db);
	$object->fetch($id);

	$upload_dir = $conf->mailing->dir_output . "/" . get_exdir($object->id, 2, 0, 1, $object, 'submiteverywhere');

	if ($action == 'setdesc')     $object->label          = trim($_REQUEST["desc"]);
	if ($action == 'setfrom')     $object->email_from     = trim($_REQUEST["from"]);
	if ($action == 'setreplyto')  $object->email_replyto  = trim($_REQUEST["replyto"]);
	if ($action == 'seterrorsto') $object->email_errorsto = trim($_REQUEST["errorsto"]);

	if ($action == 'setdesc' && empty($object->label))      $message.=($message?'<br>':'').$langs->trans("ErrorFieldRequired", $langs->transnoentities("MailTitle"));
	if ($action == 'setfrom' && empty($object->email_from)) $message.=($message?'<br>':'').$langs->trans("ErrorFieldRequired", $langs->transnoentities("MailFrom"));

	if (! $message) {
		if ($object->update($user) >= 0) {
			Header("Location: ".$_SERVER["PHP_SELF"]."?id=".$object->id);
			exit;
		}
		$message=$object->error;
	}

	$message='<div class="error">'.$message.'</div>';
	$action="";
}

// Action update emailing
if (! empty($_POST["removedfileid"])) {
	$object = new SubmitewMessage($db);
	$object->fetch($id);

	$upload_dir = $conf->mailing->dir_output . "/" . get_exdir($object->id, 2, 0, 1, $object, 'submiteverywhere');

	$listofpaths=dol_dir_list($upload_dir, 'all', 0, '', '', 'name', SORT_ASC, 0);

	// Remove file
	$filenb=($_POST["removedfileid"]-1);
	if (isset($listofpaths[$filenb])) {
		$result=dol_delete_file($listofpaths[$filenb]['fullname'], 1);
	}

	$action="edit";
}

// Action update emailing
if ($action == 'update' && empty($_POST["removedfile"]) && empty($_POST["cancel"])) {
	require_once DOL_DOCUMENT_ROOT."/core/lib/files.lib.php";

	$object = new SubmitewMessage($db);
	$object->fetch($id);

	$isupload=0;

	// If upload file
	if (! empty($_POST["addfile"]) && ! empty($conf->global->MAIN_UPLOAD_DOC)) {
		$isupload=1;
		$upload_dir = $conf->mailing->dir_output."/".get_exdir($object->id, 2, 0, 1, $object, 'submiteverywhere');

		$mesg=dol_add_file_process($upload_dir, 0, 1);
	}

	if (! $isupload) {
		$object->title          = trim($_POST["title"]);
		$object->body           = trim($_POST["short_desc"]);
		$object->body_long      = trim($_POST["long_desc"]);

		if (! $object->title)     $message.=($message?'<br>':'').$langs->trans("ErrorFieldRequired", $langs->trans("MailTopic"));
		if (! $object->body)      $message.=($message?'<br>':'').$langs->trans("ErrorFieldRequired", $langs->trans("MailBody"));
		if (! $object->body_long) $message.=($message?'<br>':'').$langs->trans("ErrorFieldRequired", $langs->trans("MailBody"));

		if (! $message) {
			if ($object->update($user) >= 0) {
				Header("Location: ".$_SERVER["PHP_SELF"]."?id=".$object->id);
				exit;
			}
			$message=$object->error;
		}

		$message='<div class="error">'.$message.'</div>';
		$action="edit";
	} else {
		$action="edit";
	}
}

// Action confirmation validation
if ($action == 'confirm_valid') {
	if ($confirm == 'yes') {
		$object = new SubmitewMessage($db);

		if ($object->fetch($id) >= 0) {
			$object->valid($user);

			Header("Location: ".$_SERVER["PHP_SELF"]."?id=".$object->id);
			exit;
		} else {
			dol_print_error($db);
		}
	} else {
		Header("Location: card.php?id=".$_REQUEST["id"]);
		exit;
	}
}

// Resend
if ($action == 'confirm_reset') {
	if ($confirm == 'yes') {
		$object = new SubmitewMessage($db);

		if ($object->fetch($id) >= 0) {
			$db->begin();

			$result=$object->valid($user);
			if ($result > 0) {
				$result=$object->reset_targets_status($user);
			}

			if ($result > 0) {
				$db->commit();
				Header("Location: ".$_SERVER["PHP_SELF"]."?id=".$object->id);
				exit;
			} else {
				$mesg=$object->error;
				$db->rollback();
			}
		} else {
			dol_print_error($db);
		}
	} else {
		Header("Location: ".$_SERVER["PHP_SELF"]."?id=".$_REQUEST["id"]);
		exit;
	}
}

// Action confirmation suppression
if ($action == 'confirm_delete') {
	if ($_REQUEST["confirm"] == 'yes') {
		$object = new SubmitewMessage($db);
		$object->fetch($id);

		if ($object->delete($object->id)) {
			Header("Location: ".dol_buildpath("/submiteverywhere/list.php"));
			exit;
		}
	}
}

if (! empty($_POST["cancel"])) {
	$action = '';
}



/*
 * View
 */

$help_url='EN:Module_SubmitEveryWhere|FR:Module_SubmitEveryWhere_Fr|ES:M&oacute;dulo_SubmitEveryWhere';
llxHeader('', 'SubmitEveryWhere', $help_url);

$html = new Form($db);
$htmlother = new FormOther($db);
$formadmin = new FormAdmin($db);
$object = new SubmitewMessage($db);


if ($action == 'create') {
	// EMailing in creation mode
	print '<form name="new_mailing" action="'.$_SERVER["PHP_SELF"].'" method="post">'."\n";
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="add">';

	print_fiche_titre($langs->trans("NewMessage"));

	dol_htmloutput_mesg($message);

	print '<table class="border" width="100%">';
	print '<tr><td width="25%" class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" name="label" size="40" value="'.$_POST['label'].'"></td></tr>';
	print '</table>';
	print '</br><br>';

	print '<table class="border" width="100%">';

	print '<tr><td width="25%" class="fieldrequired">'.$langs->trans("Title").'</td><td><input class="flat" name="title" size="60" value="'.$_POST['title'].'"></td></tr>';

	print '<tr><td width="25%" class="fieldrequired">'.$langs->trans("Language").'</td><td>';
	print $formadmin->select_language($_POST['lang_id'], 'lang_id', 0, 0, 1);
	print '</td></tr>';

	print '<tr><td width="25%" class="fieldrequired" valign="top">'.$langs->trans("ShortContent").'<br>';
	/*print '<br><i>'.$langs->trans("CommonSubstitutions").':<br>';
	foreach($substitutionarray as $key => $val)
	{
		print $key.' = '.$langs->trans($val).'<br>';
	}
	print '</i>';*/
	print '</td>';
	print '<td>';
	// Editeur wysiwyg
	require_once DOL_DOCUMENT_ROOT."/core/class/doleditor.class.php";
	$doleditor=new DolEditor('shortdesc', $_POST['shortdesc'], '', 180, 'dolibarr_mailings', '', true, true, 0 && $conf->fckeditor->enabled, 5, '90%');
	$doleditor->Create();
	print '</td></tr>';

	print '<tr><td width="25%" class="fieldrequired" valign="top">'.$langs->trans("LongContent").'<br>';
	/*print '<br><i>'.$langs->trans("CommonSubstitutions").':<br>';
	foreach($substitutionarray as $key => $val)
	{
		print $key.' = '.$langs->trans($val).'<br>';
	}
	print '</i>';*/
	print '</td>';
	print '<td>';
	// Editeur wysiwyg
	require_once DOL_DOCUMENT_ROOT."/core/class/doleditor.class.php";
	$doleditor=new DolEditor('longdesc', $_POST['longdesc'], '', 260, 'dolibarr_mailings', '', true, true, 0 && $conf->fckeditor->enabled, 20, '90%');
	$doleditor->Create();
	print '</td></tr>';

	print '</table>';

	print '<br><center><input type="submit" class="button" value="'.$langs->trans("CreateMessage").'"></center>';

	print '</form>';
} else {
	if ($object->fetch($id) >= 0) {
		$upload_dir = $conf->mailing->dir_output . "/" . get_exdir($object->id, 2, 0, 1, $object, 'submiteverywhere');

		$head = submitew_prepare_head($object);

		dol_fiche_head($head, 'card', $langs->trans("Mailing"), 0, 'email');

		if ($message) print $message."<br>";

		// Confirmation de la validation du mailing
		if ($_GET["action"] == 'valid') {
			print $html->formconfirm($_SERVER["PHP_SELF"]."?id=".$object->id, $langs->trans("ValidMailing"), $langs->trans("ConfirmValidMailing"), "confirm_valid", '', '', 1);
		}

		// Confirm reset
		if ($_GET["action"] == 'reset') {
			print $html->formconfirm($_SERVER["PHP_SELF"]."?id=".$object->id, $langs->trans("ResetMailing"), $langs->trans("ConfirmResetMailing", $object->ref), "confirm_reset", '', '', 2);
		}

		// Confirm delete
		if ($_GET["action"] == 'delete') {
			print $html->formconfirm($_SERVER["PHP_SELF"]."?id=".$object->id, $langs->trans("DeleteAMailing"), $langs->trans("ConfirmDeleteMailing"), "confirm_delete", '', '', 1);
		}


		if ($_GET["action"] != 'edit') {
			/*
			 * Mailing en mode visu
			 */
			if ($_GET["action"] == 'sendall') {
				// Define message to recommand from command line

				// Pour des raisons de securite, on ne permet pas cette fonction via l'IHM,
				// on affiche donc juste un message

				if (empty($conf->global->MAILING_LIMIT_SENDBYWEB)) {
					// Pour des raisons de securite, on ne permet pas cette fonction via l'IHM,
					// on affiche donc juste un message
					$mesg.='<div class="warning">'.$langs->trans("MailingNeedCommand").'</div>';
					$mesg.='<br><textarea cols="60" rows="'.ROWS_2.'" wrap="soft">php ./scripts/emailings/mailing-send.php '.$_GET["id"].'</textarea>';
					$mesg.='<br><br><div class="warning">'.$langs->trans("MailingNeedCommand2").'</div>';
					$_GET["action"]='';
				} else {
					$text='';
					if ($conf->file->mailing_limit_sendbyweb == 0) {
						$text.=$langs->trans("MailingNeedCommand");
						$text.='<br><textarea cols="60" rows="'.ROWS_2.'" wrap="soft">php ./scripts/emailings/mailing-send.php '.$_GET["id"].'</textarea>';
						$text.='<br><br>';
					}
					$text.=$langs->trans('ConfirmSendingEmailing').'<br>';
					$text.=$langs->trans('LimitSendingEmailing', $conf->global->MAILING_LIMIT_SENDBYWEB);
					print $html->formconfirm($_SERVER['PHP_SELF'].'?id='.$_REQUEST['id'], $langs->trans('SendMailing'), $text, 'sendallconfirmed', $formquestion, '', 1, 260);
				}
			}

			print '<table class="border" width="100%">';

			print '<tr><td width="25%">'.$langs->trans("Ref").'</td>';
			print '<td colspan="3">';
			print $html->showrefnav($object, 'id');
			print '</td></tr>';

			// Description
			print '<tr><td>'.$html->editfieldkey("MailTitle", 'desc', $object->label, 'id', $object->id, $user->rights->mailing->creer).'</td><td colspan="3">';
			print $html->editfieldval("MailTitle", 'desc', $object->label, $object, $user->rights->mailing->creer);
			print '</td></tr>';

			// From
			print '<tr><td>'.$html->editfieldkey("MailFrom", 'from', $object->email_from, 'id', $object->id, $user->rights->mailing->creer && $object->statut < 3, 'email').'</td><td colspan="3">';
			print $html->editfieldval("MailFrom", 'from', $object->email_from, $object, $user->rights->mailing->creer && $object->statut < 3, 'email');
			print '</td></tr>';

			// Errors to
			print '<tr><td>'.$html->editfieldkey("MailErrorsTo", 'errorsto', $object->email_errorsto, 'id', $object->id, $user->rights->mailing->creer && $object->statut < 3, 'email').'</td><td colspan="3">';
			print $html->editfieldval("MailErrorsTo", 'errorsto', $object->email_errorsto, $object, $user->rights->mailing->creer && $object->statut < 3, 'email');
			print '</td></tr>';

			// Status
			print '<tr><td width="25%">'.$langs->trans("Status").'</td><td colspan="3">'.$object->getLibStatut(4).'</td></tr>';

			// Nb of distinct emails
			print '<tr><td width="25%">';
			print $langs->trans("TotalNbOfDistinctRecipients");
			print '</td><td colspan="3">';
			$nbemail = ($object->nbemail?$object->nbemail:'<font class="error">'.$langs->trans("NoTargetYet").'</font>');
			if (!empty($conf->global->MAILING_LIMIT_SENDBYWEB) && is_numeric($nbemail) && $conf->global->MAILING_LIMIT_SENDBYWEB < $nbemail) {
				if ($conf->global->MAILING_LIMIT_SENDBYWEB > 0) {
					$text=$langs->trans('LimitSendingEmailing', $conf->global->MAILING_LIMIT_SENDBYWEB);
					print $html->textwithpicto($nbemail, $text, 1, 'warning');
				} else {
					$text=$langs->trans('NotEnoughPermissions');
					print $html->textwithpicto($nbemail, $text, 1, 'warning');
				}
			} else {
				print $nbemail;
			}
			print '</td></tr>';

			print '</table>';

			print "</div>";


			// Clone confirmation
			if ($_GET["action"] == 'clone') {
				// Create an array for form
				$formquestion=array(
					'text' => $langs->trans("ConfirmClone"),
				array('type' => 'checkbox', 'name' => 'clone_content',   'label' => $langs->trans("CloneContent"),   'value' => 1),
				array('type' => 'checkbox', 'name' => 'clone_receivers', 'label' => $langs->trans("CloneReceivers").' ('.$langs->trans("FeatureNotYetAvailable").')', 'value' => 0, 'disabled' => true)
				);
				// Paiement incomplet. On demande si motif = escompte ou autre
				print $html->formconfirm($_SERVER["PHP_SELF"].'?id='.$object->id, $langs->trans('CloneEMailing'), $langs->trans('ConfirmCloneEMailing', $object->ref), 'confirm_clone', $formquestion, 'yes');
				print '<br>';
			}


			if ($mesg) print $mesg;


			/*
			 * Boutons d'action
			 */

			if (GETPOST("cancel") || GETPOST("confirm")=='no' || GETPOST("action") == ''
			  || in_array(GETPOST('action', 'aZ09'), array('valid','delete','sendall'))) {
				print "\n\n<div class=\"tabsAction\">\n";

				if ($object->statut == 0 && $user->rights->mailing->creer) {
					print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=edit&amp;id='.$object->id.'">'.$langs->trans("EditMailing").'</a>';
				}

				//print '<a class="butAction" href="card.php?action=test&amp;id='.$object->id.'">'.$langs->trans("PreviewMailing").'</a>';

				print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=test&amp;id='.$object->id.'">'.$langs->trans("TestMailing").'</a>';

				if ($object->statut == 0) {
					if ($object->nbemail <= 0) {
						print '<a class="butActionRefused" href="#" title="'.dol_escape_htmltag($langs->trans("NoTargetYet")).'">'.$langs->trans("ValidMailing").'</a>';
					} elseif (empty($user->rights->mailing->valider)) {
						print '<a class="butActionRefused" href="#" title="'.dol_escape_htmltag($langs->trans("NotEnoughPermissions")).'">'.$langs->trans("ValidMailing").'</a>';
					} else {
						print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=valid&amp;id='.$object->id.'">'.$langs->trans("ValidMailing").'</a>';
					}
				}

				if (($object->statut == 1 || $object->statut == 2) && $object->nbemail > 0 && $user->rights->mailing->valider) {
					if ($conf->global->MAILING_LIMIT_SENDBYWEB < 0) {
						print '<a class="butActionRefused" href="#" title="'.dol_escape_htmltag($langs->trans("NotEnoughPermissions")).'">'.$langs->trans("SendMailing").'</a>';
					} else {
						print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=sendall&amp;id='.$object->id.'">'.$langs->trans("SendMailing").'</a>';
					}
				}

				if ($user->rights->mailing->creer) {
					print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=clone&amp;object=emailing&amp;id='.$object->id.'">'.$langs->trans("ToClone").'</a>';
				}

				if (($object->statut == 2 || $object->statut == 3) && $user->rights->mailing->valider) {
					print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=reset&amp;id='.$object->id.'">'.$langs->trans("ResetMailing").'</a>';
				}

				if (($object->statut <= 1 && $user->rights->mailing->creer) || $user->rights->mailing->supprimer) {
					print '<a class="butActionDelete" href="'.$_SERVER['PHP_SELF'].'?action=delete&amp;token='.newToken().'&amp;id='.$object->id.'">'.$langs->trans("DeleteMailing").'</a>';
				}

				print '<br><br></div>';
			}

			// Affichage formulaire de TEST
			if ($_GET["action"] == 'test') {
				print_titre($langs->trans("TestMailing"));

				// Create l'objet formulaire mail
				include_once DOL_DOCUMENT_ROOT.'/core/class/html.formmail.class.php';
				$formmail = new FormMail($db);
				$formmail->fromname = $object->email_from;
				$formmail->frommail = $object->email_from;
				$formmail->trackid='testsubmiteverwhere'.$object->id;
				if (! empty($conf->global->MAIN_EMAIL_ADD_TRACK_ID) && ($conf->global->MAIN_EMAIL_ADD_TRACK_ID & 2)) {	// If bit 2 is set
					include DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
					$formmail->frommail=dolAddEmailTrackId($formmail->frommail, 'testsubmiteverwhere'.$object->id);
				}
				$formmail->withsubstit=1;
				$formmail->withfrom=0;
				$formmail->withto=$user->email?$user->email:1;
				$formmail->withtocc=0;
				$formmail->withtoccc=$conf->global->MAIN_EMAIL_USECCC;
				$formmail->withtopic=0;
				$formmail->withtopicreadonly=1;
				$formmail->withfile=0;
				$formmail->withbody=0;
				$formmail->withbodyreadonly=1;
				$formmail->withcancel=1;
				$formmail->withdeliveryreceipt=0;
				// Tableau des substitutions
				$formmail->substit=$substitutionarrayfortest;
				// Tableau des parametres complementaires du post
				$formmail->param["action"]="send";
				$formmail->param["models"]="body";
				$formmail->param["mailid"]=$object->id;
				$formmail->param["returnurl"]=DOL_URL_ROOT."/comm/mailing/card.php?id=".$object->id;

				// Init list of files
				if (! empty($_REQUEST["mode"]) && $_REQUEST["mode"]=='init') {
					$formmail->clear_attached_files();
				}

				$formmail->show_form();

				print '<br>';
			}

			// Print mail content
			print_fiche_titre($langs->trans("EMail"), '', '');
			print '<table class="border" width="100%">';

			// Subject
			print '<tr><td width="25%">'.$langs->trans("MailTopic").'</td><td colspan="3">'.$object->title.'</td></tr>';

			// Joined files
			$i='';
			//$i=0;
			//while ($i < 4)
			//{
			//	$i++;
				//$property='joined_file'.$i;
				print '<tr><td>'.$langs->trans("MailFile").' '.$i.'</td><td colspan="3">';
				// List of files
				$listofpaths=dol_dir_list($upload_dir, 'all', 0, '', '', 'name', SORT_ASC, 0);
			if (count($listofpaths)) {
				foreach ($listofpaths as $key => $val) {
					print img_mime($listofpaths[$key]['name']).' '.$listofpaths[$key]['name'];
					print '<br>';
				}
			} else {
				print $langs->trans("NoAttachedFiles").'<br>';
			}
				print '</td></tr>';
			//}

			// Background color
			/*print '<tr><td width="25%">'.$langs->trans("BackgroundColorByDefault").'</td><td colspan="3">';
			print $htmlother->selectColor($object->bgcolor,'bgcolor','edit_mailing',0);
			print '</td></tr>';*/

			// Message
			print '<tr><td class="tdtop">'.$langs->trans("MailMessage").'</td>';
			print '<td colspan="3" bgcolor="'.($object->bgcolor?(preg_match('/^#/', $object->bgcolor)?'':'#').$object->bgcolor:'white').'">';
			print dol_htmlentitiesbr($object->body);
			print '</td>';
			print '</tr>';

			print '</table>';
			print "<br>";
		} else {
			/*
			 * Mailing en mode edition
			 */

			if ($mesg) print $mesg."<br>";
			if ($message) print $message."<br>";

			print '<table class="border" width="100%">';

			print '<tr><td width="25%">'.$langs->trans("Ref").'</td><td colspan="3">'.$object->id.'</td></tr>';
			print '<tr><td width="25%">'.$langs->trans("MailTitle").'</td><td colspan="3">'.$object->label.'</td></tr>';
			print '<tr><td width="25%">'.$langs->trans("MailFrom").'</td><td colspan="3">'.dol_print_email($object->email_from, 0, 0, 0, 0, 1).'</td></tr>';
			print '<tr><td width="25%">'.$langs->trans("MailErrorsTo").'</td><td colspan="3">'.dol_print_email($object->email_errorsto, 0, 0, 0, 0, 1).'</td></tr>';

			// Status
			print '<tr><td width="25%">'.$langs->trans("Status").'</td><td colspan="3">'.$object->getLibStatut(4).'</td></tr>';

			// Nb of distinct emails
			print '<tr><td width="25%">';
			print $langs->trans("TotalNbOfDistinctRecipients");
			print '</td><td colspan="3">';
			$nbemail = ($object->nbemail?$object->nbemail:'<font class="error">'.$langs->trans("NoTargetYet").'</font>');
			if (!empty($conf->global->MAILING_LIMIT_SENDBYWEB) && is_numeric($nbemail) && $conf->global->MAILING_LIMIT_SENDBYWEB < $nbemail) {
				$text=$langs->trans('LimitSendingEmailing', $conf->global->MAILING_LIMIT_SENDBYWEB);
				print $html->textwithpicto($nbemail, $text, 1, 'warning');
			} else {
				print $nbemail;
			}
			print '</td></tr>';

			print '</table>';
			print "</div>";

			print "\n";
			print '<form name="edit_mailing" action="card.php" method="post" enctype="multipart/form-data">'."\n";
			print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
			print '<input type="hidden" name="action" value="update">';
			print '<input type="hidden" name="id" value="'.$object->id.'">';

			// Print mail content
			print_fiche_titre($langs->trans("EMail"), '', '');
			print '<table class="border" width="100%">';

			// Subject
			print '<tr><td width="25%" class="fieldrequired">'.$langs->trans("MailTopic").'</td><td colspan="3"><input class="flat" type="text" size=60 name="title" value="'.$object->title.'"></td></tr>';

			// Joined files
			$i='';
			//$i=0;
			//while ($i < 4)
			//{
			//	$i++;
				//$property='joined_file'.$i;
				print '<tr><td>'.$langs->trans("MailFile").' '.$i.'</td>';
				print '<td colspan="3">';
				// List of files
				$listofpaths=dol_dir_list($upload_dir, 'all', 0, '', '', 'name', SORT_ASC, 0);
			if (count($listofpaths)) {
				foreach ($listofpaths as $key => $val) {
					print img_mime($listofpaths[$key]['name']).' '.$listofpaths[$key]['name'];
					print ' <input type="image" style="border: 0px;" src="'.DOL_URL_ROOT.'/theme/'.$conf->theme.'/img/delete.png" value="removedfile" name="removedfile" />';
					print '<input type="hidden" name="removedfileid" value="'.($key+1).'" />';
					print '<br>';
				}
			} else {
				print $langs->trans("NoAttachedFiles").'<br>';
			}
				// Add link to add file
				print '<input type="file" class="flat" name="addedfile'.$i.'" value="'.$langs->trans("Upload").'"/>';
				print ' ';
				print '<input type="submit" class="button" name="addfile'.$i.'" value="'.$langs->trans("MailingAddFile").'">';
				//print $object->$property?'<br>'.$object->$property:'';


				print '</td></tr>';
			//}

			// Background color
			print '<tr><td width="25%">'.$langs->trans("BackgroundColorByDefault").'</td><td colspan="3">';
			print $htmlother->selectColor($object->bgcolor, 'bgcolor', 'edit_mailing', 0);
			print '</td></tr>';

			// Message
			print '<tr><td width="25%" valign="top">'.$langs->trans("MailMessage").'<br>';
			print '<br><i>'.$langs->trans("CommonSubstitutions").':<br>';
			print '__ID__ = '.$langs->trans("IdRecord").'<br>';
			print '__EMAIL__ = '.$langs->trans("EMail").'<br>';
			print '__LASTNAME__ = '.$langs->trans("Lastname").'<br>';
			print '__FIRSTNAME__ = '.$langs->trans("Firstname").'<br>';
			print '__OTHER1__ = '.$langs->trans("Other").'1<br>';
			print '__OTHER2__ = '.$langs->trans("Other").'2<br>';
			print '__OTHER3__ = '.$langs->trans("Other").'3<br>';
			print '__OTHER4__ = '.$langs->trans("Other").'4<br>';
			print '__OTHER5__ = '.$langs->trans("Other").'5<br>';
			print '</i></td>';
			print '<td colspan="3">';
			// Editeur wysiwyg
			require_once DOL_DOCUMENT_ROOT."/core/class/doleditor.class.php";
			$doleditor=new DolEditor('body', $object->body, '', 320, 'dolibarr_mailings', '', true, true, $conf->fckeditor->enabled && $conf->global->FCKEDITOR_ENABLE_MAILING, 20, '90%');
			$doleditor->Create();
			print '</td></tr>';

			print '<tr><td colspan="4" align="center">';
			print '<input type="submit" class="button" value="'.$langs->trans("Save").'" name="save">';
			print ' &nbsp; ';
			print '<input type="submit" class="button" value="'.$langs->trans("Cancel").'" name="cancel">';
			print '</td></tr>';

			print '</table>';

			print '</form>';
			print '<br>';
		}
	} else {
		dol_print_error($db, $object->error);
	}
}

llxFooter();

$db->close();
