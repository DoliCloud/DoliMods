<?php
/* Copyright (C) 2003      Eric Seigne          <erics@rycks.com>
 * Copyright (C) 2003,2005 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2011 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2004      Sebastien Di Cintio  <sdicintio@ressource-toi.org>
 * Copyright (C) 2004      Benoit Mortier       <benoit.mortier@opensides.be>
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
 *      \file       htdocs/submiteverywhere/admin/submiteverywheresetuppage.php
 *      \ingroup    submiteverywhere
 *      \brief      Page to setup module SubmitEverywhere
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
if (! $res && file_exists("../../main.inc.php")) $res=@include "../../main.inc.php";
if (! $res && file_exists("../../../main.inc.php")) $res=@include "../../../main.inc.php";
if (! $res) die("Include of main fails");

require_once DOL_DOCUMENT_ROOT."/core/lib/admin.lib.php";
require_once DOL_DOCUMENT_ROOT."/core/class/html.formadmin.class.php";
dol_include_once("/submiteverywhere/lib/submiteverywhere.lib.php");


$langs->load("admin");
$langs->load("submiteverywhere@submiteverywhere");

if (!$user->admin) accessforbidden();

$mesg='';
$error=0;

$listoftargets=array(
'digg'=>array('label'=>$langs->trans("Digg"),'loginedit'=>1,'passedit'=>1,'titlelength'=>32,'descshortlength'=>256,'desclonglength'=>2000,'url'=>'http://www.digg.com','urledit'=>0),
'facebook'=>array('label'=>$langs->trans("Facebook"),'loginedit'=>1,'passedit'=>1,'titlelength'=>32,'descshortlength'=>256,'desclonglength'=>2000,'url'=>'http://www.facebook.com','urledit'=>0),
'linkedin'=>array('label'=>$langs->trans("LinkedIn"),'loginedit'=>1,'passedit'=>1,'titlelength'=>32,'descshortlength'=>256,'desclonglength'=>2000,'url'=>'http://linkedin.com','urledit'=>0),
'twitter'=>array('label'=>$langs->trans("Twitter"),'loginedit'=>1,'passedit'=>1,'titlelength'=>-1,'descshortlength'=>140,'desclonglength'=>-1,'url'=>'http://twitter.com','urledit'=>0),
'googleplus'=>array('label'=>$langs->trans("GooglePlus"),'loginedit'=>1,'passedit'=>1,'titlelength'=>32,'descshortlength'=>256,'desclonglength'=>2000,'url'=>'http://plus.google.com','urledit'=>0),
'email'=>array('label'=>$langs->trans("Email"),'loginedit'=>0,'passedit'=>0,'titlelength'=>0,'descshortlength'=>-1,'desclonglength'=>0,'url'=>'','urledit'=>1),
'web'=>array('label'=>$langs->trans("GenericWebSite"),'loginedit'=>1,'passedit'=>1,'titlelength'=>32,'descshortlength'=>256,'desclonglength'=>2000,'url'=>'http://','urledit'=>1),
//'sms'=>array('label'=>$langs->trans("Email"),'titlelength'=>10,'descshortlength'=>140,'desclonglength'=>-1),
);

$action=GETPOST('action', 'aZ09');
$id=GETPOST('id', 'int');

$sortfield = GETPOST("sortfield", 'alpha');
$sortorder = GETPOST("sortorder", 'alpha');
$page = GETPOST("page", 'int');
if ($page == -1) { $page = 0; }
$offset = $conf->liste_limit * $page;
$pageprev = $page - 1;
$pagenext = $page + 1;
if (! $sortfield) $sortfield="label";
if (! $sortorder) $sortorder="ASC";

$limit = GETPOST('limit')?GETPOST('limit', 'int'):$conf->liste_limit;



/*
 * Action
 */

if (($action == 'add' || $action == 'update') && ! GETPOST("cancel")) {
	if ($action == 'add') $id='';

	if (! GETPOST('label'.$id)) {
		$error++;
		$errors[]=$langs->trans("ErrorFieldRequired", $langs->transnoentitiesnoconv("Label"));
	}
	if (! GETPOST('type'.$id)) {
		$error++;
		$errors[]=$langs->trans("ErrorFieldRequired", $langs->transnoentitiesnoconv("Type"));
	}
	/*if (GETPOST('titlelength') == '')
	{
		$error++;
		$errors[]=$langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("TitleLength"));
	}
	if (GETPOST('descshortlength') == '')
	{
		$error++;
		$errors[]=$langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("DescShortLength"));
	}
	if (GETPOST('desclonglength') == '')
	{
		$error++;
		$errors[]=$langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("DescLongLength"));
	}*/

	if (! $error) {
		if ($action == 'add') {
			$url=GETPOST('url'.$id);
			$type=GETPOST('type'.$id);
			foreach ($listoftargets as $key => $val) {
				//print $key."-".$type."-".$val['url'];
				if ($key == $type) { $url=$val['url']; break; }
				//print $url;
			}

			$sql = "INSERT INTO ".MAIN_DB_PREFIX."submitew_targets (label,targetcode,langcode,titlelength,descshortlength,desclonglength,login,pass,url)";
			$sql.= " VALUES ('".$db->escape(GETPOST('label'.$id))."', '".$db->escape(GETPOST('type'.$id))."', '".$db->escape(GETPOST('langcode'.$id))."',";
			$sql.= " '".(GETPOST('titlelength'.$id)!=''?GETPOST('titlelength'.$id):-1)."',";
			$sql.= " '".(GETPOST('descshortlength'.$id)!=''?GETPOST('descshortlength'.$id):-1)."',";
			$sql.= " '".(GETPOST('desclonglength'.$id)!=''?GETPOST('desclonglength'.$id):-1)."',";
			$sql.= " ".(GETPOST('login'.$id)!=''?"'".$db->escape(GETPOST('login'.$id))."'":"null").",";
			$sql.= " ".(GETPOST('pass'.$id)!=''?"'".$db->escape(GETPOST('pass'.$id))."'":"null").",";
			$sql.= " ".($url!=''?"'".$db->escape($url)."'":"null");
			$sql.= ")";
			$resql=$db->query($sql);
			if ($resql) {
				//$_POST['type']='';
				$_POST['label']='';
				//$_POST['langcode']='';
				$_POST['login']='';
				$_POST['pass']='';
				$_POST['url']='';
			} else {
				if ($db->lasterrno == 'DB_ERROR_RECORD_ALREADY_EXISTS') {
					$langs->load("errors");
					$errors[]=$langs->trans("ErrorRefAlreadyExists");
				} else {
					dol_print_error($db);
					$error++;
				}
			}
		}
		if ($action == 'update') {
			$sql = "UPDATE ".MAIN_DB_PREFIX."submitew_targets ";
			$sql.= " SET label='".$db->escape(GETPOST('label'.$id))."',";
			$sql.= " targetcode = '".$db->escape(GETPOST('type'.$id))."',";
			$sql.= " langcode = '".$db->escape(GETPOST('langcode'.$id))."',";
			$sql.= " titlelength = '".(GETPOST('titlelength'.$id)!=''?GETPOST('titlelength'.$id):-1)."',";
			$sql.= " descshortlength = '".(GETPOST('descshortlength'.$id)!=''?GETPOST('descshortlength'.$id):-1)."',";
			$sql.= " desclonglength = '".(GETPOST('desclonglength'.$id)!=''?GETPOST('desclonglength'.$id):-1)."',";
			$sql.= " login = ".(GETPOST('login'.$id)!=''?"'".$db->escape(GETPOST('login'.$id))."'":"null").",";
			$sql.= " pass = ".(GETPOST('pass'.$id)!=''?"'".$db->escape(GETPOST('pass'.$id))."'":"null").",";
			$sql.= " url = ".(GETPOST('url'.$id)!=''?"'".$db->escape(GETPOST('url'.$id))."'":"null");
			$sql.= " WHERE rowid = ".$id;
			$resql=$db->query($sql);

			if ($resql) {
				//$_POST['type']='';
				$_POST['label']='';
				//$_POST['langcode']='';
				$_POST['login']='';
				$_POST['pass']='';
				$_POST['url']='';
			} else {
				if ($db->lasterrno == 'DB_ERROR_RECORD_ALREADY_EXISTS') {
					$langs->load("errors");
					$errors[]=$langs->trans("ErrorRefAlreadyExists");
				} else {
					dol_print_error($db);
					$error++;
				}
			}
		}
	}
}

if ($action == 'delete') {
	$sql = "DELETE FROM ".MAIN_DB_PREFIX."submitew_targets";
	$sql.= " WHERE rowid = ".$id;
	$resql=$db->query($sql);
	if ($resql) {
	} else {
		dol_print_error($db);
		$error++;
	}
}


/*
 * View
 */

$htmladmin=new FormAdmin($db);

llxHeader();

$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php?restore_lastsearch_values=1">'.$langs->trans("BackToModuleList").'</a>';
print_fiche_titre($langs->trans("SubmitEveryWhereSetup"), $linkback, 'setup');
print '<br>';

print $langs->trans("DescSubmitEveryWhere").'<br><br>'."\n";


print_fiche_titre($langs->trans("AddTarget"), '', '');

// Form to add entry
print '<form name="externalrssconfig" action="'.$_SERVER["PHP_SELF"].'" method="post">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';

print '<table class="nobordernopadding" width="100%">';
print '<tr class="liste_titre">';
print '<td>'.$langs->trans("Parameters").'</td>';
//print '<td width="160">'.$langs->trans("TargetType").'</td>';
//print '<td align="center">'.$langs->trans("Language").'</td>';
print '<td colspan="3">&nbsp;</td>';
print '</tr>';

$var=false;
print '<tr '.$bc[$var].'>';
// Label
print '<td>';
print $langs->trans("TargetType").':<br>';
print $langs->trans("Label").':<br>';
print $langs->trans("Language").':<br>';
print '</td>';
print '<td align="left">';
//print '</td>';
// Type
//print '<td align="left">';
print '<select class="flat" name="type" id="type">'."\n";
print '<option value="">&nbsp;</option>'."\n";
foreach ($listoftargets as $key => $val) {
	print '<option value="'.$key.'">'.$val['label'].'</option>'."\n";
}
print '</select>';
//print '</td>';
// Language
//print '<td align="center">';
print '<br>';
print '<input type="text" name="label" value="'.($_POST["label"]?$_POST["label"]:'').'" size="24">';
print '<br>';
print $htmladmin->select_language($langs->defaultlang, 'langcode');
print '</td>';
// Title
print '<td>';
print $langs->trans("TitleLength").': <input type="text" autocomplete="off" name="titlelength" id="titlelength" value="" size="4" disabled="disabled">';
print ' &nbsp; ';
print $langs->trans("DescShortLength").': <input type="text" autocomplete="off" name="descshortlength" id="descshortlength" value="" size="4" disabled="disabled">';
print ' &nbsp; ';
print $langs->trans("DescLongLength").': <input type="text" autocomplete="off" name="desclonglength" id="desclonglength" value="" size="4" disabled="disabled">';
print '<br>';
print $langs->trans("Login").': <input type="text" autocomplete="off" name="login" id="login" value="'.$obj->login.'" size="8" disabled="disabled"> &nbsp; ';
print $langs->trans("Password").': <input type="password" autocomplete="off" name="pass" id="pass" value="'.$obj->pass.'" size="8" disabled="disabled">';
print '<br>';
print $langs->trans("UrlOrEMail").': <input type="url" autocomplete="off" name="url" id="url" value="'.$obj->url.'" size="48" disabled="disabled">';
print '</td>';
print '</tr>';

print '<tr><td colspan="6" align="center"><br>';
print '<input type="submit" class="button" value="'.$langs->trans("Add").'">';
print '<input type="hidden" name="action" value="add">';
print '</td>';
print '</tr>';

print '</table>';
print '</form>';


print '<br>';


// Jquery interactions on add form
print '<script type="text/javascript">
jQuery(document).ready(function(){
    jQuery("#type").change(function(){
        if (jQuery("#type").val()==\'\') {
            jQuery("#titlelength").attr("disabled","disabled");
            jQuery("#descshortlength").attr("disabled","disabled");
            jQuery("#desclonglength").attr("disabled","disabled");
            jQuery("#url").attr("disabled","disabled");
            jQuery("#titlelength").val(\'\'); jQuery("#descshortlength").val(\'\'); jQuery("#desclonglength").val(\'\');
        };
    ';
foreach ($listoftargets as $key => $val) {
	print 'if (jQuery("#type").val()==\''.$key.'\') {
        if ('.$val['titlelength'].' > -1)     { jQuery("#titlelength").removeAttr("disabled"); jQuery("#titlelength").val('.$val['titlelength'].'); }
        else { jQuery("#titlelength").attr("disabled","disabled"); jQuery("#titlelength").val(\''.$langs->trans("NA").'\'); }
        if ('.$val['descshortlength'].' > -1) { jQuery("#descshortlength").removeAttr("disabled"); jQuery("#descshortlength").val('.$val['descshortlength'].'); }
        else { jQuery("#descshortlength").attr("disabled","disabled"); jQuery("#descshortlength").val(\''.$langs->trans("NA").'\'); }
        if ('.$val['desclonglength'].' > -1)  { jQuery("#desclonglength").removeAttr("disabled"); jQuery("#desclonglength").val('.$val['desclonglength'].'); }
        else { jQuery("#desclonglength").attr("disabled","disabled"); jQuery("#desclonglength").val(\''.$langs->trans("NA").'\'); }
        if ('.$val['urledit'].' > 0) { jQuery("#url").removeAttr("disabled"); jQuery("#url").val(\''.dol_escape_js($val['url']).'\'); }
        else {  jQuery("#url").attr("disabled","disabled"); jQuery("#url").val(\''.dol_escape_js($val['url']).'\'); }
        if ('.$val['loginedit'].' > 0) { jQuery("#login").removeAttr("disabled"); jQuery("#login").val(\''.dol_escape_js($val['login']).'\'); }
        else {  jQuery("#login").attr("disabled","disabled"); jQuery("#login").val(\''.dol_escape_js($val['login']).'\'); }
        if ('.$val['passedit'].' > 0) { jQuery("#pass").removeAttr("disabled"); jQuery("#pass").val(\''.dol_escape_js($val['pass']).'\'); }
        else {  jQuery("#pass").attr("disabled","disabled"); jQuery("#pass").val(\''.dol_escape_js($val['pass']).'\'); }
	} '."\n";
}
print '
    });
});
</script>
';


dol_htmloutput_mesg('', $errors, 'error');



print_fiche_titre($langs->trans("ListOfAvailableTargets"), '', '');

print '<table class="nobordernopadding" width="100%">';

$sql ="SELECT rowid, label, targetcode, langcode, url, login, pass, comment, position, titlelength, descshortlength, desclonglength";
$sql.=" FROM ".MAIN_DB_PREFIX."submitew_targets";
$sql.=$db->order($sortfield, $sortorder);

dol_syslog("Get list of targets sql=".$sql, LOG_DEBUG);
$resql=$db->query($sql);
if ($resql) {
	$param='';
	$num =$db->num_rows($resql);
	$i=0;

	print '<tr class="liste_titre">';
	print_liste_field_titre($langs->trans("Label"), $_SERVER["PHP_SELF"], "label", $param, '', 'width="180"', $sortfield, $sortorder);
	print_liste_field_titre($langs->trans("TargetType"), $_SERVER["PHP_SELF"], "targetcode", $param, '', 'width="160"', $sortfield, $sortorder);
	print_liste_field_titre($langs->trans("Language"), $_SERVER["PHP_SELF"], "langcode", $param, '', 'align="center"', $sortfield, $sortorder);
	print '<td colspan="2">'.$langs->trans("Parameters").'</td>';
	print '</tr>';

	while ($i < $num) {
		$obj = $db->fetch_object($resql);

		if ($action == 'edit' && $id == $obj->rowid) {
			print "<form name=\"updatetarget".$i."\" action=\"".$_SERVER["PHP_SELF"]."\" method=\"post\">";
			print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
			print '<input type="hidden" name="id" value="'.$obj->rowid.'">';
			print '<input type="hidden" name="sortfield" value="'.$sortfield.'">';
			print '<input type="hidden" name="sortorder" value="'.$sortorder.'">';
			print '<input type="hidden" name="page" value="'.$page.'">';
			print '<input type="hidden" name="action" value="update">';
		}

		$var=!$var;
		print "<tr ".$bc[$var].">";
		print '<td>';
		if ($action == 'edit' && $id == $obj->rowid) {
			print '<input type="text" name="label'.$obj->rowid.'" value="'.$obj->label.'" size="12">';
		} else {
			print $obj->label;
		}
		print '</td>';

		// Picto of target
		print '<td>';
		$s=picto_from_targetcode($obj->targetcode);
		print $s.' '.$obj->targetcode;
		if ($action == 'edit' && $id == $obj->rowid) {
			print '<input type="hidden" name="type'.$obj->rowid.'" value="'.$obj->targetcode.'">';
		}
		print '</td>';

		// Edit
		print '<td align="center">';
		if ($action == 'edit' && $id == $obj->rowid) {
			print $htmladmin->select_language($obj->langcode, 'langcode'.$obj->rowid);
		} else {
			$s=picto_from_langcode($obj->langcode);
			print $s;
		}
		print '</td>';

		// Params
		print '<td>';
		if ($action == 'edit' && $id == $obj->rowid) {
			print $langs->trans("TitleLength").': <input type="text" autocomplete="off" name="titlelength" id="titlelength'.$obj->rowid.'" value="'.($obj->titlelength>=0?$obj->titlelength:'NA').'" size="4">';
			print ' - ';
			print $langs->trans("DescShortLength").': <input type="text" autocomplete="off" name="descshortlength" id="descshortlength'.$obj->rowid.'" value="'.($obj->descshortlength>=0?$obj->descshortlength:'NA').'" size="4">';
			print ' - ';
			print $langs->trans("DescLongLength").': <input type="text" autocomplete="off" autocomplete="off" name="desclonglength" id="desclonglength'.$obj->rowid.'" value="'.($obj->desclonglength>=0?$obj->desclonglength:'NA').'" size="4">';
			if (in_array($obj->targetcode, array('web','dig','facebook','linkedin','twitter'))) {
				print '<br>';
				print $langs->trans("Login").': <input type="text" autocomplete="off" name="login'.$obj->rowid.'" value="'.$obj->login.'" size="8"> - ';
				print $langs->trans("Password").': <input type="password" autocomplete="off" name="pass'.$obj->rowid.'" value="'.$obj->pass.'" size="8">';
			}
			if ($listoftargets[$obj->targetcode]['urledit']) {
				print '<br>';
				print $langs->trans("UrlOrEMail").': <input type="text" autocomplete="off" name="url'.$obj->rowid.'" value="'.$obj->url.'" size="48">';
			} else {
				print '<br>';
				print $langs->trans("UrlOrEMail").': <input type="text" autocomplete="off" disabled="disabled" name="url'.$obj->rowid.'" value="'.$obj->url.'" size="48">';
			}
		} else {
			print $langs->trans("TitleLength").': '.($obj->titlelength>=0?$obj->titlelength:'NA');
			print ' - ';
			print $langs->trans("DescShortLength").': '.($obj->descshortlength>=0?$obj->descshortlength:'NA');
			print ' - ';
			print $langs->trans("DescLongLength").': '.($obj->desclonglength>=0?$obj->desclonglength:'NA');
			if (in_array($obj->targetcode, array('web','dig','facebook','linkedin','twitter'))) {
				print '<br>';
				print $langs->trans("Login").': '.$obj->login.' - ';
				print $langs->trans("Password").': '.$obj->pass.'';
			}
			//if (in_array($obj->targetcode,array('web')))
			if ($obj->url) {
				print '<br>';
				print $langs->trans("UrlOrEMail").': '.dol_print_url($obj->url);
			}
		}
		print '</td>';
		print '<td class="nowrap" align="right">';
		if ($action == 'edit' && $id == $obj->rowid) {
			print '<input type="submit" name="submit" value="'.$langs->trans("Save").'" class="button"><br>';
			print '<input type="submit" name="cancel" value="'.$langs->trans("Cancel").'" class="button">';
		} else {
			print '<a href="'.$_SERVER["PHP_SELF"].'?action=edit&amp;token='.newToken().'&amp;id='.$obj->rowid.'&amp;sortfield='.$sortfield.'&amp;sortorder='.$sortorder.'">'.img_picto($langs->trans("Edit"), 'edit').'</a> &nbsp; ';
			print '<a href="'.$_SERVER["PHP_SELF"].'?action=delete&amp;token='.newToken().'&amp;id='.$obj->rowid.'&amp;sortfield='.$sortfield.'&amp;sortorder='.$sortorder.'">'.img_picto($langs->trans("Delete"), 'delete').'</a>';
		}
		print "</tr>";

		if ($action == 'edit' && $id == $obj->rowid) {
			print "</form>";
		}

		$i++;
	}
} else {
	dol_print_error($db);
}

print '</table>'."\n";


$db->close();

llxFooter();
