<?php
/* Copyright (C) 2005      Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2005-2012 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2010      Regis Houssin        <regis@dolibarr.fr>
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
 *       \file       htdocs/comm/mailing/index.php
 *       \ingroup    mailing
 *       \brief      Page accueil de la zone mailing
 *       \version    $Id: index.php,v 1.1 2011/06/20 22:08:22 eldy Exp $
 */

// Load Dolibarr environment
$res=0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (! $res && ! empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) $res=@include str_replace("..", "", $_SERVER["CONTEXT_DOCUMENT_ROOT"])."/main.inc.php";
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

require_once DOL_DOCUMENT_ROOT ."/comm/mailing/class/mailing.class.php";
require_once DOL_DOCUMENT_ROOT."/core/lib/functions2.lib.php";

$langs->load("mails");
$langs->load("commercial");
$langs->load("orders");
$langs->load("submiteverywhere@submiteverywhere");

//if (! $user->rights->mailing->lire || $user->societe_id > 0) accessforbidden();


/*
 *	View
*/

$help_url='EN:Module_SubmitEveryWhere|FR:Module_SubmitEveryWhere_Fr|ES:M&oacute;dulo_SubmitEveryWhere';
llxHeader('', 'SubmitEveryWhere', $help_url);

print_fiche_titre($langs->trans("SubmitEveryWhereArea"));

print '<table class="notopnoleftnoright" width="100%">';

print '<tr><td valign="top" width="30%" class="notopnoleft">';


// Search message
$var=false;
print '<form method="post" action="'.dol_buildpath('/submiteverywhere/index.php', 1).'">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<table class="noborder" width="100%">';
print '<tr class="liste_titre"><td colspan="3">'.$langs->trans("SearchAMessage").'</td></tr>';
print '<tr '.$bc[$var].'>7.2';
print $langs->trans("Ref").':</td><td><input type="text" class="flat" name="sref" size="18"></td>';
print '<td rowspan="2"><input type="submit" value="'.$langs->trans("Search").'" class="button"></td></tr>';
print '<tr '.$bc[$var].'>7.2';
print $langs->trans("Other").':</td><td><input type="text" class="flat" name="sall" size="18"></td>';

print "</table></form><br>\n";


// Show statistics for submits
/*
 print '<table class="noborder" width="100%">';
print '<tr class="liste_titre"><td colspan="3">'.$langs->trans("TargetsStatistics").'</td></tr>';

print "</table><br>";
*/

print '</td><td valign="top" width="70%" class="notopnoleftnoright">';

/*
 * List of last submit
*/
$limit=10;
$sql  = "SELECT m.rowid, m.title, m.nbemail, m.statut, m.date_creat";
$sql.= " FROM ".MAIN_DB_PREFIX."submitew_message as m";
$sql.= " ORDER BY m.date_creat DESC";
$sql.= " LIMIT ".$limit;
$result=$db->query($sql);
if ($result) {
	print '<table class="noborder" width="100%">';
	print '<tr class="liste_titre">';
	print '<td colspan="2">'.$langs->trans("LastSubmits", $limit).'</td>';
	print '<td align="center">'.$langs->trans("DateCreation").'</td>';
	print '<td align="center">'.$langs->trans("NbOfTargets").'</td>';
	print '<td align="right"><a href="'.dol_buildpath('/submiteverywhere/list.php', 1).'">'.$langs->trans("Status").'</a></td>';
	print '</tr>';

	$num = $db->num_rows($result);
	if ($num > 0) {
		$var = true;
		$i = 0;

		while ($i < $num ) {
			$obj = $db->fetch_object($result);
			$var=!$var;

			print "<tr ".$bc[$var].">";
			print '<td class="nowrap"><a href="'.$_SERVER["PHP_SELF"].'?id='.$obj->rowid.'">'.img_object($langs->trans("ShowEMail"), "email").' '.$obj->rowid.'</a></td>';
			print '<td>'.dol_trunc($obj->titre, 38).'</td>';
			print '<td align="center">'.dol_print_date($obj->date_creat, 'day').'</td>';
			print '<td align="center">'.($obj->nbemail?$obj->nbemail:"0").'</td>';
			$mailstatic=new Mailing($db);
			print '<td align="right">'.$mailstatic->LibStatut($obj->statut, 5).'</td>';
			print '</tr>';
			$i++;
		}
	} else {
		$var = false;
		print '<tr '.$bc[$var].'><td colspan="5" class="opacitymedium">'.$langs->trans("None").'</td></tr>';
	}
	print "</table><br>";
	$db->free($result);
} else {
	dol_print_error($db);
}



print '</td></tr>';
print '</table>';

llxFooter();

$db->close();
