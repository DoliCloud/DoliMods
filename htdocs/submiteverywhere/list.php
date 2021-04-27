<?php
/* Copyright (C) 2005-2012 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2010 Regis Houssin        <regis@dolibarr.fr>
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
 *       \file       htdocs/comm/mailing/list.php
 *       \ingroup    mailing
 *       \brief      Liste des mailings
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

require_once DOL_DOCUMENT_ROOT."/comm/mailing/class/mailing.class.php";

$langs->load("mails");
$langs->load("submiteverywhere@submiteverywhere");

//if (!$user->rights->mailing->lire) accessforbidden();

// Securite acces client
if ($user->societe_id > 0) {
	$action = '';
	$socid = $user->societe_id;
}

$sortfield = GETPOST("sortfield", 'alpha');
$sortorder = GETPOST("sortorder", 'alpha');
$page = GETPOST("page", 'int');
if ($page == -1) { $page = 0; }
$offset = $conf->liste_limit * $page;
$pageprev = $page - 1;
$pagenext = $page + 1;
if (! $sortorder) $sortorder="DESC";
if (! $sortfield) $sortfield="m.date_creat";

$sall=isset($_GET["sall"])?$_GET["sall"]:$_POST["sall"];
$sref=isset($_GET["sref"])?$_GET["sref"]:$_POST["sref"];

$filteremail=$_REQUEST["filteremail"]?$_REQUEST["filteremail"]:'';



/*
 * View
 */

$help_url='EN:Module_SubmitEveryWhere|FR:Module_SubmitEveryWhere_Fr|ES:M&oacute;dulo_SubmitEveryWhere';
llxHeader('', 'SubmitEveryWhere', $help_url);

$html = new Form($db);

if ($filteremail) {
	$sql = "SELECT m.rowid, m.title, m.nbemail, m.statut, m.date_creat as datec, m.date_envoi as date_envoi,";
	$sql.= " mc.statut as sendstatut";
	$sql.= " FROM ".MAIN_DB_PREFIX."submitew_message as m, ".MAIN_DB_PREFIX."submitew_targets as mc";
	$sql.= " WHERE m.rowid = mc.fk_mailing AND m.entity = ".$conf->entity;
	$sql.= " AND mc.email = '".$db->escape($filteremail)."'";
	if ($sref) $sql.= " AND m.rowid = '".$sref."'";
	if ($sall) $sql.= " AND (m.titre like '%".$sall."%' OR m.sujet like '%".$sall."%' OR m.body like '%".$sall."%')";
	if (! $sortorder) $sortorder="ASC";
	if (! $sortfield) $sortfield="m.rowid";
	$sql.= $db->order($sortfield, $sortorder);
	$sql.= $db->plimit($conf->liste_limit +1, $offset);
} else {
	$sql = "SELECT m.rowid, m.title, m.nbemail, m.statut, m.date_creat as datec, m.date_envoi as date_envoi";
	$sql.= " FROM ".MAIN_DB_PREFIX."submitew_message as m";
	$sql.= " WHERE m.entity = ".$conf->entity;
	if ($sref) $sql.= " AND m.rowid = '".$sref."'";
	if ($sall) $sql.= " AND (m.titre like '%".$sall."%' OR m.sujet like '%".$sall."%' OR m.body like '%".$sall."%')";
	if (! $sortorder) $sortorder="ASC";
	if (! $sortfield) $sortfield="m.rowid";
	$sql.= $db->order($sortfield, $sortorder);
	$sql.= $db->plimit($conf->liste_limit +1, $offset);
}

dol_syslog("sql=".$sql);
//print $sql;
$result = $db->query($sql);
if ($result) {
	$num = $db->num_rows($result);

	$title=$langs->trans("ListOfMessage");
	if ($filteremail) $title.=' ('.$langs->trans("SentTo", $filteremail).')';
	print_barre_liste($title, $page, $_SERVER["PHP_SELF"], "", $sortfield, $sortorder, "", $num);

	$i = 0;

	$param = "&amp;sall=".$sall;
	if ($filteremail) $param.='&amp;filteremail='.urlencode($filteremail);

	print '<table class="liste">';
	print '<tr class="liste_titre">';
	print_liste_field_titre($langs->trans("Ref"), $_SERVER["PHP_SELF"], "m.rowid", $param, "", "", $sortfield, $sortorder);
	print_liste_field_titre($langs->trans("Title"), $_SERVER["PHP_SELF"], "m.titre", $param, "", "", $sortfield, $sortorder);
	print_liste_field_titre($langs->trans("DateCreation"), $_SERVER["PHP_SELF"], "m.date_creat", $param, "", 'align="center"', $sortfield, $sortorder);
	if (! $filteremail) print_liste_field_titre($langs->trans("NbOfTargets"), $_SERVER["PHP_SELF"], "m.nbemail", $param, "", 'align="center"', $sortfield, $sortorder);
	if (! $filteremail) print_liste_field_titre($langs->trans("DateLastSend"), $_SERVER["PHP_SELF"], "m.date_envoi", $param, "", 'align="center"', $sortfield, $sortorder);
	else print_liste_field_titre($langs->trans("DateSending"), $_SERVER["PHP_SELF"], "mc.date_envoi", $param, "", 'align="center"', $sortfield, $sortorder);
	print_liste_field_titre($langs->trans("Status"), $_SERVER["PHP_SELF"], ($filteremail?"mc.statut":"m.statut"), $param, "", 'align="right"', $sortfield, $sortorder);
	print "</tr>\n";

	print '<form method="GET" action="'.$_SERVER["PHP_SELF"].'">';
	print '<tr class="liste_titre">';
	print '<td class="liste_titre">';
	print '<input type="text" class="flat" name="sref" value="'.$sref.'" size="6">';
	print '</td>';
	// Title
	print '<td class="liste_titre">';
	print '<input type="text" class="flat" name="sall" value="'.$sall.'" size="40">';
	print '</td>';
	print '<td class="liste_titre">&nbsp;</td>';
	if (! $filteremail) print '<td class="liste_titre">&nbsp;</td>';
	print '<td class="liste_titre">&nbsp;</td>';
	print '<td class="liste_titre" align="right"><input class="liste_titre" type="image" src="'.DOL_URL_ROOT.'/theme/'.$conf->theme.'/img/search.png" value="'.dol_escape_htmltag($langs->trans("Search")).'" title="'.dol_escape_htmltag($langs->trans("Search")).'">';
	print "</td>";
	print "</tr>\n";
	print '</form>';

	$var=true;

	$email=new Mailing($db);

	while ($i < min($num, $conf->liste_limit)) {
		$obj = $db->fetch_object($result);

		$var=!$var;

		print "<tr $bc[$var]>";
		print '<td><a href="'.DOL_URL_ROOT.'/comm/mailing/card.php?id='.$obj->rowid.'">';
		print img_object($langs->trans("ShowEMail"), "email").' '.stripslashes($obj->rowid).'</a></td>';
		print '<td>'.$obj->titre.'</td>';
		// Date creation
		print '<td align="center">';
		print dol_print_date($db->jdate($obj->datec), 'day');
		print '</td>';
		// Nb of email
		if (! $filteremail) {
			print '<td align="center">';
			$nbemail = $obj->nbemail;
			if (!empty($conf->global->MAILING_LIMIT_SENDBYWEB) && $conf->global->MAILING_LIMIT_SENDBYWEB < $nbemail) {
				$text=$langs->trans('LimitSendingEmailing', $conf->global->MAILING_LIMIT_SENDBYWEB);
				print $html->textwithpicto($nbemail, $text, 1, 'warning');
			} else {
				print $nbemail;
			}
			print '</td>';
		}
		// Last send
		print '<td align="center" nowrap="nowrap">'.dol_print_date($db->jdate($obj->date_envoi), 'day').'</td>';
		print '</td>';
		// Status
		print '<td align="right" nowrap="nowrap">';
		if ($filteremail) {
			if ($obj->sendstatut==-1) print $langs->trans("MailingStatusError").' '.img_error();
			if ($obj->sendstatut==1) print $langs->trans("MailingStatusSent").' '.img_picto($langs->trans("MailingStatusSent"), 'statut6');
		} else {
			print $email->LibStatut($obj->statut, 5);
		}
		print '</td>';
		print "</tr>\n";
		$i++;
	}
	print "</table>";
	$db->free($result);
} else {
	dol_print_error($db);
}


llxFooter();

$db->close();
